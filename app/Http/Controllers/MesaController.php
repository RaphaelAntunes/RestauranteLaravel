<?php

namespace App\Http\Controllers;

use App\Models\Mesa;
use Illuminate\Http\Request;

class MesaController extends Controller
{
    public function index()
    {
        $mesas = Mesa::orderBy('numero')->get()->map(function($mesa) {
            // Contar pedidos da sessão atual
            $mesa->pedidos_count = $mesa->pedidos()
                ->where(function($query) use ($mesa) {
                    if ($mesa->sessao_atual) {
                        $query->where('sessao_id', $mesa->sessao_atual);
                    } else {
                        $query->whereNull('sessao_id');
                    }
                })
                ->whereIn('status', ['aberto', 'em_preparo', 'pronto'])
                ->count();
            return $mesa;
        });

        return view('mesas.index', compact('mesas'));
    }

    public function create()
    {
        return view('mesas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero' => 'required|integer|unique:mesas',
            'capacidade' => 'required|integer|min:1',
            'localizacao' => 'nullable|string|max:100',
            'status' => 'required|in:disponivel,ocupada,reservada,manutencao',
        ], [
            'numero.required' => 'O número da mesa é obrigatório',
            'numero.unique' => 'Já existe uma mesa com este número',
            'capacidade.required' => 'A capacidade é obrigatória',
        ]);

        Mesa::create($validated);

        return redirect()->route('mesas.index')
            ->with('success', 'Mesa criada com sucesso!');
    }

    public function show(Mesa $mesa)
    {
        // Carregar apenas pedidos da sessão atual
        $mesa->pedidos = $mesa->pedidos()
            ->where(function($query) use ($mesa) {
                if ($mesa->sessao_atual) {
                    $query->where('sessao_id', $mesa->sessao_atual);
                } else {
                    $query->whereNull('sessao_id');
                }
            })
            ->latest()
            ->get();

        return view('mesas.show', compact('mesa'));
    }

    public function edit(Mesa $mesa)
    {
        return view('mesas.edit', compact('mesa'));
    }

    public function update(Request $request, Mesa $mesa)
    {
        $validated = $request->validate([
            'numero' => 'required|integer|unique:mesas,numero,' . $mesa->id,
            'capacidade' => 'required|integer|min:1',
            'localizacao' => 'nullable|string|max:100',
            'status' => 'required|in:disponivel,ocupada,reservada,manutencao',
            'ativo' => 'boolean',
        ]);

        $mesa->update($validated);

        return redirect()->route('mesas.index')
            ->with('success', 'Mesa atualizada com sucesso!');
    }

    public function destroy(Mesa $mesa)
    {
        $pedidosAtivos = $mesa->pedidos()
            ->where(function($query) use ($mesa) {
                if ($mesa->sessao_atual) {
                    $query->where('sessao_id', $mesa->sessao_atual);
                } else {
                    $query->whereNull('sessao_id');
                }
            })
            ->whereIn('status', ['aberto', 'em_preparo', 'pronto'])
            ->count();

        if ($pedidosAtivos > 0) {
            return back()->with('error', 'Não é possível excluir uma mesa com pedidos ativos.');
        }

        $mesa->delete();

        return redirect()->route('mesas.index')
            ->with('success', 'Mesa excluída com sucesso!');
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
                    $query->where('sessao_id', $mesa->sessao_atual);
                } else {
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
        $produtosTemp = \App\Models\Produto::with(['categoria', 'tamanhos'])
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

        // Buscar sabores agrupados por categoria para o modal de pizzas
        $sabores = \App\Models\Sabor::with('categoria')
            ->where('ativo', true)
            ->orderBy('nome')
            ->get()
            ->groupBy('categoria.nome');

        return view('mesas.comanda', compact('mesa', 'pedidos', 'totalGeral', 'quantidadeItens', 'produtos', 'sabores'));
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

        return view('mesas.imprimir-comanda', compact('mesa', 'pedidos', 'totalGeral', 'quantidadeItens'));
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
    public function cancelarPedido(\App\Models\Pedido $pedido)
    {
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

        // Se não há mais pedidos ativos, liberar a mesa e preparar para nova sessão
        if ($pedidosAtivos == 0) {
            $mesa->update([
                'status' => 'disponivel',
                'cliente_nome' => null,
                'sessao_atual' => null // Limpar sessão para gerar nova no próximo pedido
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pedido cancelado com sucesso!'
        ]);
    }
}
