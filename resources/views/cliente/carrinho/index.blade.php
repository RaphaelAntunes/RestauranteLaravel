@extends('layouts.cliente')

@section('title', 'Carrinho')

@section('content')
<h1 class="text-3xl font-bold mb-6">Meu Carrinho</h1>

@if($itens->isEmpty())
    <div class="bg-white rounded-lg shadow p-8 text-center">
        <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-4"></i>
        <p class="text-gray-600 text-lg mb-4">Seu carrinho está vazio</p>
        <a href="{{ route('cliente.cardapio') }}" class="inline-block bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700">
            Ver Cardápio
        </a>
    </div>
@else
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            @foreach($itens as $item)
                <div class="bg-white rounded-lg shadow mb-4 p-4">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="font-bold text-lg">{{ $item->produto->nome }}</h3>
                            @if($item->produtoTamanho)
                                <p class="text-gray-600 text-sm">Tamanho: {{ $item->produtoTamanho->nome }}</p>
                            @endif
                            @if($item->sabores->count() > 0)
                                <p class="text-gray-600 text-sm">Sabores: {{ $item->sabores->pluck('sabor.nome')->join(', ') }}</p>
                            @endif
                            <p class="text-gray-800 font-semibold mt-2">R$ {{ number_format($item->preco_unitario, 2, ',', '.') }}</p>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="text-gray-700">Qtd: {{ $item->quantidade }}</span>

                            <form action="{{ route('cliente.carrinho.remover', $item->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6 sticky top-4">
                <h3 class="font-bold text-xl mb-4">Resumo</h3>

                <div class="space-y-2 mb-4">
                    <div class="flex justify-between">
                        <span>Subtotal:</span>
                        <span>R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Taxa de Entrega:</span>
                        <span>R$ {{ number_format($taxaEntrega, 2, ',', '.') }}</span>
                    </div>
                    <div class="border-t pt-2 flex justify-between font-bold text-lg">
                        <span>Total:</span>
                        <span class="text-red-600">R$ {{ number_format($total, 2, ',', '.') }}</span>
                    </div>
                </div>

                <a href="{{ route('cliente.checkout') }}" class="block w-full bg-red-600 text-white text-center py-3 rounded-lg hover:bg-red-700 font-semibold">
                    Finalizar Pedido
                </a>

                <a href="{{ route('cliente.cardapio') }}" class="block w-full text-center text-gray-600 mt-3 hover:text-gray-800">
                    Adicionar mais itens
                </a>
            </div>
        </div>
    </div>
@endif
@endsection
