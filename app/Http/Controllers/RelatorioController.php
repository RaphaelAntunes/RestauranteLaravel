<?php

namespace App\Http\Controllers;

use App\Models\Pagamento;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RelatorioController extends Controller
{
    /**
     * Dashboard de relatórios
     */
    public function index()
    {
        return view('relatorios.index');
    }

    /**
     * Relatório de vendas por período
     */
    public function vendas(Request $request)
    {
        $dataInicio = $request->get('data_inicio')
            ? \Carbon\Carbon::parse($request->get('data_inicio'))->startOfDay()
            : today()->startOfMonth()->startOfDay();
        $dataFim = $request->get('data_fim')
            ? \Carbon\Carbon::parse($request->get('data_fim'))->endOfDay()
            : today()->endOfDay();

        // Faturamento bruto e taxa de serviço
        $faturamentoBruto = Pagamento::whereBetween('created_at', [$dataInicio, $dataFim])
            ->where('status', 'aprovado')
            ->sum('total');
        $taxaServicoTotal = Pagamento::whereBetween('created_at', [$dataInicio, $dataFim])
            ->where('status', 'aprovado')
            ->sum('valor_taxa_servico');

        // Faturamento líquido (sem taxa de serviço - 10% é repassado aos garçons)
        $faturamento = $faturamentoBruto - $taxaServicoTotal;

        // Número de pedidos (usando pagamentos para consistência)
        $totalPedidos = Pagamento::whereBetween('created_at', [$dataInicio, $dataFim])
            ->where('status', 'aprovado')
            ->count();

        // Ticket médio (baseado no faturamento líquido)
        $ticketMedio = $totalPedidos > 0 ? $faturamento / $totalPedidos : 0;

        // Vendas por dia (faturamento líquido)
        $vendasPorDia = Pagamento::whereBetween('created_at', [$dataInicio, $dataFim])
            ->where('status', 'aprovado')
            ->selectRaw('DATE(created_at) as data, SUM(total - COALESCE(valor_taxa_servico, 0)) as total, COUNT(*) as quantidade')
            ->groupBy('data')
            ->orderBy('data')
            ->get();

        // Vendas por forma de pagamento (faturamento líquido)
        $vendasPorFormaPagamento = Pagamento::whereBetween('created_at', [$dataInicio, $dataFim])
            ->where('status', 'aprovado')
            ->selectRaw('forma_pagamento, SUM(total - COALESCE(valor_taxa_servico, 0)) as total, COUNT(*) as quantidade')
            ->groupBy('forma_pagamento')
            ->get();

        // Vendas por tipo de pedido (mesa/delivery/retirada)
        $vendasPorTipo = Pagamento::join('mesas', 'pagamentos.mesa_id', '=', 'mesas.id')
            ->whereBetween('pagamentos.created_at', [$dataInicio, $dataFim])
            ->where('pagamentos.status', 'aprovado')
            ->selectRaw('
                mesas.tipo,
                SUM(pagamentos.total - COALESCE(pagamentos.valor_taxa_servico, 0)) as total,
                COUNT(*) as quantidade
            ')
            ->groupBy('mesas.tipo')
            ->get()
            ->keyBy('tipo');

        // Garantir que todos os tipos existam no resultado
        $tiposVenda = [
            'mesa' => [
                'label' => 'Mesa',
                'total' => $vendasPorTipo->get('normal')->total ?? 0,
                'quantidade' => $vendasPorTipo->get('normal')->quantidade ?? 0,
                'cor' => 'green',
                'icone' => 'M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z'
            ],
            'delivery' => [
                'label' => 'Delivery',
                'total' => $vendasPorTipo->get('delivery')->total ?? 0,
                'quantidade' => $vendasPorTipo->get('delivery')->quantidade ?? 0,
                'cor' => 'purple',
                'icone' => 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0'
            ],
            'retirada' => [
                'label' => 'Retirada',
                'total' => $vendasPorTipo->get('retirada')->total ?? 0,
                'quantidade' => $vendasPorTipo->get('retirada')->quantidade ?? 0,
                'cor' => 'indigo',
                'icone' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4'
            ]
        ];

        return view('relatorios.vendas', [
            'dataInicio' => $dataInicio->format('Y-m-d'),
            'dataFim' => $dataFim->format('Y-m-d'),
            'faturamento' => $faturamento,
            'taxaServico' => $taxaServicoTotal,
            'totalPedidos' => $totalPedidos,
            'ticketMedio' => $ticketMedio,
            'vendasPorDia' => $vendasPorDia,
            'vendasPorFormaPagamento' => $vendasPorFormaPagamento,
            'tiposVenda' => $tiposVenda
        ]);
    }

    /**
     * Produtos mais vendidos
     */
    public function produtosMaisVendidos(Request $request)
    {
        $dataInicio = $request->get('data_inicio')
            ? \Carbon\Carbon::parse($request->get('data_inicio'))->startOfDay()
            : today()->startOfMonth()->startOfDay();
        $dataFim = $request->get('data_fim')
            ? \Carbon\Carbon::parse($request->get('data_fim'))->endOfDay()
            : today()->endOfDay();

        $produtos = PedidoItem::join('pedidos', 'pedido_itens.pedido_id', '=', 'pedidos.id')
            ->join('produtos', 'pedido_itens.produto_id', '=', 'produtos.id')
            ->join('categorias', 'produtos.categoria_id', '=', 'categorias.id')
            ->whereBetween('pedidos.created_at', [$dataInicio, $dataFim])
            ->whereIn('pedidos.status', ['finalizado'])
            ->selectRaw('
                produtos.id,
                produtos.nome,
                categorias.nome as categoria,
                SUM(pedido_itens.quantidade) as quantidade_vendida,
                SUM(pedido_itens.subtotal) as faturamento
            ')
            ->groupBy('produtos.id', 'produtos.nome', 'categorias.nome')
            ->orderBy('quantidade_vendida', 'desc')
            ->limit(20)
            ->get();

        return view('relatorios.produtos', [
            'dataInicio' => $dataInicio->format('Y-m-d'),
            'dataFim' => $dataFim->format('Y-m-d'),
            'produtos' => $produtos
        ]);
    }

    /**
     * Faturamento mensal
     */
    public function faturamentoMensal(Request $request)
    {
        $ano = $request->get('ano', now()->year);

        // Faturamento líquido (sem taxa de serviço do garçom)
        $faturamentoPorMes = Pagamento::whereYear('created_at', $ano)
            ->where('status', 'aprovado')
            ->selectRaw('
                MONTH(created_at) as mes,
                SUM(total - COALESCE(valor_taxa_servico, 0)) as total,
                SUM(COALESCE(valor_taxa_servico, 0)) as taxa_servico,
                COUNT(*) as quantidade,
                AVG(total - COALESCE(valor_taxa_servico, 0)) as ticket_medio
            ')
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->keyBy('mes');

        // Preencher meses sem vendas
        $meses = collect(range(1, 12))->map(function($mes) use ($faturamentoPorMes) {
            return [
                'mes' => $mes,
                'nome' => date('F', mktime(0, 0, 0, $mes, 1)),
                'total' => $faturamentoPorMes->has($mes) ? $faturamentoPorMes[$mes]->total : 0,
                'taxa_servico' => $faturamentoPorMes->has($mes) ? $faturamentoPorMes[$mes]->taxa_servico : 0,
                'quantidade' => $faturamentoPorMes->has($mes) ? $faturamentoPorMes[$mes]->quantidade : 0,
                'ticket_medio' => $faturamentoPorMes->has($mes) ? $faturamentoPorMes[$mes]->ticket_medio : 0,
            ];
        });

        $totalAnual = $meses->sum('total');
        $totalTaxaServico = $meses->sum('taxa_servico');

        return view('relatorios.faturamento', compact('ano', 'meses', 'totalAnual', 'totalTaxaServico'));
    }

    /**
     * Relatório de desempenho por garçom
     */
    public function desempenhoGarcons(Request $request)
    {
        $dataInicio = $request->get('data_inicio')
            ? \Carbon\Carbon::parse($request->get('data_inicio'))->startOfDay()
            : today()->startOfDay();
        $dataFim = $request->get('data_fim')
            ? \Carbon\Carbon::parse($request->get('data_fim'))->endOfDay()
            : today()->endOfDay();

        // Buscar TODOS os garçons ativos
        $garcons = DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->where('roles.nome', 'garcom')
            ->where('users.ativo', true)
            ->select('users.id', 'users.nome')
            ->orderBy('users.nome')
            ->get()
            ->map(function ($garcom) use ($dataInicio, $dataFim) {
                // Buscar pedidos finalizados do garçom no período
                $pedidos = Pedido::where('user_id', $garcom->id)
                    ->whereBetween('created_at', [$dataInicio, $dataFim])
                    ->where('status', 'finalizado')
                    ->get();

                $totalPedidos = $pedidos->count();
                $faturamento = $pedidos->sum('total');

                // Buscar gorjetas através dos pedidos do garçom
                // A gorjeta está vinculada ao pagamento, que está vinculado aos pedidos via pagamento_detalhes
                $pedidosIds = $pedidos->pluck('id')->toArray();

                $gorjetas = 0;
                if (!empty($pedidosIds)) {
                    // Buscar pagamentos que contêm os pedidos deste garçom
                    $pagamentoIds = DB::table('pagamento_detalhes')
                        ->whereIn('pedido_id', $pedidosIds)
                        ->pluck('pagamento_id')
                        ->unique()
                        ->toArray();

                    if (!empty($pagamentoIds)) {
                        $gorjetas = Pagamento::whereIn('id', $pagamentoIds)
                            ->where('status', 'aprovado')
                            ->where('taxa_servico_aplicada', true)
                            ->sum('valor_taxa_servico');
                    }
                }

                $garcom->total_pedidos = $totalPedidos;
                $garcom->faturamento = $faturamento;
                $garcom->gorjetas = $gorjetas;
                $garcom->ticket_medio = $totalPedidos > 0 ? $faturamento / $totalPedidos : 0;

                return $garcom;
            })
            ->sortByDesc('faturamento')
            ->values();

        // Total geral de gorjetas no período
        $totalGorjetas = Pagamento::whereBetween('created_at', [$dataInicio, $dataFim])
            ->where('status', 'aprovado')
            ->where('taxa_servico_aplicada', true)
            ->sum('valor_taxa_servico');

        // Total de faturamento dos garçons
        $totalFaturamento = $garcons->sum('faturamento');
        $totalPedidosGeral = $garcons->sum('total_pedidos');

        // Buscar gorjetas para detalhamento por dia (incluindo pedidos vinculados)
        $gorjetas = Pagamento::where('taxa_servico_aplicada', true)
            ->whereBetween('created_at', [$dataInicio, $dataFim])
            ->where('status', 'aprovado')
            ->with(['mesa', 'user', 'detalhes.pedido'])
            ->orderBy('created_at', 'desc')
            ->get();

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

        return view('relatorios.garcons', [
            'dataInicio' => $dataInicio->format('Y-m-d'),
            'dataFim' => $dataFim->format('Y-m-d'),
            'garcons' => $garcons,
            'totalGorjetas' => $totalGorjetas,
            'totalFaturamento' => $totalFaturamento,
            'totalPedidosGeral' => $totalPedidosGeral,
            'gorjetas' => $gorjetas,
            'gorjetasPorDia' => $gorjetasPorDia,
            'gorjetasPorAtendentePorDia' => $gorjetasPorAtendentePorDia,
        ]);
    }
}
