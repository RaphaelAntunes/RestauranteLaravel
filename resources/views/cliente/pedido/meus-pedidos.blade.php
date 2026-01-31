@extends('layouts.cliente')

@section('title', 'Meus Pedidos')

@section('content')
<h1 class="text-3xl font-bold mb-6">Meus Pedidos</h1>

@if($pedidos->isEmpty())
    <div class="bg-white rounded-lg shadow p-8 text-center">
        <i class="fas fa-receipt text-6xl text-gray-300 mb-4"></i>
        <p class="text-gray-600 text-lg mb-4">Você ainda não fez nenhum pedido</p>
        <a href="{{ route('cliente.cardapio') }}" class="inline-block bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700">
            Ver Cardápio
        </a>
    </div>
@else
    <div class="space-y-4">
        @foreach($pedidos as $pedido)
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="font-bold text-lg">Pedido #{{ $pedido->numero_pedido }}</h3>
                        <p class="text-gray-600 text-sm">{{ $pedido->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <span class="px-3 py-1 rounded-full text-sm font-semibold
                        {{ $pedido->status === 'entregue' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $pedido->status === 'em_preparo' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $pedido->status === 'aberto' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $pedido->status === 'cancelado' ? 'bg-red-100 text-red-800' : '' }}">
                        {{ ucfirst(str_replace('_', ' ', $pedido->status)) }}
                    </span>
                </div>

                <div class="border-t pt-4 mb-4">
                    <p class="text-gray-700 mb-2">
                        <strong>{{ $pedido->itens->count() }}</strong> {{ $pedido->itens->count() === 1 ? 'item' : 'itens' }}
                    </p>
                    <p class="text-xl font-bold text-red-600">
                        R$ {{ number_format($pedido->getTotalComTaxa(), 2, ',', '.') }}
                    </p>
                </div>

                <a href="{{ route('cliente.pedido.acompanhar', $pedido->id) }}" class="text-red-600 hover:underline">
                    Ver Detalhes →
                </a>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $pedidos->links() }}
    </div>
@endif
@endsection
