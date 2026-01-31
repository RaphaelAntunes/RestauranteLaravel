@extends('layouts.cliente')

@section('title', 'Acompanhar Pedido')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">Pedido #{{ $pedido->numero_pedido }}</h1>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="font-bold text-xl mb-4">Status do Pedido</h3>

        <div class="flex justify-between items-center mb-8">
            @php
                $statuses = ['aberto', 'em_preparo', 'pronto', 'saiu_entrega', 'entregue'];
                $currentIndex = array_search($pedido->status, $statuses);
                if ($currentIndex === false) $currentIndex = -1;

                $timestamps = [
                    'aberto' => $pedido->data_abertura,
                    'em_preparo' => $pedido->em_preparo_at,
                    'pronto' => $pedido->pronto_at,
                    'saiu_entrega' => $pedido->saiu_entrega_at,
                    'entregue' => $pedido->entregue_at ?? $pedido->data_finalizacao,
                ];
            @endphp

            @foreach(['aberto' => 'Recebido', 'em_preparo' => 'Preparando', 'pronto' => 'Pronto', 'saiu_entrega' => 'Saiu', 'entregue' => 'Entregue'] as $key => $label)
                @php
                    $statusIndex = array_search($key, $statuses);
                    $isCompleted = $statusIndex <= $currentIndex;
                    $isCurrent = $statusIndex == $currentIndex;
                    $timestamp = $timestamps[$key] ?? null;
                @endphp
                <div class="flex flex-col items-center">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center {{ $isCompleted ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-600' }} {{ $isCurrent ? 'ring-4 ring-green-200' : '' }}">
                        @if($isCompleted)
                            <i class="fas fa-check"></i>
                        @else
                            <span class="text-lg font-bold">{{ $statusIndex + 1 }}</span>
                        @endif
                    </div>
                    <span class="text-sm mt-2 font-medium {{ $isCurrent ? 'text-green-600' : '' }}">{{ $label }}</span>
                    @if($timestamp)
                        <span class="text-xs text-gray-500">{{ $timestamp->format('H:i') }}</span>
                    @endif
                </div>
            @endforeach
        </div>

        @if($pedido->previsao_entrega)
            <p class="text-center text-gray-600">
                <i class="fas fa-clock"></i> Previsão de entrega: {{ $pedido->previsao_entrega->format('H:i') }}
            </p>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="font-bold text-xl mb-4">Itens do Pedido</h3>

        @foreach($pedido->itens as $item)
            <div class="flex justify-between py-3 border-b">
                <div>
                    <span class="font-semibold">{{ $item->quantidade }}x {{ $item->produto->nome ?? $item->produto_nome }}</span>
                    @if($item->produtoTamanho)
                        <span class="text-gray-600 text-sm">({{ $item->produtoTamanho->nome }})</span>
                    @endif
                </div>
                <span>R$ {{ number_format($item->subtotal, 2, ',', '.') }}</span>
            </div>
        @endforeach

        <div class="mt-4 space-y-2">
            <div class="flex justify-between">
                <span>Subtotal:</span>
                <span>R$ {{ number_format($pedido->total, 2, ',', '.') }}</span>
            </div>
            @if($pedido->taxa_entrega > 0)
                <div class="flex justify-between">
                    <span>Taxa de Entrega:</span>
                    <span>R$ {{ number_format($pedido->taxa_entrega, 2, ',', '.') }}</span>
                </div>
            @endif
            <div class="flex justify-between font-bold text-lg border-t pt-2">
                <span>Total:</span>
                <span class="text-red-600">R$ {{ number_format($pedido->getTotalComTaxa(), 2, ',', '.') }}</span>
            </div>
        </div>
    </div>

    @if($pedido->isDelivery() && $pedido->clienteEndereco)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-bold text-xl mb-4">Endereço de Entrega</h3>
            <p class="text-gray-700">{{ $pedido->clienteEndereco->getEnderecoCompleto() }}</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
    setInterval(function() {
        location.reload();
    }, 30000);
</script>
@endpush
@endsection
