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
            'taxa_servico_aplicada' => 'nullable|boolean',
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

            // Calcular desconto (arredondado para 2 casas decimais)
            $desconto = 0;
            $valor_desconto = 0;
            if (isset($validated['tipo_desconto']) && $validated['tipo_desconto'] === 'porcentagem') {
                $desconto = $validated['desconto_porcentagem'] ?? 0;
                $valor_desconto = round(($subtotal * $desconto) / 100, 2);
            } elseif (isset($validated['tipo_desconto']) && $validated['tipo_desconto'] === 'valor') {
                $valor_desconto = round($validated['desconto_valor'] ?? 0, 2);
                $desconto = $subtotal > 0 ? ($valor_desconto / $subtotal) * 100 : 0; // Converter para % para salvar
            }

            // Calcular acréscimo (arredondado para 2 casas decimais)
            $acrescimo = 0;
            $valor_acrescimo = 0;
            if (isset($validated['tipo_acrescimo']) && $validated['tipo_acrescimo'] === 'porcentagem') {
                $acrescimo = $validated['acrescimo_porcentagem'] ?? 0;
                $valor_acrescimo = round(($subtotal * $acrescimo) / 100, 2);
            } elseif (isset($validated['tipo_acrescimo']) && $validated['tipo_acrescimo'] === 'valor') {
                $valor_acrescimo = round($validated['acrescimo_valor'] ?? 0, 2);
                $acrescimo = $subtotal > 0 ? ($valor_acrescimo / $subtotal) * 100 : 0; // Converter para % para salvar
            }

            // Calcular taxa de serviço (10% do garçom)
            $taxa_servico_aplicada = $request->boolean('taxa_servico_aplicada');
            $taxa_servico = 10; // 10%
            $valor_taxa_servico = 0;
            if ($taxa_servico_aplicada) {
                $valor_taxa_servico = round(($subtotal * $taxa_servico) / 100, 2);
            }

            // Arredondar para 2 casas decimais para evitar problemas de ponto flutuante
            $total = round($subtotal - $valor_desconto + $valor_acrescimo + $valor_taxa_servico, 2);
            $valor_pago = round($validated['valor_pago'], 2);

            // Validar se o valor pago é suficiente (tolerância de 1 centavo)
            if ($valor_pago < $total - 0.01) {
                return back()
                    ->withInput()
                    ->with('error', 'O valor pago (R$ ' . number_format($valor_pago, 2, ',', '.') . ') é menor que o total da conta (R$ ' . number_format($total, 2, ',', '.') . ')');
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
                'taxa_servico_aplicada' => $taxa_servico_aplicada,
                'taxa_servico' => $taxa_servico_aplicada ? $taxa_servico : 0,
                'valor_taxa_servico' => $valor_taxa_servico,
                'total' => $total,
                'valor_total' => $total, // Campo obrigatório
                'forma_pagamento' => $validated['forma_pagamento'],
                'metodo_pagamento' => $validated['forma_pagamento'], // Compatibilidade com campo antigo
                'valor_pago' => $valor_pago,
                'troco' => round(max(0, $valor_pago - $total), 2),
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

            // Se for mesa virtual (online), deletar. Caso contrário, liberar.
            if ($mesa->isOnline()) {
                $mesa->delete();
            } else {
                $mesa->update([
                    'status' => 'disponivel',
                    'cliente_nome' => null,
                    'sessao_atual' => null
                ]);
            }

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

    /**
     * Relatório de gorjetas (taxa de serviço)
     */
    public function relatorioGorjetas(Request $request)
    {
        $dataInicio = $request->get('data_inicio', now()->startOfDay()->format('Y-m-d'));
        $dataFim = $request->get('data_fim', now()->endOfDay()->format('Y-m-d'));

        $gorjetas = Pagamento::where('taxa_servico_aplicada', true)
            ->where('status', 'aprovado')
            ->whereDate('created_at', '>=', $dataInicio)
            ->whereDate('created_at', '<=', $dataFim)
            ->with(['mesa', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalGorjetas = $gorjetas->sum('valor_taxa_servico');
        $quantidadeAtendimentos = $gorjetas->count();

        // Agrupar por garçom/atendente com estatísticas detalhadas
        $gorjetasPorAtendente = $gorjetas->groupBy('user_id')->map(function ($pagamentos) {
            $totalGorjeta = $pagamentos->sum('valor_taxa_servico');
            $totalVendido = $pagamentos->sum('subtotal');
            return [
                'user_id' => $pagamentos->first()->user_id,
                'atendente' => $pagamentos->first()->user->nome ?? 'Desconhecido',
                'total' => $totalGorjeta,
                'total_vendido' => $totalVendido,
                'quantidade' => $pagamentos->count(),
                'media_por_atendimento' => $pagamentos->count() > 0 ? $totalGorjeta / $pagamentos->count() : 0,
                'maior_gorjeta' => $pagamentos->max('valor_taxa_servico'),
                'menor_gorjeta' => $pagamentos->min('valor_taxa_servico'),
            ];
        })->sortByDesc('total')->values();

        // Agrupar por dia
        $gorjetasPorDia = $gorjetas->groupBy(function ($pagamento) {
            return $pagamento->created_at->format('Y-m-d');
        })->map(function ($pagamentos, $data) {
            return [
                'data' => $data,
                'data_formatada' => \Carbon\Carbon::parse($data)->format('d/m/Y'),
                'dia_semana' => \Carbon\Carbon::parse($data)->locale('pt_BR')->isoFormat('dddd'),
                'total' => $pagamentos->sum('valor_taxa_servico'),
                'total_vendido' => $pagamentos->sum('subtotal'),
                'quantidade' => $pagamentos->count(),
            ];
        })->sortByDesc('data')->values();

        // Detalhamento por garçom por dia
        $gorjetasPorAtendentePorDia = $gorjetas->groupBy('user_id')->map(function ($pagamentos) {
            $atendente = $pagamentos->first()->user->nome ?? 'Desconhecido';
            $userId = $pagamentos->first()->user_id;

            $porDia = $pagamentos->groupBy(function ($p) {
                return $p->created_at->format('Y-m-d');
            })->map(function ($pagsDia, $data) {
                return [
                    'data' => $data,
                    'data_formatada' => \Carbon\Carbon::parse($data)->format('d/m/Y'),
                    'total_gorjeta' => $pagsDia->sum('valor_taxa_servico'),
                    'total_vendido' => $pagsDia->sum('subtotal'),
                    'quantidade' => $pagsDia->count(),
                ];
            })->sortByDesc('data')->values();

            return [
                'user_id' => $userId,
                'atendente' => $atendente,
                'dias' => $porDia,
                'total_geral' => $pagamentos->sum('valor_taxa_servico'),
                'total_vendido_geral' => $pagamentos->sum('subtotal'),
            ];
        })->sortByDesc('total_geral')->values();

        // Estatísticas gerais
        $estatisticas = [
            'maior_gorjeta' => $gorjetas->max('valor_taxa_servico') ?? 0,
            'menor_gorjeta' => $gorjetas->min('valor_taxa_servico') ?? 0,
            'media_gorjeta' => $quantidadeAtendimentos > 0 ? $totalGorjetas / $quantidadeAtendimentos : 0,
            'total_vendido' => $gorjetas->sum('subtotal'),
            'ticket_medio' => $quantidadeAtendimentos > 0 ? $gorjetas->sum('subtotal') / $quantidadeAtendimentos : 0,
        ];

        return view('pdv.gorjetas', compact(
            'gorjetas',
            'totalGorjetas',
            'quantidadeAtendimentos',
            'gorjetasPorAtendente',
            'gorjetasPorDia',
            'gorjetasPorAtendentePorDia',
            'estatisticas',
            'dataInicio',
            'dataFim'
        ));
    }
}
