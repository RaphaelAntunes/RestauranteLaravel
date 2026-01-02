@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Bem-vindo, {{ $user->nome }}!</p>
        </div>
        <div class="text-sm text-gray-600 dark:text-gray-400">
            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Pedidos Abertos -->
        <div class="dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-shadow border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pedidos Abertos</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['pedidos_abertos'] }}</p>
                </div>
                <div class="flex-shrink-0 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-3 shadow-lg shadow-blue-500/30">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Mesas Ocupadas -->
        <div class="dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-shadow border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Mesas Ocupadas</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['mesas_ocupadas'] }}</p>
                </div>
                <div class="flex-shrink-0 bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-3 shadow-lg shadow-green-500/30">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Faturamento Hoje -->
        <div class="dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-shadow border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Faturamento Hoje</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">R$ {{ number_format($stats['total_hoje'], 2, ',', '.') }}</p>
                </div>
                <div class="flex-shrink-0 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-3 shadow-lg shadow-yellow-500/30">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Produtos Ativos -->
        <div class="dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-shadow border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Produtos Ativos</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['produtos_ativos'] }}</p>
                </div>
                <div class="flex-shrink-0 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-3 shadow-lg shadow-purple-500/30">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Pedidos Recentes -->
        <div class="dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Pedidos Recentes</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4 max-h-96 overflow-y-auto pr-2">
                    @forelse($pedidos_recentes as $pedido)
                        <div onclick="abrirModalPedido({{ $pedido->id }})" class="cursor-pointer flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4  dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700  dark:hover:bg-gray-900/70 hover:border-red-400 dark:hover:border-red-500 transition-all duration-200 shadow-sm hover:shadow-md">
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-gray-900 dark:text-white text-sm sm:text-base break-all">{{ $pedido->numero_pedido }}</p>
                                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 truncate">Mesa {{ $pedido->mesa->numero }} - {{ $pedido->user ? $pedido->user->nome : 'Sistema' }}</p>
                            </div>
                            <div class="text-left sm:text-right flex-shrink-0 w-full sm:w-auto">
                                <span class="inline-flex px-3 py-1.5 text-xs font-bold rounded-full border shadow-sm whitespace-nowrap
                                    @if($pedido->status == 'aberto') bg-yellow-100 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-300 border-yellow-200 dark:border-yellow-800
                                    @elseif($pedido->status == 'em_preparo') bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-300 border-blue-200 dark:border-blue-800
                                    @elseif($pedido->status == 'pronto') bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-300 border-green-200 dark:border-green-800
                                    @else bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-300 border-gray-200 dark:border-gray-700
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $pedido->status)) }}
                                </span>
                                <p class="text-sm font-bold text-red-600 dark:text-red-400 mt-1">R$ {{ number_format($pedido->total, 2, ',', '.') }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400 text-center py-8">Nenhum pedido recente</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Mesas Ocupadas -->
        <div class="dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Mesas Ocupadas</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4 max-h-96 overflow-y-auto pr-2">
                    @forelse($mesas_ocupadas as $mesa)
                        <div onclick="abrirModalMesa({{ $mesa->id }})" class="cursor-pointer flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700 dark:hover:bg-gray-900/70 hover:border-red-400 dark:hover:border-red-500 transition-all duration-200 shadow-sm hover:shadow-md">
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-gray-900 dark:text-white">Mesa {{ $mesa->numero }}</p>
                                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 truncate">{{ $mesa->localizacao }} - Capacidade: {{ $mesa->capacidade }}</p>
                            </div>
                            <div class="text-left sm:text-right flex-shrink-0">
                                <span class="inline-flex px-3 py-1.5 text-xs font-bold rounded-full bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-300 border border-red-200 dark:border-red-800 shadow-sm whitespace-nowrap">
                                    {{ $mesa->pedidos_count }} pedido(s)
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400 text-center py-8">Nenhuma mesa ocupada</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    @if($user->isAdmin() || $user->isGarcom())
    <div class="dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Ações Rápidas</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('pedidos.create') }}" class="flex items-center justify-center bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 transition-all duration-200">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Novo Pedido
            </a>
            <a href="{{ route('mesas.index') }}" class="flex items-center justify-center bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg shadow-green-500/30 hover:shadow-green-500/50 transition-all duration-200">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Gerenciar Mesas
            </a>
            <a href="{{ route('produtos.create') }}" class="flex items-center justify-center bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg shadow-purple-500/30 hover:shadow-purple-500/50 transition-all duration-200">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Novo Produto
            </a>
        </div>
    </div>
    @endif
</div>

@include('components.dashboard-modals')

@endsection
