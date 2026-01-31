@extends('layouts.app')

@section('title', 'Pedidos')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Pedidos do Restaurante</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Gerencie todos os pedidos</p>
        </div>
        <a href="{{ route('pedidos.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white font-semibold rounded-xl shadow-lg shadow-red-500/30 hover:shadow-red-500/50 transition-all duration-200">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Novo Pedido
        </a>
    </div>

    <!-- Filtros -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
        <form method="GET" action="{{ route('pedidos.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Data Início -->
                <div>
                    <label for="data_inicio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Data Início
                    </label>
                    <input type="date"
                           name="data_inicio"
                           id="data_inicio"
                           value="{{ request('data_inicio') }}"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm">
                </div>

                <!-- Data Fim -->
                <div>
                    <label for="data_fim" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Data Fim
                    </label>
                    <input type="date"
                           name="data_fim"
                           id="data_fim"
                           value="{{ request('data_fim') }}"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm">
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Status
                    </label>
                    <select name="status"
                            id="status"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm">
                        <option value="">Todos os status</option>
                        <option value="aberto" {{ request('status') == 'aberto' ? 'selected' : '' }}>Aberto</option>
                        <option value="em_preparo" {{ request('status') == 'em_preparo' ? 'selected' : '' }}>Em Preparo</option>
                        <option value="pronto" {{ request('status') == 'pronto' ? 'selected' : '' }}>Pronto</option>
                        <option value="entregue" {{ request('status') == 'entregue' ? 'selected' : '' }}>Entregue</option>
                        <option value="finalizado" {{ request('status') == 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                        <option value="cancelado" {{ request('status') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>

                <!-- Botões -->
                <div class="flex items-end gap-2">
                    <button type="submit"
                            class="flex-1 bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white px-4 py-3 rounded-xl shadow-lg transition-all duration-200 font-semibold">
                        Filtrar
                    </button>
                    <a href="{{ route('pedidos.index') }}"
                       class="px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors font-medium">
                        Limpar
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Cards de Pedidos -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($pedidos as $pedido)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-200 dark:border-gray-700 overflow-hidden transform hover:-translate-y-1">
            <!-- Header do Card -->
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-2xl font-bold text-gray-900 dark:text-white">#{{ $pedido->numero_pedido }}</span>
                            <span class="px-3 py-1 text-xs font-bold rounded-full
                                @if($pedido->status == 'aberto') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400 border border-yellow-200 dark:border-yellow-800
                                @elseif($pedido->status == 'em_preparo') bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400 border border-blue-200 dark:border-blue-800
                                @elseif($pedido->status == 'pronto') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400 border border-green-200 dark:border-green-800
                                @elseif($pedido->status == 'finalizado') bg-gray-100 dark:bg-gray-900/30 text-gray-800 dark:text-gray-400 border border-gray-200 dark:border-gray-700
                                @else bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400 border border-red-200 dark:border-red-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $pedido->status)) }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $pedido->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>

                <!-- Informações da Mesa e Garçom -->
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $pedido->mesa ? 'Mesa ' . $pedido->mesa->numero : 'Delivery' }}</span>
                        @if($pedido->mesa && $pedido->mesa->localizacao)
                        <span class="text-sm text-gray-500 dark:text-gray-400">({{ $pedido->mesa->localizacao }})</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $pedido->user ? $pedido->user->nome : 'Sistema' }}</span>
                    </div>
                </div>
            </div>

            <!-- Itens do Pedido -->
            <div class="p-6 bg-gray-50 dark:bg-gray-900/50">
                <div class="space-y-2 mb-4">
                    @foreach($pedido->itens->take(3) as $item)
                    <div class="flex justify-between items-start text-sm">
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 dark:text-white">
                                {{ $item->quantidade }}x {{ $item->produto_nome }}
                                @if($item->produtoTamanho)
                                    <span class="text-gray-500">({{ $item->produtoTamanho->nome }})</span>
                                @endif
                            </p>
                            @if($item->sabores && $item->sabores->count() > 0)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Sabores: {{ $item->sabores->map(fn($s) => $s->sabor->nome ?? '')->filter()->join(', ') }}
                            </p>
                            @endif
                            @if($item->observacoes)
                            <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-1 italic">
                                Obs: {{ $item->observacoes }}
                            </p>
                            @endif
                        </div>
                        <span class="font-semibold text-gray-900 dark:text-white ml-2">
                            R$ {{ number_format($item->subtotal, 2, ',', '.') }}
                        </span>
                    </div>
                    @endforeach
                    @if($pedido->itens->count() > 3)
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        +{{ $pedido->itens->count() - 3 }} item(ns)
                    </p>
                    @endif
                </div>

                @if($pedido->observacoes)
                <div class="mt-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                    <p class="text-xs font-medium text-yellow-800 dark:text-yellow-300">Observação:</p>
                    <p class="text-xs text-yellow-700 dark:text-yellow-400 mt-1">{{ $pedido->observacoes }}</p>
                </div>
                @endif
            </div>

            <!-- Footer do Card -->
            <div class="p-6 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Total:</span>
                    <span class="text-2xl font-bold text-red-600 dark:text-red-400">
                        R$ {{ number_format($pedido->total, 2, ',', '.') }}
                    </span>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('pedidos.show', $pedido) }}" class="flex-1 bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white text-center px-4 py-3 rounded-xl font-semibold shadow-lg transition-all duration-200">
                        Ver Detalhes
                    </a>
                    @if(in_array($pedido->status, ['aberto', 'em_preparo']))
                    <a href="{{ route('pedidos.edit', $pedido) }}" class="px-4 py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-semibold transition-all duration-200">
                        Editar
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">Nenhum pedido encontrado</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Tente ajustar os filtros para encontrar pedidos</p>
                <a href="{{ route('pedidos.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white rounded-xl font-semibold shadow-lg shadow-red-500/30 hover:shadow-red-500/50 transition-all duration-200">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Criar Pedido
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Paginação -->
    @if($pedidos->hasPages())
    <div class="mt-6">
        {{ $pedidos->links() }}
    </div>
    @endif
</div>
@endsection