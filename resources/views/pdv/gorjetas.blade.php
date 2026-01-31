@extends('layouts.app')

@section('title', 'Relatório de Gorjetas')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="bg-gray-800 rounded-xl shadow-xl p-6 border border-gray-700">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center space-x-4">
                <div class="bg-gradient-to-r from-green-500 to-green-600 p-3 rounded-xl">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-white">Relatório de Gorjetas</h1>
                    <p class="text-gray-400">Taxa de Serviço (10% do Garçom)</p>
                </div>
            </div>
            <a href="{{ route('pdv.index') }}" class="text-gray-400 hover:text-white transition">
                ← Voltar ao PDV
            </a>
        </div>
    </div>

    <!-- Filtros de Data -->
    <div class="bg-gray-800 rounded-xl shadow-xl p-6 border border-gray-700">
        <form method="GET" action="{{ route('pdv.gorjetas') }}" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1">
                <label for="data_inicio" class="block text-sm font-medium text-gray-300 mb-2">Data Início</label>
                <input type="date" name="data_inicio" id="data_inicio" value="{{ $dataInicio }}"
                    class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-xl text-white focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
            <div class="flex-1">
                <label for="data_fim" class="block text-sm font-medium text-gray-300 mb-2">Data Fim</label>
                <input type="date" name="data_fim" id="data_fim" value="{{ $dataFim }}"
                    class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-xl text-white focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-xl font-bold transition shadow-xl">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Filtrar
            </button>
        </form>
    </div>

    <!-- Cards de Resumo Principal -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total de Gorjetas -->
        <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-xl shadow-xl p-6 border border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Total de Gorjetas (10%)</p>
                    <p class="text-3xl font-bold text-white mt-2">R$ {{ number_format($totalGorjetas, 2, ',', '.') }}</p>
                </div>
                <div class="bg-green-500/30 p-3 rounded-xl">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Vendido -->
        <div class="bg-gradient-to-br from-yellow-600 to-yellow-700 rounded-xl shadow-xl p-6 border border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm font-medium">Total Vendido (Base)</p>
                    <p class="text-3xl font-bold text-white mt-2">R$ {{ number_format($estatisticas['total_vendido'], 2, ',', '.') }}</p>
                </div>
                <div class="bg-yellow-500/30 p-3 rounded-xl">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Quantidade de Atendimentos -->
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl shadow-xl p-6 border border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Atendimentos com Taxa</p>
                    <p class="text-3xl font-bold text-white mt-2">{{ $quantidadeAtendimentos }}</p>
                </div>
                <div class="bg-blue-500/30 p-3 rounded-xl">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Ticket Médio -->
        <div class="bg-gradient-to-br from-purple-600 to-purple-700 rounded-xl shadow-xl p-6 border border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Ticket Médio</p>
                    <p class="text-3xl font-bold text-white mt-2">R$ {{ number_format($estatisticas['ticket_medio'], 2, ',', '.') }}</p>
                </div>
                <div class="bg-purple-500/30 p-3 rounded-xl">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards de Estatísticas Secundárias -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Média de Gorjeta -->
        <div class="bg-gray-800 rounded-xl shadow-xl p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm font-medium">Média por Gorjeta</p>
                    <p class="text-2xl font-bold text-green-400 mt-2">R$ {{ number_format($estatisticas['media_gorjeta'], 2, ',', '.') }}</p>
                </div>
                <div class="bg-gray-700 p-3 rounded-xl">
                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Maior Gorjeta -->
        <div class="bg-gray-800 rounded-xl shadow-xl p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm font-medium">Maior Gorjeta</p>
                    <p class="text-2xl font-bold text-blue-400 mt-2">R$ {{ number_format($estatisticas['maior_gorjeta'], 2, ',', '.') }}</p>
                </div>
                <div class="bg-gray-700 p-3 rounded-xl">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Menor Gorjeta -->
        <div class="bg-gray-800 rounded-xl shadow-xl p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm font-medium">Menor Gorjeta</p>
                    <p class="text-2xl font-bold text-orange-400 mt-2">R$ {{ number_format($estatisticas['menor_gorjeta'], 2, ',', '.') }}</p>
                </div>
                <div class="bg-gray-700 p-3 rounded-xl">
                    <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumo por Dia -->
    @if($gorjetasPorDia->count() > 0)
    <div class="bg-gray-800 rounded-xl shadow-xl p-6 border border-gray-700">
        <h2 class="text-xl font-bold text-white mb-4 flex items-center">
            <svg class="w-6 h-6 mr-2 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            Resumo por Dia
        </h2>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-600">
                        <th class="text-left py-3 px-4 text-gray-300 font-semibold">Data</th>
                        <th class="text-left py-3 px-4 text-gray-300 font-semibold">Dia da Semana</th>
                        <th class="text-center py-3 px-4 text-gray-300 font-semibold">Atendimentos</th>
                        <th class="text-right py-3 px-4 text-gray-300 font-semibold">Total Vendido</th>
                        <th class="text-right py-3 px-4 text-gray-300 font-semibold">Total Gorjetas (10%)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($gorjetasPorDia as $dia)
                    <tr class="border-b border-gray-700 hover:bg-gray-700/50 transition">
                        <td class="py-3 px-4 text-white font-semibold">{{ $dia['data_formatada'] }}</td>
                        <td class="py-3 px-4 text-gray-300 capitalize">{{ $dia['dia_semana'] }}</td>
                        <td class="py-3 px-4 text-center">
                            <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                {{ $dia['quantidade'] }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right text-white">R$ {{ number_format($dia['total_vendido'], 2, ',', '.') }}</td>
                        <td class="py-3 px-4 text-right">
                            <span class="text-green-400 font-bold text-lg">R$ {{ number_format($dia['total'], 2, ',', '.') }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-700/50">
                        <td colspan="2" class="py-4 px-4 font-bold text-white">Total do Período</td>
                        <td class="py-4 px-4 text-center font-bold text-white">{{ $quantidadeAtendimentos }}</td>
                        <td class="py-4 px-4 text-right font-bold text-white">R$ {{ number_format($estatisticas['total_vendido'], 2, ',', '.') }}</td>
                        <td class="py-4 px-4 text-right">
                            <span class="text-2xl font-bold text-green-400">R$ {{ number_format($totalGorjetas, 2, ',', '.') }}</span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif

    <!-- Gorjetas por Atendente - Detalhado -->
    @if($gorjetasPorAtendentePorDia->count() > 0)
    <div class="bg-gray-800 rounded-xl shadow-xl p-6 border border-gray-700">
        <h2 class="text-xl font-bold text-white mb-4 flex items-center">
            <svg class="w-6 h-6 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            Gorjetas por Garçom - Detalhamento Diário
        </h2>

        <div class="space-y-6">
            @foreach($gorjetasPorAtendentePorDia as $atendente)
            <div class="bg-gray-700/50 rounded-xl p-5 border border-gray-600">
                <!-- Cabeçalho do Garçom -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 pb-4 border-b border-gray-600">
                    <div class="flex items-center space-x-3">
                        <div class="bg-gradient-to-r from-green-500 to-green-600 p-3 rounded-full">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">{{ $atendente['atendente'] }}</h3>
                            <p class="text-sm text-gray-400">{{ $atendente['dias']->count() }} dia(s) trabalhado(s)</p>
                        </div>
                    </div>
                    <div class="mt-3 md:mt-0 text-right">
                        <p class="text-sm text-gray-400">Total de Vendas</p>
                        <p class="text-lg font-semibold text-white">R$ {{ number_format($atendente['total_vendido_geral'], 2, ',', '.') }}</p>
                        <p class="text-sm text-gray-400 mt-1">Total em Gorjetas (10%)</p>
                        <p class="text-2xl font-bold text-green-400">R$ {{ number_format($atendente['total_geral'], 2, ',', '.') }}</p>
                    </div>
                </div>

                <!-- Tabela de dias -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-600">
                                <th class="text-left py-2 px-3 text-gray-400 text-sm font-medium">Data</th>
                                <th class="text-center py-2 px-3 text-gray-400 text-sm font-medium">Atendimentos</th>
                                <th class="text-right py-2 px-3 text-gray-400 text-sm font-medium">Vendido</th>
                                <th class="text-right py-2 px-3 text-gray-400 text-sm font-medium">Gorjeta (10%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($atendente['dias'] as $dia)
                            <tr class="border-b border-gray-700/50 hover:bg-gray-600/30 transition">
                                <td class="py-3 px-3 text-white">{{ $dia['data_formatada'] }}</td>
                                <td class="py-3 px-3 text-center">
                                    <span class="bg-blue-600/50 text-blue-200 px-2 py-1 rounded text-sm">
                                        {{ $dia['quantidade'] }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-right text-gray-300">R$ {{ number_format($dia['total_vendido'], 2, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right">
                                    <span class="text-green-400 font-semibold">R$ {{ number_format($dia['total_gorjeta'], 2, ',', '.') }}</span>
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

    <!-- Resumo Compacto por Garçom -->
    @if($gorjetasPorAtendente->count() > 0)
    <div class="bg-gray-800 rounded-xl shadow-xl p-6 border border-gray-700">
        <h2 class="text-xl font-bold text-white mb-4 flex items-center">
            <svg class="w-6 h-6 mr-2 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            Ranking de Garçons - Resumo
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($gorjetasPorAtendente as $index => $atendente)
            <div class="bg-gray-700 rounded-lg p-4 border border-gray-600 {{ $index === 0 ? 'ring-2 ring-yellow-500' : '' }}">
                @if($index === 0)
                <div class="flex justify-end mb-2">
                    <span class="bg-yellow-500 text-yellow-900 px-2 py-1 rounded text-xs font-bold">TOP 1</span>
                </div>
                @endif
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-3">
                        <div class="bg-green-600 p-2 rounded-full">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <span class="font-semibold text-white">{{ $atendente['atendente'] }}</span>
                    </div>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Atendimentos:</span>
                        <span class="text-white">{{ $atendente['quantidade'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Total Vendido:</span>
                        <span class="text-white">R$ {{ number_format($atendente['total_vendido'], 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Média/Gorjeta:</span>
                        <span class="text-blue-400">R$ {{ number_format($atendente['media_por_atendimento'], 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Maior Gorjeta:</span>
                        <span class="text-yellow-400">R$ {{ number_format($atendente['maior_gorjeta'], 2, ',', '.') }}</span>
                    </div>
                    <div class="pt-2 border-t border-gray-600">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400 font-medium">Total Gorjetas:</span>
                            <span class="text-xl font-bold text-green-400">R$ {{ number_format($atendente['total'], 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Lista de Gorjetas -->
    <div class="bg-gray-800 rounded-xl shadow-xl p-6 border border-gray-700">
        <h2 class="text-xl font-bold text-white mb-4 flex items-center">
            <svg class="w-6 h-6 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            Detalhamento das Gorjetas
        </h2>

        @if($gorjetas->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-600">
                        <th class="text-left py-3 px-4 text-gray-300 font-semibold">Data/Hora</th>
                        <th class="text-left py-3 px-4 text-gray-300 font-semibold">Mesa</th>
                        <th class="text-left py-3 px-4 text-gray-300 font-semibold">Atendente</th>
                        <th class="text-right py-3 px-4 text-gray-300 font-semibold">Subtotal</th>
                        <th class="text-right py-3 px-4 text-gray-300 font-semibold">Taxa (10%)</th>
                        <th class="text-right py-3 px-4 text-gray-300 font-semibold">Total Pago</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($gorjetas as $gorjeta)
                    <tr class="border-b border-gray-700 hover:bg-gray-700/50 transition">
                        <td class="py-3 px-4 text-white">{{ $gorjeta->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-3 px-4">
                            <span class="bg-blue-600 text-white px-2 py-1 rounded font-semibold">
                                Mesa {{ $gorjeta->mesa->numero ?? '-' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-gray-300">{{ $gorjeta->user->nome ?? 'Desconhecido' }}</td>
                        <td class="py-3 px-4 text-right text-white">R$ {{ number_format($gorjeta->subtotal, 2, ',', '.') }}</td>
                        <td class="py-3 px-4 text-right">
                            <span class="text-green-400 font-semibold">R$ {{ number_format($gorjeta->valor_taxa_servico, 2, ',', '.') }}</span>
                        </td>
                        <td class="py-3 px-4 text-right text-white font-semibold">R$ {{ number_format($gorjeta->total, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-700/50">
                        <td colspan="4" class="py-4 px-4 text-right font-bold text-white">Total de Gorjetas:</td>
                        <td class="py-4 px-4 text-right">
                            <span class="text-2xl font-bold text-green-400">R$ {{ number_format($totalGorjetas, 2, ',', '.') }}</span>
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <p class="text-gray-400 text-lg">Nenhuma gorjeta registrada no período selecionado.</p>
            <p class="text-gray-500 text-sm mt-2">Selecione outro período ou aguarde novos pagamentos com taxa de serviço.</p>
        </div>
        @endif
    </div>
</div>
@endsection
