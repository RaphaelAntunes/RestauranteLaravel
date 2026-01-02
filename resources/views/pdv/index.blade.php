@extends('layouts.app')

@section('title', 'PDV - Ponto de Venda')

@section('content')
<div class="space-y-6">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">PDV - Ponto de Venda</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($mesas as $mesa)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 hover:border-red-500 dark:hover:border-red-500 transition-colors p-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Mesa {{ $mesa->numero }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $mesa->localizacao }}</p>
                </div>
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 border border-red-200 dark:border-red-800">
                    Ocupada
                </span>
            </div>

            <div class="space-y-2 mb-4">
                @php
                    $totalMesa = $mesa->pedidos->sum('total');
                    $quantidadePedidos = $mesa->pedidos->count();
                @endphp
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    <span class="font-medium">Pedidos:</span> {{ $quantidadePedidos }}
                </p>
                <p class="text-lg font-bold text-indigo-600 dark:text-indigo-400">
                    Total: R$ {{ number_format($totalMesa, 2, ',', '.') }}
                </p>
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Pedidos:</h4>
                @foreach($mesa->pedidos as $pedido)
                    <p class="text-xs text-gray-600 dark:text-gray-300">{{ $pedido->numero_pedido }} - R$ {{ number_format($pedido->total, 2, ',', '.') }}</p>
                @endforeach
            </div>

            <div class="mt-4">
                <a href="{{ route('pdv.fechar', $mesa) }}" class="block w-full bg-green-600 hover:bg-green-700 text-white text-center px-4 py-2 rounded-lg font-medium shadow-lg transition-all">
                    Fechar Conta
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12 text-gray-500 dark:text-gray-400">
            Nenhuma mesa com pedidos em aberto
        </div>
        @endforelse
    </div>
</div>
@endsection
