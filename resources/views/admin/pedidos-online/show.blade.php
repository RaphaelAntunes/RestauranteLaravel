@extends('layouts.app')

@section('title', 'Pedido #' . $pedido->numero_pedido)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.pedidos-online.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Pedido #{{ $pedido->numero_pedido }}</h1>
                <span class="px-3 py-1 text-sm font-bold rounded-full
                    @if($pedido->status === 'aberto') bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400 border border-blue-200 dark:border-blue-800
                    @elseif($pedido->status === 'em_preparo') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400 border border-yellow-200 dark:border-yellow-800
                    @elseif($pedido->status === 'pronto') bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800
                    @elseif($pedido->status === 'saiu_entrega') bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-400 border border-purple-200 dark:border-purple-800
                    @elseif($pedido->status === 'entregue') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400 border border-green-200 dark:border-green-800
                    @elseif($pedido->status === 'cancelado') bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400 border border-red-200 dark:border-red-800
                    @else bg-gray-100 dark:bg-gray-900/30 text-gray-800 dark:text-gray-400 border border-gray-200 dark:border-gray-700 @endif">
                    {{ ucfirst(str_replace('_', ' ', $pedido->status)) }}
                </span>
            </div>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Criado em {{ $pedido->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <span class="px-4 py-2 text-sm font-semibold rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
            {{ ucfirst($pedido->tipo_pedido) }}
        </span>
    </div>

    @if(session('success'))
    <div class="bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded-xl">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-xl">
        {{ session('error') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Coluna Principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Itens do Pedido -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Itens do Pedido</h2>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($pedido->itens as $item)
                    <div class="p-6">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400 px-2 py-1 rounded-lg text-sm font-bold">
                                        {{ $item->quantidade }}x
                                    </span>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">
                                        {{ $item->produto_nome }}
                                        @if($item->produtoTamanho)
                                            <span class="text-gray-500 dark:text-gray-400 font-normal">({{ $item->produtoTamanho->nome }})</span>
                                        @endif
                                    </h3>
                                </div>
                                @if($item->sabores && $item->sabores->count() > 0)
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                                    <span class="font-medium">Sabores:</span> {{ $item->sabores->map(fn($s) => $s->sabor->nome ?? '')->filter()->join(', ') }}
                                </p>
                                @endif
                                @if($item->observacoes)
                                <p class="text-sm text-yellow-600 dark:text-yellow-400 mt-2">
                                    <span class="font-medium">Obs:</span> {{ $item->observacoes }}
                                </p>
                                @endif
                            </div>
                            <div class="text-right ml-4">
                                <p class="text-sm text-gray-500 dark:text-gray-400">R$ {{ number_format($item->preco_unitario, 2, ',', '.') }} un.</p>
                                <p class="font-bold text-gray-900 dark:text-white text-lg">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</p>
                            </div>
                        </div>
                        @if(!$pedido->isFinalizado() && !$pedido->isCancelado())
                        <div class="mt-4 flex gap-2">
                            <form action="{{ route('admin.pedidos-online.remover-item', $item->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja remover este item?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium">
                                    Remover
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Observações -->
            @if($pedido->observacoes)
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-2xl p-6">
                <h3 class="font-semibold text-yellow-800 dark:text-yellow-300 mb-2">Observações do Pedido</h3>
                <p class="text-yellow-700 dark:text-yellow-400">{{ $pedido->observacoes }}</p>
            </div>
            @endif
        </div>

        <!-- Coluna Lateral -->
        <div class="space-y-6">
            <!-- Informações do Cliente -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Cliente</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $pedido->cliente->nome }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $pedido->cliente->celular }}</p>
                        </div>
                    </div>

                    @if($pedido->clienteEndereco)
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Endereço de Entrega</h4>
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                <p>{{ $pedido->clienteEndereco->logradouro }}, {{ $pedido->clienteEndereco->numero }}</p>
                                @if($pedido->clienteEndereco->complemento)
                                <p>{{ $pedido->clienteEndereco->complemento }}</p>
                                @endif
                                <p>{{ $pedido->clienteEndereco->bairro }}</p>
                                @if($pedido->clienteEndereco->referencia)
                                <p class="mt-1 text-yellow-600 dark:text-yellow-400">Ref: {{ $pedido->clienteEndereco->referencia }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Resumo Financeiro -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Resumo</h2>
                </div>
                <div class="p-6 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                        <span class="text-gray-900 dark:text-white font-medium">R$ {{ number_format($pedido->total, 2, ',', '.') }}</span>
                    </div>
                    @if($pedido->taxa_entrega > 0)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Taxa de Entrega</span>
                        <span class="text-gray-900 dark:text-white font-medium">R$ {{ number_format($pedido->taxa_entrega, 2, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="pt-3 border-t border-gray-200 dark:border-gray-700 flex justify-between">
                        <span class="font-semibold text-gray-900 dark:text-white">Total</span>
                        <span class="text-2xl font-bold text-red-600 dark:text-red-400">R$ {{ number_format($pedido->getTotalComTaxa(), 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Alterar Status -->
            @if(!$pedido->isFinalizado() && !$pedido->isCancelado())
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Alterar Status</h2>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.pedidos-online.status', $pedido->id) }}" method="POST">
                        @csrf
                        <div class="space-y-3">
                            <select name="status" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm">
                                <option value="aberto" {{ $pedido->status === 'aberto' ? 'selected' : '' }}>Aberto</option>
                                <option value="em_preparo" {{ $pedido->status === 'em_preparo' ? 'selected' : '' }}>Em Preparo</option>
                                <option value="pronto" {{ $pedido->status === 'pronto' ? 'selected' : '' }}>Pronto</option>
                                @if($pedido->tipo_pedido === 'delivery')
                                <option value="saiu_entrega" {{ $pedido->status === 'saiu_entrega' ? 'selected' : '' }}>Saiu para Entrega</option>
                                @endif
                                <option value="entregue" {{ $pedido->status === 'entregue' ? 'selected' : '' }}>Entregue</option>
                                <option value="cancelado" {{ $pedido->status === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                            </select>
                            <button type="submit" class="w-full bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white px-4 py-3 rounded-xl font-semibold shadow-lg transition-all duration-200">
                                Atualizar Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
