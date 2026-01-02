@extends('layouts.app')

@section('title', 'Comprovante de Pagamento')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="bg-gray-800 rounded-xl shadow-xl p-6 border border-gray-700">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <div class="bg-gradient-to-r from-green-500 to-green-600 p-3 rounded-xl">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-white">Pagamento Aprovado</h1>
                    <p class="text-gray-400">Comprovante de Pagamento</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-400">Comprovante #{{ $pagamento->id }}</p>
                <p class="text-sm text-gray-400">{{ $pagamento->created_at->format('d/m/Y H:i:s') }}</p>
            </div>
        </div>
    </div>

    <div class="bg-gray-800 rounded-xl shadow-xl p-8 border border-gray-700 print:shadow-none">

        <!-- Informações do Pagamento -->
        <div class="border-t border-b border-gray-600 py-4 mb-6 space-y-3">
            <div class="flex justify-between">
                <span class="text-gray-400">Mesa:</span>
                <span class="font-semibold text-white text-lg">{{ $pagamento->mesa->numero }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">Atendente:</span>
                <span class="font-semibold text-gray-200">{{ $pagamento->user->nome }}</span>
            </div>
        </div>

        <!-- Itens Consumidos -->
        <div class="mb-6">
            <h2 class="font-bold text-white mb-4 text-lg">Itens Consumidos</h2>
            <div class="space-y-3">
                @foreach($pagamento->detalhes as $detalhe)
                    <div class="bg-gray-700 rounded-lg p-4 border border-gray-600">
                        <div class="text-sm font-semibold text-gray-300 mb-3">{{ $detalhe->pedido->numero_pedido }}</div>
                        @foreach($detalhe->pedido->itens as $item)
                            <div class="mb-3 pb-3 border-b border-gray-600 last:border-0">
                                <div class="flex justify-between">
                                    <div class="flex-1">
                                        <div>
                                            <span class="text-green-400 font-semibold">{{ $item->quantidade }}x</span>
                                            <span class="text-gray-200 font-medium">{{ $item->produto->nome }}</span>
                                            @if($item->produtoTamanho)
                                                <span class="text-orange-400 font-semibold">({{ $item->produtoTamanho->nome }})</span>
                                            @endif
                                        </div>
                                        @if($item->sabores->count() > 0)
                                            <div class="text-xs text-blue-400 mt-1 ml-6">
                                                <strong>Sabores:</strong> {{ $item->sabores->pluck('sabor.nome')->join(', ') }}
                                            </div>
                                        @endif
                                        @if($item->observacoes)
                                            <div class="text-xs text-yellow-400 mt-1 ml-6">
                                                <strong>Obs:</strong> {{ $item->observacoes }}
                                            </div>
                                        @endif
                                    </div>
                                    <span class="text-green-400 font-semibold ml-3">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Valores -->
        <div class="space-y-3 mb-6 bg-gray-700 rounded-lg p-4 border border-gray-600">
            <div class="flex justify-between">
                <span class="text-gray-300">Subtotal:</span>
                <span class="text-white font-semibold">R$ {{ number_format($pagamento->subtotal, 2, ',', '.') }}</span>
            </div>
            @if($pagamento->desconto > 0)
            <div class="flex justify-between">
                <span class="text-gray-300">Desconto:</span>
                <span class="text-red-400 font-semibold">- R$ {{ number_format($pagamento->valor_desconto, 2, ',', '.') }}</span>
            </div>
            @endif
            @if($pagamento->acrescimo > 0)
            <div class="flex justify-between">
                <span class="text-gray-300">Acréscimo:</span>
                <span class="text-yellow-400 font-semibold">+ R$ {{ number_format($pagamento->valor_acrescimo, 2, ',', '.') }}</span>
            </div>
            @endif
            <div class="flex justify-between text-xl font-bold pt-3 border-t border-gray-600">
                <span class="text-white">Total:</span>
                <span class="text-green-400">R$ {{ number_format($pagamento->total, 2, ',', '.') }}</span>
            </div>
        </div>

        <!-- Forma de Pagamento -->
        <div class="bg-gradient-to-r from-blue-600/20 to-blue-700/20 rounded-lg p-4 mb-6 border border-blue-500/30">
            <div class="flex justify-between items-center mb-2">
                <span class="text-gray-300 font-medium">Forma de Pagamento:</span>
                <span class="text-blue-300 font-bold uppercase">{{ str_replace('_', ' ', $pagamento->forma_pagamento) }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-300 font-medium">Valor Pago:</span>
                <span class="text-blue-300 font-bold">R$ {{ number_format($pagamento->valor_pago, 2, ',', '.') }}</span>
            </div>
            @if($pagamento->troco > 0)
            <div class="flex justify-between items-center mt-2 pt-2 border-t border-blue-500/30">
                <span class="text-gray-300 font-medium">Troco:</span>
                <span class="text-green-400 font-bold">R$ {{ number_format($pagamento->troco, 2, ',', '.') }}</span>
            </div>
            @endif
        </div>

        @if($pagamento->observacoes)
        <div class="mb-6 bg-yellow-900/20 border border-yellow-600/30 rounded-lg p-4">
            <p class="text-sm text-yellow-200"><strong>Observações:</strong> {{ $pagamento->observacoes }}</p>
        </div>
        @endif

        <!-- Rodapé -->
        <div class="text-center text-gray-400 text-sm border-t border-gray-600 pt-6">
            <p class="text-lg font-semibold text-white mb-2">Obrigado pela preferência!</p>
            <p>Volte sempre!</p>
        </div>
    </div>

    <!-- Botões (não imprime) -->
    <div class="flex gap-3 print:hidden">
        <a href="{{ route('pdv.comprovante.imprimir', $pagamento) }}" target="_blank" class="flex-1 px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white rounded-xl font-bold text-center shadow-xl transition">
            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Imprimir Térmica
        </a>
        <button onclick="window.print()" class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl font-bold shadow-xl transition">
            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Imprimir Normal
        </button>
        <a href="{{ route('pdv.index') }}" class="flex-1 px-6 py-3 bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white rounded-xl font-bold text-center shadow-xl transition">
            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Voltar ao PDV
        </a>
    </div>
</div>
@endsection
