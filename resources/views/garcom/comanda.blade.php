@extends('layouts.garcom')

@section('title', 'Comanda - Mesa ' . $mesa->numero)

@section('content')
<div class="space-y-6">
    <!-- Header da Mesa -->
    <div class="bg-gray-800 rounded-xl shadow-xl p-4 sm:p-6 border border-gray-700">
        <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
            <div class="flex items-center space-x-3 sm:space-x-4">
                <div class="bg-gradient-to-r from-red-500 to-red-600 p-2 sm:p-3 rounded-xl flex-shrink-0">
                    <svg class="w-6 h-6 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="min-w-0">
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-white">Mesa {{ $mesa->numero }}</h1>
                    <p class="text-xs sm:text-sm text-gray-400 truncate">{{ $mesa->localizacao ?? 'Sem localização' }} • {{ $mesa->capacidade }} pessoas</p>
                </div>
            </div>
            <div class="flex items-center space-x-3 w-full sm:w-auto">
                <span class="px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-semibold whitespace-nowrap
                    @if($mesa->status == 'disponivel') bg-green-500/20 text-green-400 border border-green-500/30
                    @elseif($mesa->status == 'ocupada') bg-red-500/20 text-red-400 border border-red-500/30
                    @elseif($mesa->status == 'reservada') bg-yellow-500/20 text-yellow-400 border border-yellow-500/30
                    @else bg-gray-500/20 text-gray-400 border border-gray-500/30 @endif">
                    {{ ucfirst($mesa->status) }}
                </span>
                <a href="{{ route('garcom.index') }}" class="text-gray-400 hover:text-white transition whitespace-nowrap">
                    ← Voltar
                </a>
            </div>
        </div>
    </div>

    <!-- Nome do Cliente -->
    <div class="bg-gray-800 rounded-xl shadow-xl p-4 border border-gray-700">
        <div class="flex items-center space-x-3">
            <svg class="w-6 h-6 text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <div class="flex-1">
                <label for="cliente_nome" class="text-xs text-gray-400 block mb-1">Cliente / Anotação</label>
                <input
                    type="text"
                    id="cliente_nome"
                    value="{{ $mesa->cliente_nome ?? '' }}"
                    placeholder="Clique para adicionar nome ou anotação..."
                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    onblur="atualizarClienteNome()"
                    onkeypress="if(event.key === 'Enter') { this.blur(); }"
                >
            </div>
            <div id="clienteSaveStatus" class="hidden text-green-400 text-sm">
                ✓ Salvo
            </div>
        </div>
    </div>

    <!-- Cards de Resumo -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-gray-800 rounded-xl p-4 sm:p-6 border border-gray-700">
            <div class="text-gray-400 text-xs sm:text-sm mb-1">Total de Pedidos</div>
            <div class="text-2xl sm:text-3xl font-bold text-white">{{ $pedidos->count() }}</div>
        </div>
        <div class="bg-gray-800 rounded-xl p-4 sm:p-6 border border-gray-700">
            <div class="text-gray-400 text-xs sm:text-sm mb-1">Total de Itens</div>
            <div class="text-2xl sm:text-3xl font-bold text-white">{{ $quantidadeItens }}</div>
        </div>
        <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-xl p-4 sm:p-6 shadow-xl col-span-2 md:col-span-2">
            <div class="text-green-100 text-xs sm:text-sm mb-1">Valor Total da Conta</div>
            <div class="text-2xl sm:text-3xl md:text-4xl font-bold text-white">R$ {{ number_format($totalGeral, 2, ',', '.') }}</div>
        </div>
    </div>

    <!-- Botões de Ação Principal -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 sm:gap-4">
        @if(auth()->user()->podeLancarPedidos())
        <button onclick="openModal('modalNovoPedido')" class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 active:scale-95 text-white px-4 sm:px-6 py-3 sm:py-4 rounded-xl font-bold text-base sm:text-lg shadow-xl hover:shadow-2xl transition-all duration-200 flex items-center justify-center space-x-2">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <span>Adicionar Itens</span>
        </button>
        @else
        <div class="bg-gray-600 text-white px-4 sm:px-6 py-3 sm:py-4 rounded-xl font-bold text-base sm:text-lg shadow-xl flex items-center justify-center space-x-2 opacity-50 cursor-not-allowed">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
            <span>Sem Permissão</span>
        </div>
        @endif

        @if(auth()->user()->podeFecharMesas())
        <a href="{{ route('garcom.fechar', ['mesa' => $mesa, 'source' => 'garcom']) }}" class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 active:scale-95 text-white px-4 sm:px-6 py-3 sm:py-4 rounded-xl font-bold text-base sm:text-lg shadow-xl hover:shadow-2xl transition-all duration-200 flex items-center justify-center space-x-2">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <span>Fechar Conta</span>
        </a>
        @else
        <div class="bg-gray-600 text-white px-4 sm:px-6 py-3 sm:py-4 rounded-xl font-bold text-base sm:text-lg shadow-xl flex items-center justify-center space-x-2 opacity-50 cursor-not-allowed">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
            <span>Sem Permissão</span>
        </div>
        @endif

        <a href="{{ route('garcom.imprimir', $mesa) }}" target="_blank" class="bg-gray-700 hover:bg-gray-600 active:scale-95 text-white px-4 sm:px-6 py-3 sm:py-4 rounded-xl font-bold text-base sm:text-lg shadow-xl hover:shadow-2xl transition-all duration-200 flex items-center justify-center space-x-2 sm:col-span-2 md:col-span-1">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            <span>Imprimir Comanda</span>
        </a>
    </div>

    <!-- Lista de Pedidos -->
    <div class="bg-gray-800 rounded-xl shadow-xl border border-gray-700">
        <div class="p-6 border-b border-gray-700">
            <h2 class="text-2xl font-bold text-white">Histórico de Pedidos</h2>
        </div>
        <div class="p-6 space-y-3">
            @forelse($pedidos as $pedido)
                <div class="bg-gray-700/50 rounded-xl p-3 sm:p-4 border border-gray-600 hover:border-red-500/50 transition cursor-pointer"
                     onclick="openModal('modalPedido{{ $pedido->id }}')">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                        <div class="flex items-center space-x-3 sm:space-x-4 flex-1 min-w-0">
                            <div class="bg-gray-600 px-2 sm:px-3 py-1.5 sm:py-2 rounded-lg flex-shrink-0">
                                <span class="text-white font-bold text-xs sm:text-sm break-all">{{ $pedido->numero_pedido }}</span>
                            </div>
                            <div class="min-w-0">
                                <div class="text-white font-semibold text-sm sm:text-base">{{ $pedido->itens->count() }} {{ $pedido->itens->count() == 1 ? 'item' : 'itens' }}</div>
                                <div class="text-gray-400 text-xs sm:text-sm">{{ $pedido->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 sm:space-x-4 w-full sm:w-auto">
                            <span class="px-2 sm:px-3 py-1 sm:py-1.5 rounded-full text-xs font-semibold whitespace-nowrap
                                @if($pedido->status == 'aberto') bg-blue-500/20 text-blue-400 border border-blue-500/30
                                @elseif($pedido->status == 'em_preparo') bg-yellow-500/20 text-yellow-400 border border-yellow-500/30
                                @elseif($pedido->status == 'pronto') bg-green-500/20 text-green-400 border border-green-500/30
                                @elseif($pedido->status == 'entregue') bg-gray-500/20 text-gray-400 border border-gray-500/30
                                @elseif($pedido->status == 'cancelado') bg-red-500/20 text-red-400 border border-red-500/30
                                @endif">
                                @if($pedido->status == 'aberto') Aberto
                                @elseif($pedido->status == 'em_preparo') Em Preparo
                                @elseif($pedido->status == 'pronto') Pronto
                                @elseif($pedido->status == 'entregue') Entregue
                                @elseif($pedido->status == 'cancelado') Cancelado
                                @endif
                            </span>
                            <div class="text-lg sm:text-xl md:text-2xl font-bold text-green-400 flex-shrink-0">R$ {{ number_format($pedido->total, 2, ',', '.') }}</div>
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Modal de Detalhes do Pedido -->
                <div id="modalPedido{{ $pedido->id }}" class="modal hidden fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-2 sm:p-4">
                    <div class="bg-gray-800 rounded-2xl max-w-2xl w-full max-h-[95vh] sm:max-h-[90vh] overflow-hidden border border-gray-700 shadow-2xl">
                        <div class="p-4 sm:p-6 border-b border-gray-700 bg-gray-750">
                            <div class="flex justify-between items-start gap-3">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg sm:text-xl md:text-2xl font-bold text-white break-all">{{ $pedido->numero_pedido }}</h3>
                                    <div class="text-gray-400 text-xs sm:text-sm mt-1">{{ $pedido->created_at->format('d/m/Y H:i') }}
                                        @if($pedido->user) • <span class="truncate inline-block max-w-[150px] align-bottom">{{ $pedido->user->nome }}</span> @endif
                                    </div>
                                </div>
                                <button onclick="closeModal('modalPedido{{ $pedido->id }}')" class="text-gray-400 hover:text-white transition flex-shrink-0">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="p-3 sm:p-4 md:p-6 overflow-y-auto max-h-[calc(95vh-10rem)] sm:max-h-[calc(90vh-12rem)]">
                            <div class="space-y-2 sm:space-y-3">
                                @foreach($pedido->itens as $item)
                                    <div class="bg-gray-700 rounded-lg p-3 sm:p-4 border border-gray-600">
                                        <div class="flex flex-col sm:flex-row justify-between items-start gap-3">
                                            <div class="flex-1 min-w-0 w-full">
                                                <div class="text-white font-semibold text-base sm:text-lg break-words">
                                                    <span id="item-quantidade-{{ $item->id }}">{{ $item->quantidade }}</span>x {{ $item->produto->nome }}
                                                    @if($item->produtoTamanho)
                                                        <span class="text-orange-400">({{ $item->produtoTamanho->nome }})</span>
                                                    @endif
                                                </div>
                                                @if($item->produto->categoria)
                                                    <div class="text-gray-400 text-xs sm:text-sm">{{ $item->produto->categoria->nome }}</div>
                                                @endif
                                                @if($item->sabores->count() > 0)
                                                    <div class="mt-2 bg-blue-500/10 p-2 rounded border border-blue-500/30">
                                                        <span class="text-blue-400 font-medium text-xs sm:text-sm">Sabores:</span>
                                                        <span class="text-blue-300 text-xs sm:text-sm break-words">{{ $item->sabores->pluck('sabor.nome')->join(', ') }}</span>
                                                    </div>
                                                @endif
                                                @if($item->observacoes)
                                                    <div class="mt-2 text-yellow-400 text-xs sm:text-sm bg-yellow-500/10 p-2 rounded border border-yellow-500/30 break-words">
                                                        <span class="font-medium">Obs:</span> {{ $item->observacoes }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="text-left sm:text-right flex-shrink-0 w-full sm:w-auto">
                                                <div class="text-gray-400 text-xs sm:text-sm">Unitário: R$ {{ number_format($item->preco_unitario, 2, ',', '.') }}</div>
                                                <div class="text-green-400 font-bold text-lg sm:text-xl" id="item-subtotal-{{ $item->id }}">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</div>
                                            </div>
                                        </div>
                                        @if($pedido->status != 'finalizado' && $pedido->status != 'cancelado')
                                        <div class="mt-3 pt-3 border-t border-gray-600 flex items-center justify-between gap-2">
                                            @if(auth()->user()->podeLancarPedidos())
                                            <div class="flex items-center space-x-2">
                                                <button type="button" onclick="alterarQuantidadeItem({{ $item->id }}, -1, {{ $item->preco_unitario }})"
                                                    class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-gray-600 hover:bg-gray-500 active:scale-95 flex items-center justify-center text-white font-bold transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                    </svg>
                                                </button>
                                                <span class="text-white text-xs sm:text-sm font-medium px-1">Qtd</span>
                                                <button type="button" onclick="alterarQuantidadeItem({{ $item->id }}, 1, {{ $item->preco_unitario }})"
                                                    class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-gray-600 hover:bg-gray-500 active:scale-95 flex items-center justify-center text-white font-bold transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            @else
                                            <div class="flex items-center space-x-2 opacity-50">
                                                <span class="text-gray-400 text-xs">Quantidade: {{ $item->quantidade }}</span>
                                            </div>
                                            @endif
                                            @if(auth()->user()->podeCancelarItens())
                                            <button type="button" onclick="cancelarItem({{ $item->id }}, '{{ addslashes($item->produto->nome) }}')"
                                                class="p-2 bg-red-600 hover:bg-red-700 active:scale-95 text-white rounded-lg transition flex items-center justify-center"
                                                title="Cancelar item">
                                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                            @endif
                                        </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-4 sm:mt-6 bg-gradient-to-r from-green-600 to-green-700 rounded-lg p-3 sm:p-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-white font-bold text-sm sm:text-base md:text-lg">Total do Pedido:</span>
                                    <span class="text-white font-bold text-xl sm:text-2xl">R$ {{ number_format($pedido->total, 2, ',', '.') }}</span>
                                </div>
                            </div>
                            @if($pedido->status != 'finalizado' && $pedido->status != 'cancelado' && auth()->user()->podeCancelarPedidos())
                            <div class="mt-3 sm:mt-4">
                                <button type="button" id="btnCancelar{{ $pedido->id }}" data-pedido-id="{{ $pedido->id }}"
                                    class="btn-cancelar-pedido w-full px-4 sm:px-6 py-2.5 sm:py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm sm:text-base font-bold transition flex items-center justify-center space-x-2">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    <span>Cancelar Pedido</span>
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-gray-400">
                    <svg class="mx-auto h-16 w-16 text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-lg">Nenhum pedido registrado para esta mesa</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal de Novo Pedido -->
<div id="modalNovoPedido" class="modal hidden fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-gray-800 rounded-2xl max-w-5xl w-full max-h-[90vh] overflow-hidden border border-gray-700 shadow-2xl">
        <div class="p-6 border-b border-gray-700 bg-gray-750">
            <div class="flex justify-between items-center">
                <h3 class="text-2xl font-bold text-white">Adicionar Novos Itens</h3>
                <button onclick="closeModal('modalNovoPedido')" class="text-gray-400 hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-12rem)]">
            <form action="{{ route('pedidos.store') }}" method="POST" id="formNovoPedido">
                @csrf
                <input type="hidden" name="mesa_id" value="{{ $mesa->id }}">
                <input type="hidden" name="source" value="garcom">

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Cardápio -->
                    <div>
                        <h4 class="text-lg font-bold text-white mb-4">Cardápio</h4>
                        <div class="space-y-4 max-h-96 overflow-y-auto pr-2">
                            @foreach($produtos as $categoriaNome => $produtosCategoria)
                                <div>
                                    <div class="text-red-400 font-semibold mb-2 sticky top-0 bg-gray-800 py-2">{{ $categoriaNome }}</div>
                                    <div class="space-y-2">
                                        @foreach($produtosCategoria as $produto)
                                            <button type="button"
                                                onclick="@if($produto->tamanhos->count() > 0) abrirModalPizza({{ $produto->id }}, '{{ addslashes($produto->nome) }}', {{ $produto->tamanhos }}, '{{ addslashes($categoriaNome) }}') @else adicionarProduto({{ $produto->id }}, '{{ addslashes($produto->nome) }}', {{ $produto->preco }}, event) @endif"
                                                class="w-full text-left bg-gray-700 hover:bg-gray-600 active:scale-95 p-3 rounded-lg border border-gray-600 hover:border-red-500/50 hover:shadow-lg transition-all duration-200">
                                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                                                    <div class="flex-1 min-w-0">
                                                        <div class="text-white font-medium break-words">{{ $produto->nome }}</div>
                                                        @if($produto->descricao)
                                                            <div class="text-gray-400 text-xs line-clamp-2">{{ Str::limit($produto->descricao, 50) }}</div>
                                                        @endif
                                                        @if($produto->tamanhos->count() > 0)
                                                            <span class="inline-flex mt-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-orange-900/40 text-orange-300 border border-orange-800">
                                                                {{ $produto->tamanhos->count() }} tamanhos
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="text-green-400 font-bold text-sm sm:text-base flex-shrink-0 whitespace-nowrap">
                                                        @if($produto->tamanhos->count() > 0)
                                                            <span class="hidden sm:inline">A partir de </span><span class="sm:hidden">A partir<br></span>R$ {{ number_format($produto->tamanhos->min('preco'), 2, ',', '.') }}
                                                        @else
                                                            R$ {{ number_format($produto->preco, 2, ',', '.') }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Carrinho -->
                    <div>
                        <h4 class="text-lg font-bold text-white mb-4">Carrinho</h4>
                        <div id="carrinho" class="space-y-2 mb-4 max-h-64 overflow-y-auto pr-2">
                            <div class="text-center text-gray-400 py-8">Carrinho vazio</div>
                        </div>

                        <!-- Total -->
                        <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-lg p-4 mb-4">
                            <div class="flex justify-between items-center">
                                <span class="text-white font-bold text-lg">Total:</span>
                                <span class="text-white font-bold text-3xl" id="totalCarrinho">R$ 0,00</span>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="space-y-2">
                            <button type="submit" id="btnEnviarPedido" disabled class="w-full px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:from-red-600 hover:to-red-700 active:scale-95 font-bold disabled:from-gray-600 disabled:to-gray-600 disabled:cursor-not-allowed transition-all duration-200 shadow-lg hover:shadow-xl">
                                Enviar Pedido
                            </button>
                            <button type="button" onclick="limparCarrinho()" class="w-full px-6 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 active:scale-95 transition-all duration-200 shadow-md hover:shadow-lg">
                                Limpar Carrinho
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Seleção de Pizza -->
<div id="modalPizza" class="modal hidden fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-2 sm:p-4">
    <div class="bg-gray-800 rounded-2xl max-w-2xl w-full max-h-[95vh] sm:max-h-[90vh] overflow-hidden border border-gray-700 shadow-2xl" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="p-4 sm:p-6 border-b border-gray-700 bg-gradient-to-r from-red-500 to-orange-500">
            <div class="flex justify-between items-center gap-3">
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg sm:text-xl md:text-2xl font-bold text-white break-words" id="modalPizzaNome">Pizza</h3>
                    <p class="text-white/90 text-xs sm:text-sm">Escolha o tamanho e sabores</p>
                </div>
                <button onclick="fecharModalPizza()" class="text-white hover:bg-white/20 rounded-lg p-1.5 sm:p-2 transition flex-shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Body -->
        <div class="p-3 sm:p-4 md:p-6 overflow-y-auto max-h-[calc(95vh-10rem)] sm:max-h-[calc(90vh-12rem)] space-y-4 sm:space-y-6">
            <!-- Tamanhos -->
            <div>
                <h4 class="text-base sm:text-lg font-semibold text-white mb-2 sm:mb-3">Escolha o Tamanho *</h4>
                <div id="tamanhos-container" class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-3"></div>
            </div>

            <!-- Sabores -->
            <div id="sabores-section" class="hidden">
                <h4 class="text-base sm:text-lg font-semibold text-white mb-2">Escolha os Sabores *</h4>
                <p class="text-xs sm:text-sm text-gray-400 mb-2 sm:mb-3">
                    <span id="sabores-info"></span>
                </p>
                <div id="sabores-container" class="space-y-2 sm:space-y-4"></div>
            </div>

            <!-- Observações -->
            <div>
                <label for="pizza-observacoes" class="block text-xs sm:text-sm font-medium text-white mb-2">Observações</label>
                <textarea id="pizza-observacoes" rows="2" class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base bg-gray-700 border border-gray-600 rounded-lg text-gray-100 focus:ring-2 focus:ring-red-500" placeholder="Ex: Sem cebola, borda recheada, etc"></textarea>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-3 sm:p-4 border-t border-gray-700 bg-gray-750 flex flex-col sm:flex-row justify-end gap-2 sm:gap-3">
            <button onclick="fecharModalPizza()" class="px-4 py-2 text-sm sm:text-base bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition order-2 sm:order-1">
                Cancelar
            </button>
            <button onclick="adicionarPizza()" class="px-4 py-2 text-sm sm:text-base bg-gradient-to-r from-red-500 to-orange-500 text-white rounded-lg hover:from-red-600 hover:to-orange-600 transition font-semibold order-1 sm:order-2">
                Adicionar ao Carrinho
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
let itens = [];
let itemIndex = 0;

// Dados da pizza sendo configurada
let pizzaAtual = {
    produtoId: null,
    produtoNome: null,
    categoria: null,
    tamanhos: [],
    tamanhoSelecionado: null,
    saboresSelecionados: []
};

// Sabores disponíveis
const saboresDisponiveis = @json($sabores);

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('modal')) {
        closeModal(event.target.id);
    }
});

// Close modal on ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        // Fechar modal de pizza se estiver aberto
        const modalPizza = document.getElementById('modalPizza');
        if (!modalPizza.classList.contains('hidden')) {
            fecharModalPizza();
            return;
        }

        // Fechar outros modais
        document.querySelectorAll('.modal:not(.hidden)').forEach(modal => {
            closeModal(modal.id);
        });
    }
});

