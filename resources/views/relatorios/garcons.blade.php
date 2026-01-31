@extends('layouts.app')

@section('title', 'Desempenho dos Garçons')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Desempenho dos Garçons</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Ranking de desempenho e gorjetas por garçom</p>
        </div>
        <a href="{{ route('relatorios.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
            ← Voltar
        </a>
    </div>

    <!-- Filtros -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data Início</label>
                <input type="date" name="data_inicio" value="{{ $dataInicio }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data Fim</label>
                <input type="date" name="data_fim" value="{{ $dataFim }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">
                    Filtrar
                </button>
            </div>
        </form>
    </div>

    <!-- Cards de Resumo Geral -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Faturamento Total</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">R$ {{ number_format($totalFaturamento, 2, ',', '.') }}</p>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900/30 p-3 rounded-lg">
                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Gorjetas (10%)</p>
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400 mt-1">R$ {{ number_format($totalGorjetas, 2, ',', '.') }}</p>
                </div>
                <div class="bg-green-100 dark:bg-green-900/30 p-3 rounded-lg">
                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total de Pedidos</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalPedidosGeral }}</p>
                </div>
                <div class="bg-purple-100 dark:bg-purple-900/30 p-3 rounded-lg">
                    <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Ranking de Garçons -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Ranking de Garçons</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($garcons as $index => $garcom)
                <div class="bg-gray-50 dark:bg-gray-700 rounded-xl border-2
                    @if($index == 0) border-yellow-400 dark:border-yellow-500
                    @elseif($index == 1) border-gray-300 dark:border-gray-500
                    @elseif($index == 2) border-orange-400 dark:border-orange-500
                    @else border-gray-200 dark:border-gray-600
                    @endif p-5">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg
                                @if($index == 0) bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400
                                @elseif($index == 1) bg-gray-100 dark:bg-gray-600 text-gray-600 dark:text-gray-300
                                @elseif($index == 2) bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400
                                @else bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-500
                                @endif">
                                {{ $index + 1 }}º
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white">{{ $garcom->nome }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $garcom->total_pedidos }} pedido(s)</p>
                            </div>
                        </div>
                        @if($index < 3)
                            <div class="text-2xl">
                                @if($index == 0) 🥇
                                @elseif($index == 1) 🥈
                                @else 🥉
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-600">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Faturamento</span>
                            <span class="font-bold text-gray-900 dark:text-white">R$ {{ number_format($garcom->faturamento, 2, ',', '.') }}</span>
                        </div>

                        <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-600">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Gorjetas (10%)</span>
                            <span class="font-bold text-green-600 dark:text-green-400">R$ {{ number_format($garcom->gorjetas, 2, ',', '.') }}</span>
                        </div>

                        @if($garcom->total_pedidos > 0)
                            <div class="flex justify-between items-center py-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Ticket Médio</span>
                                <span class="font-semibold text-blue-600 dark:text-blue-400">R$ {{ number_format($garcom->ticket_medio, 2, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-3 p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">Nenhum garçom com vendas no período</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Detalhamento de Gorjetas por Garçom e por Dia -->
    @if(isset($gorjetasPorAtendentePorDia) && $gorjetasPorAtendentePorDia->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
            <svg class="w-6 h-6 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            Detalhamento de Gorjetas por Dia
        </h2>

        <div class="space-y-6">
            @foreach($gorjetasPorAtendentePorDia as $atendente)
            <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-5 border border-gray-200 dark:border-gray-600">
                <!-- Cabeçalho do Garçom -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 pb-4 border-b border-gray-200 dark:border-gray-600">
                    <div class="flex items-center space-x-3">
                        <div class="bg-gradient-to-r from-green-500 to-green-600 p-3 rounded-full">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $atendente['atendente'] }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $atendente['dias']->count() }} dia(s) com gorjeta</p>
                        </div>
                    </div>
                    <div class="mt-3 md:mt-0 text-right">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Vendido</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">R$ {{ number_format($atendente['total_vendido_geral'], 2, ',', '.') }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total em Gorjetas</p>
                        <p class="text-xl font-bold text-green-600 dark:text-green-400">R$ {{ number_format($atendente['total_geral'], 2, ',', '.') }}</p>
                    </div>
                </div>

                <!-- Tabela de dias -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-600">
                                <th class="text-left py-2 px-3 text-sm font-medium text-gray-600 dark:text-gray-400">Data</th>
                                <th class="text-center py-2 px-3 text-sm font-medium text-gray-600 dark:text-gray-400">Atendimentos</th>
                                <th class="text-right py-2 px-3 text-sm font-medium text-gray-600 dark:text-gray-400">Vendido</th>
                                <th class="text-right py-2 px-3 text-sm font-medium text-gray-600 dark:text-gray-400">Gorjeta (10%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($atendente['dias'] as $dia)
                            <tr class="border-b border-gray-100 dark:border-gray-600/50 hover:bg-gray-100 dark:hover:bg-gray-600/30 transition">
                                <td class="py-3 px-3 text-gray-900 dark:text-white">{{ $dia['data_formatada'] }}</td>
                                <td class="py-3 px-3 text-center">
                                    <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 px-2 py-1 rounded text-sm font-medium">
                                        {{ $dia['quantidade'] }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-right text-gray-700 dark:text-gray-300">R$ {{ number_format($dia['total_vendido'], 2, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right">
                                    <span class="text-green-600 dark:text-green-400 font-semibold">R$ {{ number_format($dia['total_gorjeta'], 2, ',', '.') }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Detalhamento de Pedidos com Gorjeta -->
    @if(isset($gorjetas) && $gorjetas->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
            <svg class="w-6 h-6 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Pedidos com Taxa de Serviço (10%)
        </h2>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Data/Hora</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Garçom</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Mesa</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Pedido(s)</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Subtotal</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Gorjeta (10%)</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($gorjetas as $pagamento)
                    <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <td class="py-3 px-4 text-gray-900 dark:text-white">
                            <div class="font-medium">{{ $pagamento->created_at->format('d/m/Y') }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $pagamento->created_at->format('H:i') }}</div>
                        </td>
                        <td class="py-3 px-4 text-gray-900 dark:text-white">
                            {{ $pagamento->user->nome ?? 'N/A' }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 px-2 py-1 rounded text-sm font-medium">
                                Mesa {{ $pagamento->mesa->numero ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="space-y-1">
                                @if($pagamento->detalhes && $pagamento->detalhes->count() > 0)
                                    @foreach($pagamento->detalhes as $detalhe)
                                        @if($detalhe->pedido)
                                        <div class="flex items-center gap-2">
                                            <span class="bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300 px-2 py-0.5 rounded text-xs font-mono">
                                                {{ $detalhe->pedido->numero_pedido }}
                                            </span>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                                R$ {{ number_format($detalhe->valor, 2, ',', '.') }}
                                            </span>
                                        </div>
                                        @endif
                                    @endforeach
                                @else
                                    <span class="text-gray-400 text-sm">-</span>
                                @endif
                            </div>
                        </td>
                        <td class="py-3 px-4 text-right text-gray-900 dark:text-white">
                            R$ {{ number_format($pagamento->subtotal, 2, ',', '.') }}
                        </td>
                        <td class="py-3 px-4 text-right">
                            <span class="text-green-600 dark:text-green-400 font-bold">
                                R$ {{ number_format($pagamento->valor_taxa_servico, 2, ',', '.') }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right font-bold text-gray-900 dark:text-white">
                            R$ {{ number_format($pagamento->total, 2, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 dark:bg-gray-700/50">
                        <td colspan="4" class="py-4 px-4 font-bold text-gray-900 dark:text-white">
                            Total ({{ $gorjetas->count() }} pagamento(s))
                        </td>
                        <td class="py-4 px-4 text-right font-bold text-gray-900 dark:text-white">
                            R$ {{ number_format($gorjetas->sum('subtotal'), 2, ',', '.') }}
                        </td>
                        <td class="py-4 px-4 text-right">
                            <span class="text-xl font-bold text-green-600 dark:text-green-400">
                                R$ {{ number_format($gorjetas->sum('valor_taxa_servico'), 2, ',', '.') }}
                            </span>
                        </td>
                        <td class="py-4 px-4 text-right font-bold text-gray-900 dark:text-white">
                            R$ {{ number_format($gorjetas->sum('total'), 2, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif

    <!-- Resumo por Dia (Geral) -->
    @if(isset($gorjetasPorDia) && $gorjetasPorDia->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
            <svg class="w-6 h-6 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            Resumo de Gorjetas por Dia
        </h2>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Data</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Dia</th>
                        <th class="text-center py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Atendimentos</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Total Vendido</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Gorjetas (10%)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($gorjetasPorDia as $dia)
                    <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <td class="py-3 px-4 text-gray-900 dark:text-white font-medium">{{ $dia['data_formatada'] }}</td>
                        <td class="py-3 px-4 text-gray-600 dark:text-gray-400 capitalize">{{ $dia['dia_semana'] }}</td>
                        <td class="py-3 px-4 text-center">
                            <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 px-3 py-1 rounded-full text-sm font-semibold">
                                {{ $dia['quantidade'] }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right text-gray-900 dark:text-white">R$ {{ number_format($dia['total_vendido'], 2, ',', '.') }}</td>
                        <td class="py-3 px-4 text-right">
                            <span class="text-green-600 dark:text-green-400 font-bold text-lg">R$ {{ number_format($dia['total'], 2, ',', '.') }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 dark:bg-gray-700/50">
                        <td colspan="3" class="py-4 px-4 font-bold text-gray-900 dark:text-white">Total do Período</td>
                        <td class="py-4 px-4 text-right font-bold text-gray-900 dark:text-white">R$ {{ number_format($gorjetasPorDia->sum('total_vendido'), 2, ',', '.') }}</td>
                        <td class="py-4 px-4 text-right">
                            <span class="text-2xl font-bold text-green-600 dark:text-green-400">R$ {{ number_format($totalGorjetas, 2, ',', '.') }}</span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
