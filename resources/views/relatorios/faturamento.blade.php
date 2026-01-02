@extends('layouts.app')

@section('title', 'Faturamento Mensal')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Faturamento Mensal</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Análise de faturamento por mês</p>
        </div>
        <a href="{{ route('relatorios.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
            ← Voltar
        </a>
    </div>

    <!-- Filtro de Ano -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <form method="GET" class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ano</label>
                <select name="ano" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                    @for($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}" {{ $ano == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">
                Filtrar
            </button>
        </form>
    </div>

    <!-- Total Anual -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-xl shadow-xl p-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm mb-1">Faturamento Total de {{ $ano }}</p>
                <p class="text-4xl font-bold">R$ {{ number_format($totalAnual, 2, ',', '.') }}</p>
            </div>
            <div class="bg-white/20 p-4 rounded-lg">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Faturamento por Mês -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Faturamento por Mês</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($meses as $mes)
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                            {{ ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'][$mes['mes'] - 1] }}
                        </p>
                        <span class="text-xs text-gray-500 dark:text-gray-500">{{ $mes['quantidade'] }} vendas</span>
                    </div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">R$ {{ number_format($mes['total'], 2, ',', '.') }}</p>
                    @if($mes['quantidade'] > 0)
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Ticket médio: R$ {{ number_format($mes['ticket_medio'], 2, ',', '.') }}
                        </p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Gráfico Visual Simples -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Visualização Mensal</h2>
        <div class="space-y-3">
            @php $maxTotal = $meses->max('total') @endphp
            @foreach($meses as $mes)
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            {{ ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'][$mes['mes'] - 1] }}
                        </span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">
                            R$ {{ number_format($mes['total'], 2, ',', '.') }}
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-3 rounded-full transition-all duration-500"
                             style="width: {{ $maxTotal > 0 ? ($mes['total'] / $maxTotal * 100) : 0 }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