function adicionarProduto(id, nome, preco, event) {
    const itemExistente = itens.find(item => item.produto_id === id && !item.isPizza);

    if (itemExistente) {
        itemExistente.quantidade++;
        showToast(`✓ ${nome} - Qtd: ${itemExistente.quantidade}`, 'success');
    } else {
        const item = {
            index: itemIndex++,
            produto_id: id,
            nome: nome,
            preco: preco,
            quantidade: 1,
            isPizza: false
        };
        itens.push(item);
        showToast(`✓ ${nome} adicionado!`, 'success');
    }

    renderizarCarrinho();
    atualizarTotal();

    // Feedback visual no botão clicado (especialmente útil no mobile)
    if (event && event.target) {
        const btn = event.target.closest('button');
        if (btn) {
            btn.classList.add('scale-95', 'bg-green-600');
            setTimeout(() => {
                btn.classList.remove('scale-95', 'bg-green-600');
            }, 300);
        }
    }

    // Scroll suave para o carrinho no mobile
    if (window.innerWidth < 1024) {
        setTimeout(() => {
            const carrinho = document.getElementById('carrinho');
            if (carrinho) {
                carrinho.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }, 100);
    }
}

// Funções do Modal de Pizza
function abrirModalPizza(id, nome, tamanhos, categoria) {
    pizzaAtual = {
        produtoId: id,
        produtoNome: nome,
        categoria: categoria,
        tamanhos: tamanhos,
        tamanhoSelecionado: null,
        saboresSelecionados: []
    };

    document.getElementById('modalPizzaNome').textContent = nome;
    document.getElementById('modalPizza').classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    renderizarTamanhos();
    document.getElementById('sabores-section').classList.add('hidden');
}

function fecharModalPizza() {
    document.getElementById('modalPizza').classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('pizza-observacoes').value = '';
}

function renderizarTamanhos() {
    const container = document.getElementById('tamanhos-container');
    container.innerHTML = pizzaAtual.tamanhos.map(tamanho => `
        <div class="border-2 ${pizzaAtual.tamanhoSelecionado?.id === tamanho.id ? 'border-red-500 bg-red-900/20' : 'border-gray-600'} rounded-lg p-4 cursor-pointer hover:border-red-400 transition" onclick="selecionarTamanho(${tamanho.id})">
            <div class="text-center">
                <p class="text-2xl font-bold text-white">${tamanho.nome}</p>
                <p class="text-sm text-gray-400 mt-1">${tamanho.descricao || ''}</p>
                <p class="text-lg font-semibold text-green-400 mt-2">R$ ${parseFloat(tamanho.preco).toFixed(2).replace('.', ',')}</p>
                <p class="text-xs text-gray-500 mt-1">Até ${tamanho.max_sabores} ${tamanho.max_sabores > 1 ? 'sabores' : 'sabor'}</p>
            </div>
        </div>
    `).join('');
}

function selecionarTamanho(tamanhoId) {
    pizzaAtual.tamanhoSelecionado = pizzaAtual.tamanhos.find(t => t.id === tamanhoId);
    pizzaAtual.saboresSelecionados = [];

    renderizarTamanhos();
    document.getElementById('sabores-section').classList.remove('hidden');
    document.getElementById('sabores-info').textContent = `Selecione até ${pizzaAtual.tamanhoSelecionado.max_sabores} ${pizzaAtual.tamanhoSelecionado.max_sabores > 1 ? 'sabores' : 'sabor'}`;

    renderizarSabores();
}

function renderizarSabores() {
    const container = document.getElementById('sabores-container');
    const tamanhoNome = pizzaAtual.tamanhoSelecionado.nome.toLowerCase();
    const campoPreco = `preco_${tamanhoNome}`;
    const categoriaProduto = pizzaAtual.categoria;

    // Filtrar categorias de sabores baseado no tipo de pizza
    // Pizza Doce -> só mostra sabores de "Pizzas Doces"
    // Pizza Salgada -> mostra sabores de "Pizzas" e "Pizzas Doces"
    const isPizzaDoce = categoriaProduto && categoriaProduto.toLowerCase().includes('doce');

    let html = '';
    for (const [categoriaSabor, sabores] of Object.entries(saboresDisponiveis)) {
        // Se for pizza doce, só mostrar sabores doces
        if (isPizzaDoce) {
            if (!categoriaSabor.toLowerCase().includes('doce')) {
                continue; // Pular categorias que não são doces
            }
        }

        html += `
            <div>
                <h5 class="text-sm font-semibold text-gray-300 mb-2">${categoriaSabor}</h5>
                <div class="grid grid-cols-1 gap-2">
        `;

        sabores.forEach(sabor => {
            const isSelected = pizzaAtual.saboresSelecionados.includes(sabor.id);
            const isDisabled = !isSelected && pizzaAtual.saboresSelecionados.length >= pizzaAtual.tamanhoSelecionado.max_sabores;
            const precoSabor = sabor[campoPreco] ? parseFloat(sabor[campoPreco]) : 0;
            const isEspecial = precoSabor > parseFloat(pizzaAtual.tamanhoSelecionado.preco);

            html += `
                <div class="flex items-center p-3 border ${isSelected ? 'border-red-500 bg-red-900/20' : 'border-gray-600'} rounded-lg ${isDisabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer hover:border-red-400'} transition" onclick="${!isDisabled ? `toggleSabor(${sabor.id})` : ''}">
                    <input type="checkbox" ${isSelected ? 'checked' : ''} ${isDisabled ? 'disabled' : ''} class="h-4 w-4 text-red-500 bg-gray-700 border-gray-600 rounded focus:ring-red-500 mr-3 pointer-events-none">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <p class="font-medium text-white">${sabor.nome}</p>
                            ${isEspecial ? '<span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-orange-900/40 text-orange-300 border border-orange-800">Especial</span>' : ''}
                        </div>
                        ${sabor.ingredientes ? `<p class="text-xs text-gray-400 mt-1">${sabor.ingredientes}</p>` : ''}
                        ${precoSabor > 0 ? `<p class="text-xs text-green-400 mt-1 font-semibold">R$ ${precoSabor.toFixed(2).replace('.', ',')}</p>` : ''}
                    </div>
                </div>
            `;
        });

        html += `
                </div>
            </div>
        `;
    }

    container.innerHTML = html;
}

function toggleSabor(saborId) {
    const index = pizzaAtual.saboresSelecionados.indexOf(saborId);

    if (index > -1) {
        pizzaAtual.saboresSelecionados.splice(index, 1);
    } else if (pizzaAtual.saboresSelecionados.length < pizzaAtual.tamanhoSelecionado.max_sabores) {
        pizzaAtual.saboresSelecionados.push(saborId);
    }

    renderizarSabores();
}

function adicionarPizza() {
    if (!pizzaAtual.tamanhoSelecionado) {
        showToast('⚠ Selecione um tamanho', 'warning');
        return;
    }

    if (pizzaAtual.saboresSelecionados.length === 0) {
        showToast('⚠ Selecione pelo menos um sabor', 'warning');
        return;
    }

    // Calcular preço baseado nos sabores escolhidos (sempre o maior preço)
    const tamanhoNome = pizzaAtual.tamanhoSelecionado.nome.toLowerCase();
    const campoPreco = `preco_${tamanhoNome}`;
    let maiorPreco = parseFloat(pizzaAtual.tamanhoSelecionado.preco);

    const saboresNomes = pizzaAtual.saboresSelecionados.map(id => {
        for (const sabores of Object.values(saboresDisponiveis)) {
            const sabor = sabores.find(s => s.id === id);
            if (sabor) {
                // Verificar o preço do sabor para o tamanho selecionado
                const precoSabor = sabor[campoPreco] ? parseFloat(sabor[campoPreco]) : 0;
                if (precoSabor > maiorPreco) {
                    maiorPreco = precoSabor;
                }
                return sabor.nome;
            }
        }
        return '';
    }).join(', ');

    const item = {
        index: itemIndex++,
        produto_id: pizzaAtual.produtoId,
        nome: `${pizzaAtual.produtoNome} - ${pizzaAtual.tamanhoSelecionado.nome}`,
        detalhes: `Sabores: ${saboresNomes}`,
        preco: maiorPreco,
        quantidade: 1,
        isPizza: true,
        tamanho_id: pizzaAtual.tamanhoSelecionado.id,
        sabores: pizzaAtual.saboresSelecionados,
        observacoes: document.getElementById('pizza-observacoes').value
    };

    itens.push(item);
    showToast(`✓ ${pizzaAtual.produtoNome} adicionada!`, 'success');
    renderizarCarrinho();
    atualizarTotal();
    fecharModalPizza();

    // Scroll suave para o carrinho no mobile
    if (window.innerWidth < 1024) {
        setTimeout(() => {
            const carrinho = document.getElementById('carrinho');
            if (carrinho) {
                carrinho.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }, 100);
    }
}

function removerItem(index) {
    const item = itens.find(i => i.index === index);
    itens = itens.filter(item => item.index !== index);
    if (item) {
        showToast(`${item.nome} removido do carrinho`, 'info');
    }
    renderizarCarrinho();
    atualizarTotal();
}

function alterarQuantidade(index, delta) {
    const item = itens.find(i => i.index === index);
    if (item) {
        item.quantidade += delta;
        if (item.quantidade <= 0) {
            removerItem(index);
        } else {
            renderizarCarrinho();
            atualizarTotal();
        }
    }
}

function renderizarCarrinho() {
    const carrinho = document.getElementById('carrinho');

    if (itens.length === 0) {
        carrinho.innerHTML = '<div class="text-center text-gray-400 py-8">Carrinho vazio</div>';
        document.getElementById('btnEnviarPedido').disabled = true;
        return;
    }

    document.getElementById('btnEnviarPedido').disabled = false;

    carrinho.innerHTML = itens.map(item => `
        <div class="bg-gray-700 rounded-lg p-3 border border-gray-600">
            <div class="flex justify-between items-start mb-2">
                <div class="flex-1">
                    <div class="text-white font-medium">${item.nome}</div>
                    ${item.detalhes ? `<div class="text-gray-400 text-xs mt-1">${item.detalhes}</div>` : ''}
                    ${item.observacoes ? `<div class="text-yellow-400 text-xs mt-1">Obs: ${item.observacoes}</div>` : ''}
                    <div class="text-gray-400 text-sm">R$ ${item.preco.toFixed(2).replace('.', ',')}</div>
                </div>
                <button type="button" onclick="removerItem(${item.index})" class="text-red-400 hover:text-red-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <button type="button" onclick="alterarQuantidade(${item.index}, -1)"
                        class="w-8 h-8 rounded-lg bg-gray-600 hover:bg-gray-500 flex items-center justify-center text-white font-bold transition">
                        -
                    </button>
                    <span class="w-12 text-center font-bold text-white">${item.quantidade}</span>
                    <button type="button" onclick="alterarQuantidade(${item.index}, 1)"
                        class="w-8 h-8 rounded-lg bg-gray-600 hover:bg-gray-500 flex items-center justify-center text-white font-bold transition">
                        +
                    </button>
                </div>
                <div class="text-green-400 font-bold text-lg">
                    R$ ${(item.preco * item.quantidade).toFixed(2).replace('.', ',')}
                </div>
            </div>
            <input type="hidden" name="itens[${item.index}][produto_id]" value="${item.produto_id}">
            <input type="hidden" name="itens[${item.index}][quantidade]" value="${item.quantidade}">
            <input type="hidden" name="itens[${item.index}][preco_unitario]" value="${item.preco}">
            ${item.isPizza ? `
                <input type="hidden" name="itens[${item.index}][tamanho_id]" value="${item.tamanho_id}">
                ${item.sabores.map((saborId, idx) => `<input type="hidden" name="itens[${item.index}][sabores][${idx}]" value="${saborId}">`).join('')}
                <input type="hidden" name="itens[${item.index}][observacoes]" value="${item.observacoes || ''}">
            ` : ''}
        </div>
    `).join('');
}

function atualizarTotal() {
    const total = itens.reduce((sum, item) => sum + (item.preco * item.quantidade), 0);
    document.getElementById('totalCarrinho').textContent = 'R$ ' + total.toFixed(2).replace('.', ',');
}

function limparCarrinho() {
    if (itens.length === 0) {
        showToast('Carrinho já está vazio', 'info');
        return;
    }
    itens = [];
    renderizarCarrinho();
    atualizarTotal();
    showToast('Carrinho limpo!', 'success');
}

// Atualizar nome do cliente
function atualizarClienteNome() {
    const clienteNome = document.getElementById('cliente_nome').value;
    const statusDiv = document.getElementById('clienteSaveStatus');

    fetch('{{ route('garcom.atualizarCliente', $mesa) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            cliente_nome: clienteNome
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            statusDiv.classList.remove('hidden');
            setTimeout(() => {
                statusDiv.classList.add('hidden');
            }, 2000);
        }
    })
    .catch(error => {
        console.error('Erro ao salvar:', error);
    });
}

