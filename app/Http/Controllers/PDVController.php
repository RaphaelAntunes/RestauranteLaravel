<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Mesa;
use App\Models\Pagamento;
use App\Models\PagamentoDetalhe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PDVController extends Controller
{
    /**
     * Lista mesas com pedidos para fechar
     */
    public function index()
    {
        $mesas = Mesa::where('status', 'ocupada')
            ->with(['pedidos' => function($query) {
                $query->whereIn('status', ['aberto', 'em_preparo', 'pronto', 'entregue'])
                    ->with('itens.produto', 'itens.produtoTamanho', 'itens.sabores.sabor');
            }])
            ->orderBy('numero')
            ->get();

        return view('pdv.index', compact('mesas'));
    }

    /**
     * Exibe tela de fechamento de mesa
     */
    public function fecharMesa(Request $request, Mesa $mesa)
    {
        // Verificar se é garçom e se tem permissão
        $source = $request->get('source');
        if ($source === 'garcom' && !auth()->user()->podeFecharMesas()) {
            return redirect()->route('garcom.index')
                ->with('error', 'Você não tem permissão para fechar mesas.');
        }

        $pedidos = $mesa->pedidos()
            ->where(function($query) use ($mesa) {
                if ($mesa->sessao_atual) {
                    $query->where('sessao_id', $mesa->sessao_atual);
                } else {
                    $query->whereNull('sessao_id');
                }
            })
            ->whereIn('status', ['aberto', 'em_preparo', 'pronto', 'entregue'])
            ->with('itens.produto', 'itens.produtoTamanho', 'itens.sabores.sabor')
            ->get();

        if ($pedidos->isEmpty()) {
            $redirectRoute = $request->get('source') === 'garcom' ? 'garcom.index' : 'pdv.index';
            return redirect()->route($redirectRoute)
                ->with('error', 'Esta mesa não possui pedidos em aberto.');
        }

        $total = $pedidos->sum('total');
        $source = $request->get('source');

        return view('pdv.fechar', compact('mesa', 'pedidos', 'total', 'source'));
    }

    /**
     * Processa o pagamento e fecha a conta
     */
    public function processarPagamento(Request $request, Mesa $mesa)
    {
        // Verificar se é garçom e se tem permissão
        $source = $request->get('source');
        if ($source === 'garcom' && !auth()->user()->podeFecharMesas()) {
            return redirect()->route('garcom.index')
                ->with('error', 'Você não tem permissão para processar pagamentos.');
        }

        $validated = $request->validate([
            'forma_pagamento' => 'required|in:dinheiro,pix,credito,debito,multiplo',
            'valor_pago' => 'required|numeric|min:0',
            'formas_pagamento' => 'nullable|array',
            'formas_pagamento.*.forma' => 'required|in:dinheiro,pix,credito,debito',
            'formas_pagamento.*.valor' => 'required|numeric|min:0.01',
            'tipo_desconto' => 'nullable|in:porcentagem,valor',
            'desconto_porcentagem' => 'nullable|numeric|min:0|max:100',
            'desconto_valor' => 'nullable|numeric|min:0',
            'tipo_acrescimo' => 'nullable|in:porcentagem,valor',
            'acrescimo_porcentagem' => 'nullable|numeric|min:0|max:100',
            'acrescimo_valor' => 'nullable|numeric|min:0',
            'observacoes' => 'nullable|string',
        ], [
            'forma_pagamento.required' => 'Selecione a forma de pagamento',
            'valor_pago.required' => 'Informe o valor pago',
        ]);

        DB::beginTransaction();
        try {
            // Buscar pedidos da mesa (apenas da sessão atual)
            $pedidos = $mesa->pedidos()
                ->where(function($query) use ($mesa) {
                    if ($mesa->sessao_atual) {
                        $query->where('sessao_id', $mesa->sessao_atual);
                    } else {
                        $query->whereNull('sessao_id');
                    }
                })
                ->whereIn('status', ['aberto', 'em_preparo', 'pronto', 'entregue'])
                ->get();

            if ($pedidos->isEmpty()) {
                return back()->with('error', 'Esta mesa não possui pedidos em aberto.');
            }

            $subtotal = $pedidos->sum('total');

            // Calcular desconto
            $desconto = 0;
            $valor_desconto = 0;
            if (isset($validated['tipo_desconto']) && $validated['tipo_desconto'] === 'porcentagem') {
                $desconto = $validated['desconto_porcentagem'] ?? 0;
                $valor_desconto = ($subtotal * $desconto) / 100;
            } elseif (isset($validated['tipo_desconto']) && $validated['tipo_desconto'] === 'valor') {
                $valor_desconto = $validated['desconto_valor'] ?? 0;
                $desconto = $subtotal > 0 ? ($valor_desconto / $subtotal) * 100 : 0; // Converter para % para salvar
            }

            // Calcular acréscimo
            $acrescimo = 0;
            $valor_acrescimo = 0;
            if (isset($validated['tipo_acrescimo']) && $validated['tipo_acrescimo'] === 'porcentagem') {
                $acrescimo = $validated['acrescimo_porcentagem'] ?? 0;
                $valor_acrescimo = ($subtotal * $acrescimo) / 100;
            } elseif (isset($validated['tipo_acrescimo']) && $validated['tipo_acrescimo'] === 'valor') {
                $valor_acrescimo = $validated['acrescimo_valor'] ?? 0;
                $acrescimo = $subtotal > 0 ? ($valor_acrescimo / $subtotal) * 100 : 0; // Converter para % para salvar
            }

            $total = $subtotal - $valor_desconto + $valor_acrescimo;

            // Validar se o valor pago é suficiente
            if ($validated['valor_pago'] < $total) {
                return back()
                    ->withInput()
                    ->with('error', 'O valor pago (R$ ' . number_format($validated['valor_pago'], 2, ',', '.') . ') é menor que o total da conta (R$ ' . number_format($total, 2, ',', '.') . ')');
            }

            // Criar pagamento
            $pagamento = Pagamento::create([
                'user_id' => auth()->id(),
                'mesa_id' => $mesa->id,
                'subtotal' => $subtotal,
                'desconto' => $desconto,
                'valor_desconto' => $valor_desconto,
                'acrescimo' => $acrescimo,
                'valor_acrescimo' => $valor_acrescimo,
                'total' => $total,
                'valor_total' => $total, // Campo obrigatório
                'forma_pagamento' => $validated['forma_pagamento'],
                'metodo_pagamento' => $validated['forma_pagamento'], // Compatibilidade com campo antigo
                'valor_pago' => $validated['valor_pago'],
                'troco' => max(0, $validated['valor_pago'] - $total),
                'status' => 'aprovado',
                'observacoes' => $validated['observacoes'],
            ]);

            // Criar detalhes do pagamento (vincular pedidos)
            foreach ($pedidos as $pedido) {
                PagamentoDetalhe::create([
                    'pagamento_id' => $pagamento->id,
                    'pedido_id' => $pedido->id,
                    'valor' => $pedido->total,
                ]);

                // Finalizar pedido
                $pedido->update([
                    'status' => 'finalizado',
                    'data_finalizacao' => now(),
                ]);
            }

            // Liberar mesa, limpar o cliente e preparar para nova sessão
            $mesa->update([
                'status' => 'disponivel',
                'cliente_nome' => null,
                'sessao_atual' => null // Limpar sessão para gerar nova no próximo pedido
            ]);

            DB::commit();

            // Verificar de onde veio a requisição
            if ($request->get('source') === 'garcom') {
                return redirect()->route('garcom.index')
                    ->with('success', 'Conta fechada com sucesso!');
            }

            return redirect()->route('pdv.comprovante', $pagamento)
                ->with('success', 'Pagamento realizado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao processar pagamento: ' . $e->getMessage());
        }
    }

    /**
     * Exibe comprovante de pagamento
     */
    public function comprovante(Pagamento $pagamento)
    {
        $pagamento->load(['mesa', 'user', 'detalhes.pedido.itens.produto', 'detalhes.pedido.itens.produtoTamanho', 'detalhes.pedido.itens.sabores.sabor']);
        return view('pdv.comprovante', compact('pagamento'));
    }

    /**
     * Exibe comprovante para impressão térmica
     */
    public function imprimirComprovante(Pagamento $pagamento)
    {
        $pagamento->load(['mesa', 'user', 'detalhes.pedido.itens.produto', 'detalhes.pedido.itens.produtoTamanho', 'detalhes.pedido.itens.sabores.sabor']);
        return view('pdv.imprimir', compact('pagamento'));
    }

    /**
     * Lista histórico de pagamentos
     */
    public function historico()
    {
        $pagamentos = Pagamento::with(['mesa', 'user'])
            ->latest()
            ->paginate(20);

        return view('pdv.historico', compact('pagamentos'));
    }
}
