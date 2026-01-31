<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Produto;
use App\Models\ProdutoTamanho;
use App\Models\Sabor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedidoOnlineAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Pedido::online()
            ->with(['cliente', 'clienteEndereco', 'itens.produto', 'itens.produtoTamanho', 'itens.sabores.sabor']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->tipo_pedido) {
            $query->where('tipo_pedido', $request->tipo_pedido);
        }

        if ($request->data_inicio) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }

        if ($request->data_fim) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }

        $pedidos = $query->latest()->paginate(20);

        $statusCounts = [
            'aberto' => Pedido::online()->where('status', 'aberto')->count(),
            'em_preparo' => Pedido::online()->where('status', 'em_preparo')->count(),
            'pronto' => Pedido::online()->where('status', 'pronto')->count(),
            'entregue' => Pedido::online()->where('status', 'entregue')->count(),
        ];

        return view('admin.pedidos-online.index', compact('pedidos', 'statusCounts'));
    }

    public function show(Pedido $pedido)
    {
        if (!$pedido->isOnline()) {
            abort(404);
        }

        $pedido->load(['cliente', 'clienteEndereco', 'itens.produto', 'itens.produtoTamanho', 'itens.sabores.sabor']);

        return view('admin.pedidos-online.show', compact('pedido'));
    }

    public function atualizarStatus(Request $request, Pedido $pedido)
    {
        $request->validate([
            'status' => 'required|in:aberto,em_preparo,pronto,saiu_entrega,entregue,cancelado',
        ]);

        if ($pedido->isFinalizado() || $pedido->isCancelado()) {
            return back()->with('error', 'Não é possível alterar o status de um pedido finalizado ou cancelado.');
        }

        $pedido->update(['status' => $request->status]);

        if (in_array($request->status, ['entregue', 'cancelado'])) {
            $pedido->update(['data_finalizacao' => now()]);
        }

        return back()->with('success', 'Status atualizado com sucesso!');
    }

    public function atualizar(Request $request, Pedido $pedido)
    {
        $request->validate([
            'observacoes' => 'nullable|string',
            'taxa_entrega' => 'nullable|numeric|min:0',
            'previsao_entrega' => 'nullable|date',
        ]);

        if ($pedido->isFinalizado() || $pedido->isCancelado()) {
            return back()->with('error', 'Não é possível editar um pedido finalizado ou cancelado.');
        }

        $pedido->update($request->only(['observacoes', 'taxa_entrega', 'previsao_entrega']));

        return back()->with('success', 'Pedido atualizado com sucesso!');
    }

    public function adicionarItem(Request $request, Pedido $pedido)
    {
        $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'produto_tamanho_id' => 'nullable|exists:produto_tamanhos,id',
            'quantidade' => 'required|integer|min:1',
            'sabores' => 'nullable|array',
            'sabores.*' => 'exists:sabores,id',
            'observacoes' => 'nullable|string|max:255',
        ]);

        if ($pedido->isFinalizado() || $pedido->isCancelado()) {
            return back()->with('error', 'Não é possível adicionar itens a um pedido finalizado ou cancelado.');
        }

        $produto = Produto::findOrFail($request->produto_id);
        $precoUnitario = $produto->preco;

        if ($request->produto_tamanho_id) {
            $tamanho = ProdutoTamanho::findOrFail($request->produto_tamanho_id);
            $precoUnitario = $tamanho->preco;

            if ($request->sabores) {
                $precoMaiorSabor = 0;
                foreach ($request->sabores as $saborId) {
                    $sabor = Sabor::find($saborId);
                    if ($sabor) {
                        $precoSabor = match($tamanho->nome) {
                            'P' => $sabor->preco_p,
                            'M' => $sabor->preco_m,
                            'G' => $sabor->preco_g,
                            'GG' => $sabor->preco_gg,
                            default => 0,
                        };
                        $precoMaiorSabor = max($precoMaiorSabor, $precoSabor);
                    }
                }
                $precoUnitario = $precoMaiorSabor;
            }
        }

        DB::beginTransaction();

        try {
            $item = PedidoItem::create([
                'pedido_id' => $pedido->id,
                'produto_id' => $produto->id,
                'produto_nome' => $produto->nome,
                'produto_tamanho_id' => $request->produto_tamanho_id,
                'quantidade' => $request->quantidade,
                'preco_unitario' => $precoUnitario,
                'subtotal' => $precoUnitario * $request->quantidade,
                'observacoes' => $request->observacoes,
                'status' => 'pendente',
            ]);

            $pedido->calcularTotal();

            DB::commit();

            return back()->with('success', 'Item adicionado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao adicionar item.');
        }
    }

    public function atualizarItem(Request $request, PedidoItem $item)
    {
        $request->validate([
            'quantidade' => 'required|integer|min:1',
            'observacoes' => 'nullable|string|max:255',
        ]);

        if ($item->pedido->isFinalizado() || $item->pedido->isCancelado()) {
            return back()->with('error', 'Não é possível editar itens de um pedido finalizado ou cancelado.');
        }

        $item->update([
            'quantidade' => $request->quantidade,
            'subtotal' => $item->preco_unitario * $request->quantidade,
            'observacoes' => $request->observacoes,
        ]);

        $item->pedido->calcularTotal();

        return back()->with('success', 'Item atualizado com sucesso!');
    }

    public function removerItem(PedidoItem $item)
    {
        if ($item->pedido->isFinalizado() || $item->pedido->isCancelado()) {
            return back()->with('error', 'Não é possível remover itens de um pedido finalizado ou cancelado.');
        }

        $pedido = $item->pedido;

        if ($pedido->itens()->count() === 1) {
            return back()->with('error', 'Não é possível remover o último item. Cancele o pedido inteiro.');
        }

        $item->delete();
        $pedido->calcularTotal();

        return back()->with('success', 'Item removido com sucesso!');
    }
}