// Cancelar pedido - usar event delegation
document.addEventListener('click', function(e) {
    if (e.target.closest('.btn-cancelar-pedido')) {
        e.preventDefault();
        e.stopPropagation();

        const button = e.target.closest('.btn-cancelar-pedido');
        const pedidoId = button.getAttribute('data-pedido-id');

        console.log('Cancelando pedido:', pedidoId);

        // Prevenir duplo clique - verificar se já está processando
        if (button.disabled || button.dataset.processing === 'true') {
            return;
        }

        if (!confirm('Tem certeza que deseja cancelar este pedido? Esta ação não pode ser desfeita.')) {
            return;
        }

        // Marcar como processando e desabilitar botão
        button.dataset.processing = 'true';
        button.disabled = true;

        // Salvar HTML original
        const originalHTML = button.innerHTML;

        // Adicionar spinner no botão
        button.innerHTML = `
            <svg class="animate-spin h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="ml-2">Processando...</span>
        `;
        button.classList.add('opacity-75', 'cursor-not-allowed');

        // Mostrar loading overlay
        showLoading('Cancelando pedido...');

        fetch(`/garcom/pedido/${pedidoId}/cancelar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert(data.message || 'Erro ao cancelar pedido');
                // Restaurar botão em caso de erro
                button.innerHTML = originalHTML;
                button.disabled = false;
                button.dataset.processing = 'false';
                button.classList.remove('opacity-75', 'cursor-not-allowed');
            }
        })
        .catch(error => {
            console.error('Erro ao cancelar:', error);
            alert('Erro ao cancelar pedido: ' + error.message);
            // Restaurar botão em caso de erro
            button.innerHTML = originalHTML;
            button.disabled = false;
            button.dataset.processing = 'false';
            button.classList.remove('opacity-75', 'cursor-not-allowed');
        })
        .finally(() => {
            // Esconder loading overlay
            hideLoading();
        });
    }
});

renderizarCarrinho();

// Alterar quantidade de item individual (AJAX - sem reload)
function alterarQuantidadeItem(itemId, delta, precoUnitario) {
    const quantidadeSpan = document.getElementById(`item-quantidade-${itemId}`);
    let quantidadeAtual = parseInt(quantidadeSpan.textContent);
    let novaQuantidade = quantidadeAtual + delta;

    if (novaQuantidade <= 0) {
        if (confirm('Deseja remover completamente este item do pedido?')) {
            cancelarItem(itemId, '');
        }
        return;
    }

    // Desabilitar botões durante a requisição
    const botoes = document.querySelectorAll(`button[onclick*="alterarQuantidadeItem(${itemId}"]`);
    botoes.forEach(btn => btn.disabled = true);

    fetch(`/garcom/item/${itemId}/atualizar-quantidade`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            quantidade: novaQuantidade
        })
    })
    .then(response => response.json()) // Parse JSON independente do status
    .then(data => {
        if (data.success) {
            // Atualizar quantidade
            quantidadeSpan.textContent = novaQuantidade;

            // Atualizar subtotal do item
            const novoSubtotal = novaQuantidade * precoUnitario;
            document.getElementById(`item-subtotal-${itemId}`).textContent =
                'R$ ' + novoSubtotal.toFixed(2).replace('.', ',');

            // Atualizar total do pedido no modal
            const pedidoModal = quantidadeSpan.closest('.modal');
            if (pedidoModal) {
                const totalPedidoElement = pedidoModal.querySelector('.text-white.font-bold.text-xl, .text-white.font-bold.text-2xl');
                if (totalPedidoElement && data.novo_total_pedido) {
                    totalPedidoElement.textContent = 'R$ ' + parseFloat(data.novo_total_pedido).toFixed(2).replace('.', ',');
                }
            }

            // Feedback visual
            quantidadeSpan.classList.add('text-yellow-400');
            setTimeout(() => quantidadeSpan.classList.remove('text-yellow-400'), 300);

            showToast('✓ Quantidade atualizada!', 'success');
        } else {
            showToast(data.message || 'Erro ao atualizar quantidade', 'error');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showToast('Erro ao atualizar quantidade', 'error');
    })
    .finally(() => {
        // Reabilitar botões
        botoes.forEach(btn => btn.disabled = false);
    });
}

// Cancelar item individual (AJAX - sem reload)
function cancelarItem(itemId, nomeProduto) {
    const mensagem = nomeProduto
        ? `Tem certeza que deseja cancelar "${nomeProduto}"?`
        : 'Tem certeza que deseja cancelar este item?';

    if (!confirm(mensagem)) {
        return;
    }

    fetch(`/garcom/item/${itemId}/cancelar`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message || '✓ Item cancelado!', 'success');

            // Se o pedido foi cancelado completamente, recarregar
            if (data.pedido_cancelado) {
                setTimeout(() => window.location.reload(), 1000);
                return;
            }

            // Remover item da interface com animação
            const itemElement = document.querySelector(`#item-quantidade-${itemId}`).closest('.bg-gray-700');
            if (itemElement) {
                itemElement.style.opacity = '0';
                itemElement.style.transform = 'translateX(100%)';
                itemElement.style.transition = 'all 0.3s ease';

                setTimeout(() => {
                    itemElement.remove();

                    // Atualizar total do pedido no modal
                    const pedidoModal = document.querySelector('.modal:not(.hidden)');
                    if (pedidoModal && data.novo_total_pedido) {
                        const totalElement = pedidoModal.querySelector('.text-white.font-bold.text-xl, .text-white.font-bold.text-2xl');
                        if (totalElement) {
                            totalElement.textContent = 'R$ ' + parseFloat(data.novo_total_pedido).toFixed(2).replace('.', ',');
                        }
                    }

                    // Verificar se ainda há itens no pedido
                    const itensRestantes = pedidoModal?.querySelectorAll('[id^="item-quantidade-"]').length || 0;
                    if (itensRestantes === 0) {
                        showToast('Pedido cancelado completamente', 'info');
                        setTimeout(() => window.location.reload(), 1000);
                    }
                }, 300);
            }
        } else {
            showToast(data.message || 'Erro ao cancelar item', 'error');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showToast('Erro ao cancelar item', 'error');
    });
}
</script>
@endpush

<style>
@media print {
    nav, .print\:hidden, button { display: none !important; }
    body { background: white !important; }
}
</style>
@endsection
