<?php

namespace App\Http\Controllers;

use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Produto;
use App\Models\Sabor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GarcomController extends Controller
{
    /**
     * Lista todas as mesas para o garçom
     */
    public function index()
    {
        $mesas = Mesa::with('pedidoOnline.cliente')
            ->orderByRaw("FIELD(tipo, 'delivery', 'retirada', 'normal')")
            ->orderBy('numero')
            ->get();

        // Calcular pedidos_count manualmente considerando a sessão atual
        $mesas->each(function($mesa) {
            $query = $mesa->pedidos()
                ->whereIn('status', ['aberto', 'em_preparo', 'pronto', 'saiu_entrega']);

            // Filtrar pela sessão atual da mesa
            if ($mesa->sessao_atual) {
                $query->where('sessao_id', $mesa->sessao_atual);
            } else {
                // Se não tem sessão, só contar pedidos sem sessão
                $query->whereNull('sessao_id');
            }

            $mesa->pedidos_count = $query->count();
        });

        return view('garcom.index', compact('mesas'));
    }

    /**
     * Exibe a comanda da mesa
     */
    public function comanda(Mesa $mesa)
    {
        // Buscar apenas pedidos da sessão atual
        $pedidos = $mesa->pedidos()
            ->with(['itens.produto.categoria', 'itens.produtoTamanho', 'itens.sabores.sabor', 'user'])
            ->where(function($query) use ($mesa) {
                if ($mesa->sessao_atual) {
                    // Se tem sessão, filtrar apenas pedidos desta sessão
                    $query->where('sessao_id', $mesa->sessao_atual);
                } else {
                    // Se não tem sessão, não mostrar nenhum pedido (ou apenas sem sessao_id e ativos)
                    $query->whereNull('sessao_id')
                          ->whereIn('status', ['aberto', 'em_preparo', 'pronto', 'entregue']);
                }
            })
            ->whereIn('status', ['aberto', 'em_preparo', 'pronto', 'entregue', 'cancelado'])
            ->orderBy('created_at')
            ->get();

        // Calcular totais (excluindo pedidos cancelados)
        $totalGeral = $pedidos->whereNotIn('status', ['cancelado'])->sum('total');
        $quantidadeItens = $pedidos->whereNotIn('status', ['cancelado'])->sum(function($pedido) {
            return $pedido->itens->sum('quantidade');
        });

        // Buscar produtos para adicionar novos itens (incluindo tamanhos para pizzas)
        // Ordenar por ordem (menor primeiro) e depois por nome
        $produtosTemp = Produto::with(['categoria', 'tamanhos'])
            ->where('ativo', true)
            ->orderBy('ordem')
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

        // Buscar sabores agrupados por categoria para o modal de pizzas
        // Ordenar: especiais primeiro (que têm preços definidos), depois por nome
        $sabores = Sabor::with('categoria')
            ->where('ativo', true)
            ->orderByRaw('CASE WHEN preco_p IS NOT NULL OR preco_m IS NOT NULL OR preco_g IS NOT NULL OR preco_gg IS NOT NULL THEN 0 ELSE 1 END')
            ->orderBy('ordem')
            ->orderBy('nome')
            ->get()
            ->groupBy('categoria.nome');

        return view('garcom.comanda', compact('mesa', 'pedidos', 'totalGeral', 'quantidadeItens', 'produtos', 'sabores'));
    }

    /**
     * Exibe a comanda formatada para impressão térmica
     */
    public function imprimirComanda(Mesa $mesa)
    {
        $pedidos = $mesa->pedidos()
            ->with(['itens.produto.categoria', 'itens.produtoTamanho', 'itens.sabores.sabor', 'user'])
            ->where(function($query) use ($mesa) {
                if ($mesa->sessao_atual) {
                    $query->where('sessao_id', $mesa->sessao_atual);
                } else {
                    $query->whereNull('sessao_id');
                }
            })
            ->whereIn('status', ['aberto', 'em_preparo', 'pronto', 'entregue'])
            ->orderBy('created_at')
            ->get();

        $totalGeral = $pedidos->sum('total');
        $quantidadeItens = $pedidos->sum(function($pedido) {
            return $pedido->itens->sum('quantidade');
        });

        return view('garcom.imprimir-comanda', compact('mesa', 'pedidos', 'totalGeral', 'quantidadeItens'));
    }

    /**
     * Atualiza o nome do cliente da mesa
     */
    public function atualizarCliente(Request $request, Mesa $mesa)
    {
        $validated = $request->validate([
            'cliente_nome' => 'nullable|string|max:255',
        ]);

        $mesa->update([
            'cliente_nome' => $validated['cliente_nome'],
        ]);

        return response()->json([
            'success' => true,
            'cliente_nome' => $mesa->cliente_nome,
        ]);
    }

    /**
     * Cancela um pedido
     */
    public function cancelarPedido(Pedido $pedido)
    {
        // Verificar permissão do usuário
        if (!auth()->user()->podeCancelarPedidos()) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para cancelar pedidos.'
            ], 403);
        }

        // Verificar se o pedido pode ser cancelado
        if ($pedido->status == 'finalizado' || $pedido->status == 'cancelado') {
            return response()->json([
                'success' => false,
                'message' => 'Este pedido não pode ser cancelado.'
            ], 400);
        }

        $pedido->update([
            'status' => 'cancelado',
            'data_finalizacao' => now(),
        ]);

        // Verificar se ainda há pedidos ativos na mesa
        $mesa = $pedido->mesa;
        $pedidosAtivos = $mesa->pedidos()
            ->whereIn('status', ['aberto', 'em_preparo', 'pronto', 'entregue'])
            ->count();

        // Se não há mais pedidos ativos
        if ($pedidosAtivos == 0) {
            // Se for mesa virtual (online), deletar
            if ($mesa->isOnline()) {
                $mesa->delete();
            } else {
                // Mesa normal: liberar e preparar para nova sessão
                $mesa->update([
                    'status' => 'disponivel',
                    'cliente_nome' => null,
                    'sessao_atual' => null
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Pedido cancelado com sucesso!'
        ]);
    }

    /**
     * Atualiza a quantidade de um item individual do pedido
     */
    public function atualizarQuantidadeItem(Request $request, $itemId)
    {
        // Verificar permissão do usuário
        if (!auth()->user()->podeLancarPedidos()) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para alterar quantidades.'
            ], 403);
        }

        $validated = $request->validate([
            'quantidade' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $item = PedidoItem::findOrFail($itemId);
            $pedido = $item->pedido;

            // Verificar se o pedido pode ser editado
            if ($pedido->status == 'finalizado' || $pedido->status == 'cancelado') {
                return response()->json([
                    'success' => false,
                    'message' => 'Este pedido não pode ser editado.'
                ], 400);
            }

            // Atualizar quantidade e subtotal
            $item->quantidade = $validated['quantidade'];
            $item->subtotal = $item->preco_unitario * $item->quantidade;
            $item->save();

            // Recalcular total do pedido
            $pedido->total = $pedido->itens()->sum('subtotal');
            $pedido->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Quantidade atualizada com sucesso!',
                'nova_quantidade' => $item->quantidade,
                'novo_subtotal' => $item->subtotal,
                'novo_total_pedido' => $pedido->total,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar quantidade: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancela um item individual do pedido
     */
    public function cancelarItem($itemId)
    {
        // Verificar permissão do usuário
        if (!auth()->user()->podeCancelarItens()) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para cancelar itens.'
            ], 403);
        }

        DB::beginTransaction();
        try {
            $item = PedidoItem::findOrFail($itemId);
            $pedido = $item->pedido;

            // Verificar se o pedido pode ser editado
            if ($pedido->status == 'finalizado' || $pedido->status == 'cancelado') {
                return response()->json([
                    'success' => false,
                    'message' => 'Este item não pode ser cancelado.'
                ], 400);
            }

            // Verificar se é o último item do pedido
            if ($pedido->itens()->count() == 1) {
                // Se for o último item, cancelar o pedido inteiro
                $pedido->update([
                    'status' => 'cancelado',
                    'data_finalizacao' => now(),
                ]);

                // Verificar se ainda há pedidos ativos na mesa
                $mesa = $pedido->mesa;
                $pedidosAtivos = $mesa->pedidos()
                    ->whereIn('status', ['aberto', 'em_preparo', 'pronto', 'entregue'])
                    ->count();

                // Se não há mais pedidos ativos
                if ($pedidosAtivos == 0) {
                    // Se for mesa virtual (online), deletar
                    if ($mesa->isOnline()) {
                        $mesa->delete();
                    } else {
                        // Mesa normal: liberar
                        $mesa->update([
                            'status' => 'disponivel',
                            'cliente_nome' => null,
                            'sessao_atual' => null
                        ]);
                    }
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Último item do pedido cancelado. Pedido finalizado.',
                    'pedido_cancelado' => true
                ]);
            }

            // Remover item
            $item->delete();

            // Recalcular total do pedido
            $pedido->total = $pedido->itens()->sum('subtotal');
            $pedido->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item cancelado com sucesso!',
                'novo_total_pedido' => $pedido->total,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao cancelar item: ' . $e->getMessage()
            ], 500);
        }
    }
}
