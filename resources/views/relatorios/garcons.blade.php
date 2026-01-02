@extends('layouts.app')

@section('title', 'Desempenho dos Garçons')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Desempenho dos Garçons</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Ranking de desempenho por garçom</p>
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

    <!-- Ranking de Garçons -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($garcons as $index => $garcom)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border-2
                @if($index == 0) border-yellow-400 dark:border-yellow-500
                @elseif($index == 1) border-gray-300 dark:border-gray-600
                @elseif($index == 2) border-orange-400 dark:border-orange-500
                @else border-gray-200 dark:border-gray-700
                @endif p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg
                            @if($index == 0) bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400
                            @elseif($index == 1) bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400
                            @elseif($index == 2) bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400
                            @else bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-500
                            @endif">
                            {{ $index + 1 }}º
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-white">{{ $garcom->nome }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Garçom</p>
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

                <div class="space-y-3">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Faturamento Total</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                            R$ {{ number_format($garcom->faturamento, 2, ',', '.') }}
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Total de Pedidos</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">
                            {{ $garcom->total_pedidos }}
                        </p>
                    </div>

                    @if($garcom->total_pedidos > 0)
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Ticket Médio</p>
                            <p class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                R$ {{ number_format($garcom->faturamento / $garcom->total_pedidos, 2, ',', '.') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-3 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <p class="text-gray-500 dark:text-gray-400">Nenhum garçom com vendas no período</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
