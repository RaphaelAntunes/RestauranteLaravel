@extends('layouts.app')

@section('title', 'Pedidos Online / Delivery')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Pedidos Online / Delivery</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Gerencie pedidos de delivery e retirada</p>
        </div>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1">Abertos</p>
                    <p class="text-3xl font-bold">{{ $statusCounts['aberto'] }}</p>
                </div>
                <div class="bg-white/20 backdrop-blur-sm rounded-full p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-yellow-500 to-orange-500 rounded-2xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm font-medium mb-1">Em Preparo</p>
                    <p class="text-3xl font-bold">{{ $statusCounts['em_preparo'] }}</p>
                </div>
                <div class="bg-white/20 backdrop-blur-sm rounded-full p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-indigo-500 to-purple-500 rounded-2xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-indigo-100 text-sm font-medium mb-1">Prontos</p>
                    <p class="text-3xl font-bold">{{ $statusCounts['pronto'] }}</p>
                </div>
                <div class="bg-white/20 backdrop-blur-sm rounded-full p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium mb-1">Entregues</p>
                    <p class="text-3xl font-bold">{{ $statusCounts['entregue'] }}</p>
                </div>
                <div class="bg-white/20 backdrop-blur-sm rounded-full p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
        <form method="GET" action="{{ route('admin.pedidos-online.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Status
                    </label>
                    <select name="status" id="status" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm">
                        <option value="">Todos os Status</option>
                        <option value="aberto" {{ request('status') === 'aberto' ? 'selected' : '' }}>Aberto</option>
                        <option value="em_preparo" {{ request('status') === 'em_preparo' ? 'selected' : '' }}>Em Preparo</option>
                        <option value="pronto" {{ request('status') === 'pronto' ? 'selected' : '' }}>Pronto</option>
                        <option value="entregue" {{ request('status') === 'entregue' ? 'selected' : '' }}>Entregue</option>
                    </select>
                </div>

                <div>
                    <label for="tipo_pedido" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Tipo de Pedido
                    </label>
                    <select name="tipo_pedido" id="tipo_pedido" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm">
                        <option value="">Todos os Tipos</option>
                        <option value="delivery" {{ request('tipo_pedido') === 'delivery' ? 'selected' : '' }}>Delivery</option>
                        <option value="retirada" {{ request('tipo_pedido') === 'retirada' ? 'selected' : '' }}>Retirada</option>
                    </select>
                </div>

                <div>
                    <label for="data_inicio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Data Início
                    </label>
                    <input type="date" name="data_inicio" id="data_inicio" value="{{ request('data_inicio') }}" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm">
                </div>

                <div>
                    <label for="data_fim" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Data Fim
                    </label>
                    <input type="date" name="data_fim" id="data_fim" value="{{ request('data_fim') }}" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm">
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white px-6 py-3 rounded-xl shadow-lg transition-all duration-200 font-semibold">
                    Filtrar
                </button>
                <a href="{{ route('admin.pedidos-online.index') }}" class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors font-medium">
                    Limpar
                </a>
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
                                @if($pedido->status === 'aberto') bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400 border border-blue-200 dark:border-blue-800
                                @elseif($pedido->status === 'em_preparo') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400 border border-yellow-200 dark:border-yellow-800
                                @elseif($pedido->status === 'pronto') bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800
                                @elseif($pedido->status === 'entregue') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400 border border-green-200 dark:border-green-800
                                @else bg-gray-100 dark:bg-gray-900/30 text-gray-800 dark:text-gray-400 border border-gray-200 dark:border-gray-700 @endif">
                                {{ ucfirst(str_replace('_', ' ', $pedido->status)) }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $pedido->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    <span class="px-3 py-1.5 text-xs font-semibold rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                        {{ ucfirst($pedido->tipo_pedido) }}
                    </span>
                </div>

                <!-- Informações do Cliente -->
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $pedido->cliente->nome }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $pedido->cliente->celular }}</span>
                    </div>
                    @if($pedido->clienteEndereco)
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $pedido->clienteEndereco->logradouro }}, {{ $pedido->clienteEndereco->numero }}
                            @if($pedido->clienteEndereco->complemento), {{ $pedido->clienteEndereco->complemento }}@endif
                            <br>{{ $pedido->clienteEndereco->bairro }}
                        </span>
                    </div>
                    @endif
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
                        R$ {{ number_format($pedido->getTotalComTaxa(), 2, ',', '.') }}
                    </span>
                </div>
                <a href="{{ route('admin.pedidos-online.show', $pedido->id) }}" class="block w-full bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white text-center px-4 py-3 rounded-xl font-semibold shadow-lg transition-all duration-200">
                    Ver Detalhes
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">Nenhum pedido encontrado</h3>
                <p class="text-gray-600 dark:text-gray-400">Tente ajustar os filtros para encontrar pedidos</p>
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