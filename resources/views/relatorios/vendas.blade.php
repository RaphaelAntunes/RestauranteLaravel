@extends('layouts.app')

@section('title', 'Relatório de Vendas')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Relatório de Vendas</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Análise detalhada das vendas por período</p>
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

    <!-- Cards de Resumo -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Faturamento Total</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">R$ {{ number_format($faturamento, 2, ',', '.') }}</p>
                </div>
                <div class="bg-green-100 dark:bg-green-900/30 p-3 rounded-lg">
                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total de Pedidos</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalPedidos }}</p>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900/30 p-3 rounded-lg">
                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Ticket Médio</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">R$ {{ number_format($ticketMedio, 2, ',', '.') }}</p>
                </div>
                <div class="bg-purple-100 dark:bg-purple-900/30 p-3 rounded-lg">
                    <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Vendas por Tipo de Pedido -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Vendas por Tipo de Pedido</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Mesa -->
            <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-6 border border-green-200 dark:border-green-800">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-green-100 dark:bg-green-900/40 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-semibold text-green-700 dark:text-green-300 bg-green-200 dark:bg-green-800 px-2 py-1 rounded-full">
                        {{ $tiposVenda['mesa']['quantidade'] }} {{ $tiposVenda['mesa']['quantidade'] == 1 ? 'pedido' : 'pedidos' }}
                    </span>
                </div>
                <h3 class="text-lg font-semibold text-green-900 dark:text-green-100">Mesa</h3>
                <p class="text-2xl font-bold text-green-700 dark:text-green-300 mt-1">
                    R$ {{ number_format($tiposVenda['mesa']['total'], 2, ',', '.') }}
                </p>
                @if($faturamento > 0)
                    <p class="text-xs text-green-600 dark:text-green-400 mt-2">
                        {{ number_format(($tiposVenda['mesa']['total'] / $faturamento) * 100, 1) }}% do total
                    </p>
                @endif
            </div>

            <!-- Delivery -->
            <div class="bg-purple-50 dark:bg-purple-900/20 rounded-xl p-6 border border-purple-200 dark:border-purple-800">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-purple-100 dark:bg-purple-900/40 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-semibold text-purple-700 dark:text-purple-300 bg-purple-200 dark:bg-purple-800 px-2 py-1 rounded-full">
                        {{ $tiposVenda['delivery']['quantidade'] }} {{ $tiposVenda['delivery']['quantidade'] == 1 ? 'pedido' : 'pedidos' }}
                    </span>
                </div>
                <h3 class="text-lg font-semibold text-purple-900 dark:text-purple-100">Delivery</h3>
                <p class="text-2xl font-bold text-purple-700 dark:text-purple-300 mt-1">
                    R$ {{ number_format($tiposVenda['delivery']['total'], 2, ',', '.') }}
                </p>
                @if($faturamento > 0)
                    <p class="text-xs text-purple-600 dark:text-purple-400 mt-2">
                        {{ number_format(($tiposVenda['delivery']['total'] / $faturamento) * 100, 1) }}% do total
                    </p>
                @endif
            </div>

            <!-- Retirada -->
            <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-xl p-6 border border-indigo-200 dark:border-indigo-800">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-indigo-100 dark:bg-indigo-900/40 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-semibold text-indigo-700 dark:text-indigo-300 bg-indigo-200 dark:bg-indigo-800 px-2 py-1 rounded-full">
                        {{ $tiposVenda['retirada']['quantidade'] }} {{ $tiposVenda['retirada']['quantidade'] == 1 ? 'pedido' : 'pedidos' }}
                    </span>
                </div>
                <h3 class="text-lg font-semibold text-indigo-900 dark:text-indigo-100">Retirada</h3>
                <p class="text-2xl font-bold text-indigo-700 dark:text-indigo-300 mt-1">
                    R$ {{ number_format($tiposVenda['retirada']['total'], 2, ',', '.') }}
                </p>
                @if($faturamento > 0)
                    <p class="text-xs text-indigo-600 dark:text-indigo-400 mt-2">
                        {{ number_format(($tiposVenda['retirada']['total'] / $faturamento) * 100, 1) }}% do total
                    </p>
                @endif
            </div>
        </div>
    </div>

    <!-- Vendas por Dia -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Vendas por Dia</h2>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Data</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Quantidade</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendasPorDia as $venda)
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <td class="py-3 px-4 text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($venda->data)->format('d/m/Y') }}</td>
                            <td class="py-3 px-4 text-right text-gray-900 dark:text-white">{{ $venda->quantidade }}</td>
                            <td class="py-3 px-4 text-right font-semibold text-green-600 dark:text-green-400">R$ {{ number_format($venda->total, 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-8 text-center text-gray-500 dark:text-gray-400">Nenhuma venda encontrada no período</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Vendas por Forma de Pagamento -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Vendas por Forma de Pagamento</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse($vendasPorFormaPagamento as $forma)
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ ucfirst($forma->forma_pagamento) }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">R$ {{ number_format($forma->total, 2, ',', '.') }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $forma->quantidade }} {{ $forma->quantidade == 1 ? 'venda' : 'vendas' }}</p>
                </div>
            @empty
                <div class="col-span-4 text-center text-gray-500 dark:text-gray-400 py-8">
                    Nenhuma venda encontrada
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
