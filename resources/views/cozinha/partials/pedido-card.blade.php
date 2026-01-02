<div class="pedido-card bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-4 @if($tipo == 'novo') novo-pedido border-2 border-yellow-400 dark:border-yellow-500 @endif">
    <!-- Header do Card -->
    <div class="flex justify-between items-start mb-3">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $pedido->numero_pedido }}</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">Mesa {{ $pedido->mesa->numero }}</p>
        </div>
        <span class="text-xs text-gray-500 dark:text-gray-400">
            {{ $pedido->data_abertura->format('H:i') }}
        </span>
    </div>

    <!-- Itens do Pedido -->
    <div class="space-y-2 mb-4">
        @foreach($pedido->itens as $item)
        <div class="flex justify-between items-start py-2 border-b border-gray-100 dark:border-gray-700">
            <div class="flex-1">
                <p class="font-medium text-gray-900 dark:text-white">
                    <span class="inline-block bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300 rounded-full px-2 py-0.5 text-xs font-bold mr-1 border border-indigo-200 dark:border-indigo-800">
                        {{ $item->quantidade }}x
                    </span>
                    {{ $item->produto->nome }}
                    @if($item->produtoTamanho)
                        <span class="text-orange-600 dark:text-orange-400 font-bold">({{ $item->produtoTamanho->nome }})</span>
                    @endif
                </p>
                @if($item->sabores->count() > 0)
                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-1 font-semibold">
                        <strong>Sabores:</strong> {{ $item->sabores->pluck('sabor.nome')->join(', ') }}
                    </p>
                @endif
                @if($item->observacoes)
                    <p class="text-xs text-red-600 dark:text-red-400 mt-1">
                        <strong>OBS:</strong> {{ $item->observacoes }}
                    </p>
                @endif
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $item->produto->categoria->nome }}</p>
            </div>
            <span class="text-xs text-gray-600 dark:text-gray-400 ml-2">
                {{ $item->produto->tempo_preparo ?? '15' }} min
            </span>
        </div>
        @endforeach
    </div>

    @if($pedido->observacoes)
    <div class="bg-yellow-50 dark:bg-yellow-900/30 border-l-4 border-yellow-400 dark:border-yellow-500 p-2 mb-3">
        <p class="text-xs text-yellow-800 dark:text-yellow-300"><strong>Observação Geral:</strong> {{ $pedido->observacoes }}</p>
    </div>
    @endif

    <!-- Botões de Ação -->
    <div class="flex gap-2">
        @if($tipo == 'novo')
            <button
                onclick="iniciarPreparo({{ $pedido->id }})"
                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition"
            >
                Iniciar Preparo
            </button>
        @elseif($tipo == 'preparo')
            <button
                onclick="marcarPronto({{ $pedido->id }})"
                class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition"
            >
                Marcar Pronto
            </button>
        @elseif($tipo == 'pronto')
            <button
                onclick="entregar({{ $pedido->id }})"
                class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition"
            >
                Entregar
            </button>
        @endif
    </div>
</div>
