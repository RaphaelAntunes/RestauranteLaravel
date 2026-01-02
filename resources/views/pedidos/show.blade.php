@extends('layouts.app')

@section('title', 'Detalhes do Pedido')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center gap-4">
        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-white break-words">Pedido {{ $pedido->numero_pedido }}</h1>
        <a href="{{ route('pedidos.index') }}" class="text-gray-300 hover:text-white whitespace-nowrap flex-shrink-0">← Voltar</a>
    </div>

    <div class="bg-gray-800 rounded-lg shadow-lg p-6 border border-gray-700">
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <p class="text-sm text-gray-400">Mesa</p>
                <p class="font-semibold text-gray-100">Mesa {{ $pedido->mesa->numero }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-400">Garçom</p>
                <p class="font-semibold text-gray-100 truncate">{{ $pedido->user ? $pedido->user->nome : 'Sistema' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-400">Status</p>
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                    @if($pedido->status == 'aberto') bg-yellow-100 text-yellow-800
                    @elseif($pedido->status == 'em_preparo') bg-blue-100 text-blue-800
                    @elseif($pedido->status == 'pronto') bg-green-100 text-green-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst(str_replace('_', ' ', $pedido->status)) }}
                </span>
            </div>
            <div>
                <p class="text-sm text-gray-400">Data</p>
                <p class="font-semibold text-gray-100">{{ $pedido->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        @if($pedido->observacoes)
        <div class="mb-6 p-4 bg-yellow-900/30 rounded-lg border border-yellow-700">
            <p class="text-sm font-medium text-yellow-100">Observações:</p>
            <p class="text-sm text-yellow-200">{{ $pedido->observacoes }}</p>
        </div>
        @endif

        <h3 class="text-lg font-semibold mb-4 text-gray-100">Itens do Pedido</h3>
        <table class="w-full">
            <thead class="bg-gray-700">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-300">Produto</th>
                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-300">Qtd</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-300">Preço Unit.</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-300">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                @foreach($pedido->itens as $item)
                <tr class="hover:border-red-500 transition-colors">
                    <td class="px-4 py-3">
                        <p class="font-medium text-gray-100">{{ $item->produto->nome }}</p>
                        @if($item->observacoes)
                        <p class="text-xs text-red-400">OBS: {{ $item->observacoes }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center text-gray-200">{{ $item->quantidade }}</td>
                    <td class="px-4 py-3 text-right text-gray-200">R$ {{ number_format($item->preco_unitario, 2, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right font-semibold text-gray-100">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-700">
                <tr>
                    <td colspan="3" class="px-4 py-3 text-right font-bold text-gray-100">Total:</td>
                    <td class="px-4 py-3 text-right font-bold text-lg text-gray-100">R$ {{ number_format($pedido->total, 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
