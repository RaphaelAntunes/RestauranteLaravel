@extends('layouts.app')

@section('title', 'Produtos Mais Vendidos')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Produtos Mais Vendidos</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Ranking dos produtos mais vendidos</p>
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

    <!-- Tabela de Produtos -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Posição</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Produto</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Categoria</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Qtd Vendida</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Faturamento</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produtos as $index => $produto)
                        <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="py-3 px-4">
                                <div class="flex items-center justify-center w-8 h-8 rounded-full
                                    @if($index == 0) bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400
                                    @elseif($index == 1) bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400
                                    @elseif($index == 2) bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400
                                    @else bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-500
                                    @endif font-bold">
                                    {{ $index + 1 }}
                                </div>
                            </td>
                            <td class="py-3 px-4 font-medium text-gray-900 dark:text-white">{{ $produto->nome }}</td>
                            <td class="py-3 px-4 text-gray-600 dark:text-gray-400">{{ $produto->categoria }}</td>
                            <td class="py-3 px-4 text-right font-semibold text-gray-900 dark:text-white">{{ $produto->quantidade_vendida }}</td>
                            <td class="py-3 px-4 text-right font-semibold text-green-600 dark:text-green-400">R$ {{ number_format($produto->faturamento, 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-500 dark:text-gray-400">Nenhum produto vendido no período</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
