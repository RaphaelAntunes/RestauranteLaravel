<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Mesa;
use App\Models\Produto;
use App\Models\PedidoItem;
use App\Models\ProdutoTamanho;
use App\Models\Sabor;
use App\Models\PedidoItemSabor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    public function index(Request $request)
    {
        $query = Pedido::with(['mesa', 'user']);

        // Filtro por data inicial
        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }

        // Filtro por data final
        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }

        // Filtro por status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pedidos = $query->latest()->paginate(20)->withQueryString();

        return view('pedidos.index', compact('pedidos'));
    }

    public function create()
    {
        $mesas = Mesa::where('status', 'disponivel')->where('ativo', true)->orderBy('numero')->get();
        $produtosTemp = Produto::with(['categoria', 'tamanhos' => function($query) {
                $query->where('ativo', true)->orderBy('ordem');
            }])
            ->where('ativo', true)
            ->orderBy('nome')
            ->get()
            ->groupBy('categoria.nome');

        // Ordenar categorias manualmente na ordem desejada
        $ordemCategorias = ['Pizzas', 'Pizzas Doces',  'Bebidas', 'Bordas Recheadas'];
        $produtos = collect();

        foreach ($ordemCategorias as $categoria) {
            if (isset($produtosTemp[$categoria])) {
                $produtos[$categoria] = $produtosTemp[$categoria];
            }
        }

        // Adicionar categorias restantes que não estão na lista
        foreach ($produtosTemp as $categoria => $items) {
            if (!in_array($categoria, $ordemCategorias)) {
                $produtos[$categoria] = $items;
            }
        }

        $sabores = Sabor::with('categoria')
            ->where('ativo', true)
            ->orderBy('categoria_id')
            ->orderBy('ordem')
            ->get()
            ->groupBy('categoria.nome');

        return view('pedidos.create', compact('mesas', 'produtos', 'sabores'));
    }

    public function store(Request $request)
    {
        // Verificar permissão do usuário para lançar pedidos
        if (!auth()->user()->podeLancarPedidos()) {
            return back()->with('error', 'Você não tem permissão para lançar pedidos.');
        }

        $validated = $request->validate([
            'mesa_id' => 'required|exists:mesas,id',
            'observacoes' => 'nullable|string',
            'itens' => 'required|array|min:1',
            'itens.*.produto_id' => 'required|exists:produtos,id',
            'itens.*.quantidade' => 'required|integer|min:1',
            'itens.*.observacoes' => 'nullable|string',
            'itens.*.tamanho_id' => 'nullable|exists:produto_tamanhos,id',
            'itens.*.sabores' => 'nullable|array',
            'itens.*.sabores.*' => 'exists:sabores,id',
        ], [
            'mesa_id.required' => 'Selecione uma mesa',
            'itens.required' => 'Adicione pelo menos um item ao pedido',
        ]);

        DB::beginTransaction();
        try {
            $mesa = Mesa::find($validated['mesa_id']);

            // Gerar ou reutilizar sessão da mesa
            if (!$mesa->sessao_atual || $mesa->status == 'disponivel') {
                // Se não tem sessão ou mesa está disponível, gerar nova sessão
                $novaSessao = uniqid('sessao_', true);
                $mesa->update(['sessao_atual' => $novaSessao]);
            }

            // Gerar número do pedido
            $numeroPedido = 'PED-' . date('Ymd') . '-' . str_pad(Pedido::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            // Criar pedido com sessao_id
            $pedido = Pedido::create([
                'mesa_id' => $validated['mesa_id'],
                'sessao_id' => $mesa->sessao_atual,
                'user_id' => auth()->id(),
                'numero_pedido' => $numeroPedido,
                'status' => 'aberto',
                'observacoes' => $validated['observacoes'] ?? null,
                'total' => 0,
            ]);

            $total = 0;

            // Adicionar itens
            foreach ($validated['itens'] as $item) {
                $produto = Produto::findOrFail($item['produto_id']);

                // Se tem tamanho (pizza), usar preço do tamanho
                $precoUnitario = $produto->preco;
                $tamanhoId = null;
                $tamanhoNome = null;

                if (isset($item['tamanho_id'])) {
                    $tamanho = ProdutoTamanho::findOrFail($item['tamanho_id']);
                    $precoUnitario = $tamanho->preco;
                    $tamanhoId = $tamanho->id;
                    $tamanhoNome = strtolower($tamanho->nome); // 'p', 'm', 'g', 'gg'

                    // Se tem sabores selecionados, calcular o preço baseado no sabor mais caro
                    if (isset($item['sabores']) && is_array($item['sabores']) && count($item['sabores']) > 0) {
                        $sabores = Sabor::whereIn('id', $item['sabores'])->get();
                        $maiorPreco = 0;

                        foreach ($sabores as $sabor) {
                            $campoPreco = 'preco_' . $tamanhoNome;
                            $precoSabor = $sabor->$campoPreco ?? 0;

                            if ($precoSabor > $maiorPreco) {
                                $maiorPreco = $precoSabor;
                            }
                        }

                        // Se encontrou preço nos sabores, usar o maior preço
                        if ($maiorPreco > 0) {
                            $precoUnitario = $maiorPreco;
                        }
                    }
                }

                $subtotal = $precoUnitario * $item['quantidade'];
                $total += $subtotal;

                $pedidoItem = PedidoItem::create([
                    'pedido_id' => $pedido->id,
                    'produto_id' => $produto->id,
                    'produto_nome' => $produto->nome,
                    'produto_tamanho_id' => $tamanhoId,
                    'quantidade' => $item['quantidade'],
                    'preco_unitario' => $precoUnitario,
                    'subtotal' => $subtotal,
                    'observacoes' => $item['observacoes'] ?? null,
                ]);

                // Adicionar sabores se for pizza
                if (isset($item['sabores']) && is_array($item['sabores'])) {
                    foreach ($item['sabores'] as $saborId) {
                        PedidoItemSabor::create([
                            'pedido_item_id' => $pedidoItem->id,
                            'sabor_id' => $saborId,
                        ]);
                    }
                }
            }

            // Atualizar total do pedido
            $pedido->update(['total' => $total]);

            // Atualizar status da mesa
            Mesa::find($validated['mesa_id'])->update(['status' => 'ocupada']);

            DB::commit();

            // Verificar de onde veio a requisição
            if (request('source') === 'garcom') {
                return redirect()->route('garcom.index')
                    ->with('success', 'Pedido enviado com sucesso!');
            }

            return redirect()->route('pedidos.show', $pedido)
                ->with('success', 'Pedido criado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao criar pedido: ' . $e->getMessage());
        }
    }

    public function show(Pedido $pedido)
    {
        $pedido->load(['mesa', 'user', 'itens.produto.categoria']);
        return view('pedidos.show', compact('pedido'));
    }

    public function edit(Pedido $pedido)
    {
        if (!in_array($pedido->status, ['aberto', 'em_preparo'])) {
            return back()->with('error', 'Não é possível editar este pedido.');
        }

        $pedido->load('itens.produto');
        $produtos = Produto::with('categoria')
            ->where('ativo', true)
            ->orderBy('nome')
            ->get()
            ->groupBy('categoria.nome');

        return view('pedidos.edit', compact('pedido', 'produtos'));
    }

    public function update(Request $request, Pedido $pedido)
    {
        // Verificar permissão do usuário para lançar pedidos
        if (!auth()->user()->podeLancarPedidos()) {
            return back()->with('error', 'Você não tem permissão para editar pedidos.');
        }

        if (!in_array($pedido->status, ['aberto', 'em_preparo'])) {
            return back()->with('error', 'Não é possível editar este pedido.');
        }

        $validated = $request->validate([
            'observacoes' => 'nullable|string',
            'itens' => 'required|array|min:1',
            'itens.*.produto_id' => 'required|exists:produtos,id',
            'itens.*.quantidade' => 'required|integer|min:1',
            'itens.*.observacoes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Remover itens antigos
            $pedido->itens()->delete();

            $total = 0;

            // Adicionar novos itens
            foreach ($validated['itens'] as $item) {
                $produto = Produto::findOrFail($item['produto_id']);
                $subtotal = $produto->preco * $item['quantidade'];
                $total += $subtotal;

                PedidoItem::create([
                    'pedido_id' => $pedido->id,
                    'produto_id' => $produto->id,
                    'quantidade' => $item['quantidade'],
                    'preco_unitario' => $produto->preco,
                    'subtotal' => $subtotal,
                    'observacoes' => $item['observacoes'] ?? null,
                ]);
            }

            // Atualizar pedido
            $pedido->update([
                'total' => $total,
                'observacoes' => $validated['observacoes'],
            ]);

            DB::commit();

            return redirect()->route('pedidos.show', $pedido)
                ->with('success', 'Pedido atualizado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao atualizar pedido: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, Pedido $pedido)
    {
        $validated = $request->validate([
            'status' => 'required|in:aberto,em_preparo,pronto,entregue,finalizado,cancelado',
        ]);

        $pedido->update(['status' => $validated['status']]);

        if ($validated['status'] == 'finalizado') {
            $pedido->update(['data_finalizacao' => now()]);
        }

        return back()->with('success', 'Status atualizado com sucesso!');
    }

    public function destroy(Pedido $pedido)
    {
        if ($pedido->status != 'aberto') {
            return back()->with('error', 'Apenas pedidos em aberto podem ser excluídos.');
        }

        DB::beginTransaction();
        try {
            // Deletar itens
            $pedido->itens()->delete();

            // Atualizar mesa se não houver outros pedidos ativos
            $pedidosAtivos = Pedido::where('mesa_id', $pedido->mesa_id)
                ->where('id', '!=', $pedido->id)
                ->whereIn('status', ['aberto', 'em_preparo', 'pronto'])
                ->count();

            if ($pedidosAtivos == 0) {
                Mesa::find($pedido->mesa_id)->update(['status' => 'disponivel']);
            }

            // Deletar pedido
            $pedido->delete();

            DB::commit();

            return redirect()->route('pedidos.index')
                ->with('success', 'Pedido excluído com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao excluir pedido: ' . $e->getMessage());
        }
    }
}
