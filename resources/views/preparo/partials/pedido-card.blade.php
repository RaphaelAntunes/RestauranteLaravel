<div class="pedido-card bg-white dark:bg-gray-800 rounded-lg shadow-lg border-2 p-3 @if($tipo == 'novo') novo-pedido border-yellow-400 dark:border-yellow-500 @elseif($tipo == 'preparo') border-blue-400 dark:border-blue-500 @elseif($tipo == 'pronto') border-green-400 dark:border-green-500 @elseif($tipo == 'saiu_entrega') border-purple-400 dark:border-purple-500 @endif">
    <!-- Header do Card -->
    <div class="flex justify-between items-center mb-2">
        <div class="flex items-center gap-2">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $pedido->numero_pedido }}</h3>
            <span class="text-xs px-2 py-0.5 rounded-full {{ $pedido->mesa ? 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' : 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300' }}">
                {{ $pedido->mesa ? 'Mesa ' . $pedido->mesa->numero : 'Delivery' }}
            </span>
        </div>
        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">
            {{ $pedido->data_abertura->format('H:i') }}
        </span>
    </div>

    <!-- Itens do Pedido (compacto para mobile) -->
    <div class="space-y-2 mb-3">
        @foreach($pedido->itens as $item)
        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-2">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <p class="font-semibold text-gray-900 dark:text-white text-sm">
                        <span class="inline-flex items-center justify-center bg-indigo-600 text-white rounded-full w-6 h-6 text-xs font-bold mr-1">
                            {{ $item->quantidade }}
                        </span>
                        {{ $item->produto_nome ?? ($item->produto->nome ?? 'Produto') }}
                    </p>
                    @if($item->produtoTamanho)
                        <p class="text-xs text-orange-600 dark:text-orange-400 font-bold mt-0.5">
                            Tamanho: {{ $item->produtoTamanho->nome }}
                        </p>
                    @endif
                    @if($item->sabores && $item->sabores->count() > 0)
                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-0.5">
                            <span class="font-semibold">Sabores:</span> {{ $item->sabores->pluck('sabor.nome')->filter()->join(', ') }}
                        </p>
                    @endif
                    @if($item->observacoes)
                        <p class="text-xs text-red-600 dark:text-red-400 mt-1 bg-red-50 dark:bg-red-900/30 p-1 rounded">
                            <span class="font-semibold">OBS:</span> {{ $item->observacoes }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($pedido->observacoes)
    <div class="bg-yellow-50 dark:bg-yellow-900/30 border-l-4 border-yellow-400 dark:border-yellow-500 p-2 mb-3 rounded-r">
        <p class="text-xs text-yellow-800 dark:text-yellow-300"><span class="font-semibold">Obs. Geral:</span> {{ $pedido->observacoes }}</p>
    </div>
    @endif

    <!-- Botão de Ação (grande para toque em mobile) -->
    <div class="flex gap-2">
        @if($tipo == 'novo')
            <button
                onclick="iniciarPreparo({{ $pedido->id }})"
                class="flex-1 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white px-4 py-3 rounded-lg text-sm font-bold transition touch-manipulation"
            >
                INICIAR PREPARO
            </button>
        @elseif($tipo == 'preparo')
            <button
                onclick="marcarPronto({{ $pedido->id }})"
                class="flex-1 bg-green-600 hover:bg-green-700 active:bg-green-800 text-white px-4 py-3 rounded-lg text-sm font-bold transition touch-manipulation"
            >
                PRONTO
            </button>
        @elseif($tipo == 'pronto')
            <button
                onclick="entregar({{ $pedido->id }})"
                class="flex-1 bg-purple-600 hover:bg-purple-700 active:bg-purple-800 text-white px-4 py-3 rounded-lg text-sm font-bold transition touch-manipulation"
            >
                SAIU P/ ENTREGA
            </button>
        @elseif($tipo == 'saiu_entrega')
            <button
                onclick="marcarEntregue({{ $pedido->id }})"
                class="flex-1 bg-green-600 hover:bg-green-700 active:bg-green-800 text-white px-4 py-3 rounded-lg text-sm font-bold transition touch-manipulation"
            >
                ENTREGUE
            </button>
        @endif
    </div>
</div>
