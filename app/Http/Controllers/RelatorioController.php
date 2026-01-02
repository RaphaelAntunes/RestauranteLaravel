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
        $dataInicio = $request->get('data_inicio', today()->startOfMonth());
        $dataFim = $request->get('data_fim', today());

        // Faturamento total
        $faturamento = Pagamento::whereBetween('created_at', [$dataInicio, $dataFim])
            ->where('status', 'aprovado')
            ->sum('total');

        // Número de pedidos
        $totalPedidos = Pedido::whereBetween('created_at', [$dataInicio, $dataFim])
            ->whereIn('status', ['finalizado'])
            ->count();

        // Ticket médio
        $ticketMedio = $totalPedidos > 0 ? $faturamento / $totalPedidos : 0;

        // Vendas por dia
        $vendasPorDia = Pagamento::whereBetween('created_at', [$dataInicio, $dataFim])
            ->where('status', 'aprovado')
            ->selectRaw('DATE(created_at) as data, SUM(total) as total, COUNT(*) as quantidade')
            ->groupBy('data')
            ->orderBy('data')
            ->get();

        // Vendas por forma de pagamento
        $vendasPorFormaPagamento = Pagamento::whereBetween('created_at', [$dataInicio, $dataFim])
            ->where('status', 'aprovado')
            ->selectRaw('forma_pagamento, SUM(total) as total, COUNT(*) as quantidade')
            ->groupBy('forma_pagamento')
            ->get();

        return view('relatorios.vendas', compact(
            'dataInicio',
            'dataFim',
            'faturamento',
            'totalPedidos',
            'ticketMedio',
            'vendasPorDia',
            'vendasPorFormaPagamento'
        ));
    }

    /**
     * Produtos mais vendidos
     */
    public function produtosMaisVendidos(Request $request)
    {
        $dataInicio = $request->get('data_inicio', today()->startOfMonth());
        $dataFim = $request->get('data_fim', today());

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

        return view('relatorios.produtos', compact('dataInicio', 'dataFim', 'produtos'));
    }

    /**
     * Faturamento mensal
     */
    public function faturamentoMensal(Request $request)
    {
        $ano = $request->get('ano', now()->year);

        $faturamentoPorMes = Pagamento::whereYear('created_at', $ano)
            ->where('status', 'aprovado')
            ->selectRaw('
                MONTH(created_at) as mes,
                SUM(total) as total,
                COUNT(*) as quantidade,
                AVG(total) as ticket_medio
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
                'quantidade' => $faturamentoPorMes->has($mes) ? $faturamentoPorMes[$mes]->quantidade : 0,
                'ticket_medio' => $faturamentoPorMes->has($mes) ? $faturamentoPorMes[$mes]->ticket_medio : 0,
            ];
        });

        $totalAnual = $meses->sum('total');

        return view('relatorios.faturamento', compact('ano', 'meses', 'totalAnual'));
    }

    /**
     * Relatório de desempenho por garçom
     */
    public function desempenhoGarcons(Request $request)
    {
        $dataInicio = $request->get('data_inicio', today()->startOfMonth());
        $dataFim = $request->get('data_fim', today());

        $garcons = DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->leftJoin('pedidos', 'users.id', '=', 'pedidos.user_id')
            ->leftJoin('pagamentos', function($join) use ($dataInicio, $dataFim) {
                $join->on('pedidos.id', '=', DB::raw('(SELECT pedido_id FROM pagamento_detalhes WHERE pagamento_detalhes.pedido_id = pedidos.id LIMIT 1)'))
                    ->whereBetween('pagamentos.created_at', [$dataInicio, $dataFim])
                    ->where('pagamentos.status', 'aprovado');
            })
            ->where('roles.nome', 'garcom')
            ->whereBetween('pedidos.created_at', [$dataInicio, $dataFim])
            ->selectRaw('
                users.id,
                users.nome,
                COUNT(DISTINCT pedidos.id) as total_pedidos,
                COALESCE(SUM(pagamentos.total), 0) as faturamento
            ')
            ->groupBy('users.id', 'users.nome')
            ->orderBy('faturamento', 'desc')
            ->get();

        return view('relatorios.garcons', compact('dataInicio', 'dataFim', 'garcons'));
    }
}
