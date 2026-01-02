@extends(isset($source) && $source === 'garcom' ? 'layouts.garcom' : 'layouts.app')

@section('title', 'Fechar Conta - Mesa ' . $mesa->numero)

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="bg-gray-800 rounded-xl shadow-xl p-6 border border-gray-700">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <div class="bg-gradient-to-r from-green-500 to-green-600 p-3 rounded-xl">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-white">Mesa {{ $mesa->numero }}</h1>
                    <p class="text-gray-400">Fechamento de Conta</p>
                </div>
            </div>
            <a href="{{ isset($source) && $source === 'garcom' ? route('garcom.index') : route('pdv.index') }}" class="text-gray-400 hover:text-white transition">
                ← Voltar {{ isset($source) && $source === 'garcom' ? '' : 'ao PDV' }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Resumo dos Pedidos -->
        <div class="bg-gray-800 rounded-xl shadow-xl p-6 border border-gray-700">
            <h2 class="text-xl font-bold mb-4 text-white">Resumo da Conta</h2>

            <div class="space-y-3 max-h-96 overflow-y-auto mb-4 pr-2">
                @foreach($pedidos as $pedido)
                    <div class="bg-gray-700 rounded-lg p-4 border border-gray-600">
                        <div class="text-sm text-gray-400 mb-2">{{ $pedido->numero_pedido }}</div>
                        @foreach($pedido->itens as $item)
                            <div class="mb-3 pb-3 border-b border-gray-600 last:border-0">
                                <div class="flex justify-between items-start mb-1">
                                    <div class="flex-1">
                                        <div>
                                            <span class="text-green-400 font-semibold" id="item-qtd-{{ $item->id }}">{{ $item->quantidade }}x</span>
                                            <span class="text-gray-200">{{ $item->produto->nome }}</span>
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
                                    <span class="text-green-400 font-semibold ml-3" id="item-total-{{ $item->id }}">
                                        R$ {{ number_format($item->subtotal, 2, ',', '.') }}
                                    </span>
                                </div>
                                <!-- Controles de Edição -->
                                <div class="flex items-center justify-end gap-2 mt-2">
                                    <button type="button" onclick="alterarQuantidade({{ $item->id }}, -1, {{ $item->preco_unitario }})"
                                        class="w-6 h-6 rounded bg-gray-600 hover:bg-gray-500 flex items-center justify-center text-white text-sm font-bold transition">
                                        -
                                    </button>
                                    <span class="text-gray-400 text-xs">Qtd</span>
                                    <button type="button" onclick="alterarQuantidade({{ $item->id }}, 1, {{ $item->preco_unitario }})"
                                        class="w-6 h-6 rounded bg-gray-600 hover:bg-gray-500 flex items-center justify-center text-white text-sm font-bold transition">
                                        +
                                    </button>
                                    <button type="button" onclick="cancelarItem({{ $item->id }}, '{{ addslashes($item->produto->nome) }}')"
                                        class="ml-2 p-1.5 bg-red-600 hover:bg-red-700 text-white rounded transition"
                                        title="Cancelar item">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>

            <div class="border-t border-gray-600 pt-4">
                <div class="flex justify-between items-center bg-gradient-to-r from-green-600 to-green-700 p-4 rounded-lg">
                    <span class="text-xl font-bold text-white">Total:</span>
                    <span class="text-3xl font-bold text-white" id="totalResumo">
                        R$ {{ number_format($total, 2, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Formulário de Pagamento -->
        <div class="bg-gray-800 rounded-xl shadow-xl p-6 border border-gray-700">
            <h2 class="text-xl font-bold mb-4 text-white">Dados do Pagamento</h2>

            <form action="{{ isset($source) && $source === 'garcom' ? route('garcom.pagamento', $mesa) : route('pdv.pagamento', $mesa) }}" method="POST" id="formPagamento">
                @csrf
                @if(isset($source) && $source === 'garcom')
                <input type="hidden" name="source" value="garcom">
                @endif

                <!-- Formas de Pagamento -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-3">Formas de Pagamento *</label>

                    <!-- Botões para adicionar formas -->
                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <button type="button" onclick="adicionarFormaPagamento('dinheiro')"
                            class="p-3 rounded-lg border border-gray-600 hover:border-green-500 hover:bg-green-900/20 transition text-left bg-gray-700">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-white">Dinheiro</span>
                                </div>
                            </div>
                        </button>
                        <button type="button" onclick="adicionarFormaPagamento('pix')"
                            class="p-3 rounded-lg border border-gray-600 hover:border-blue-500 hover:bg-blue-900/20 transition text-left bg-gray-700">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-white">PIX</span>
                                </div>
                            </div>
                        </button>
                        <button type="button" onclick="adicionarFormaPagamento('debito')"
                            class="p-3 rounded-lg border border-gray-600 hover:border-purple-500 hover:bg-purple-900/20 transition text-left bg-gray-700">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-white">Débito</span>
                                </div>
                            </div>
                        </button>
                        <button type="button" onclick="adicionarFormaPagamento('credito')"
                            class="p-3 rounded-lg border border-gray-600 hover:border-yellow-500 hover:bg-yellow-900/20 transition text-left bg-gray-700">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-white">Crédito</span>
                                </div>
                            </div>
                        </button>
                    </div>

                    <!-- Lista de formas adicionadas -->
                    <div id="formas-pagamento-lista" class="space-y-2 mb-3"></div>

                    <!-- Container para inputs hidden -->
                    <div id="formas-pagamento-inputs"></div>
                </div>

                <!-- Desconto e Acréscimo -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-3">Ajustes</label>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" onclick="abrirModalDesconto()"
                            class="p-4 rounded-xl border-2 border-gray-600 hover:border-red-500 hover:bg-red-900/20 transition text-left bg-gray-700">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                    </svg>
                                    <span class="font-semibold text-white">Desconto</span>
                                </div>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <div class="mt-2 text-xs text-gray-400" id="descontoAplicadoTexto">Nenhum desconto</div>
                        </button>

                        <button type="button" onclick="abrirModalAcrescimo()"
                            class="p-4 rounded-xl border-2 border-gray-600 hover:border-yellow-500 hover:bg-yellow-900/20 transition text-left bg-gray-700">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    <span class="font-semibold text-white">Acréscimo</span>
                                </div>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <div class="mt-2 text-xs text-gray-400" id="acrescimoAplicadoTexto">Nenhum acréscimo</div>
                        </button>
                    </div>
                </div>

                <!-- Inputs hidden -->
                <input type="hidden" name="tipo_desconto" id="tipo_desconto" value="">
                <input type="hidden" name="desconto_porcentagem" id="desconto_porcentagem" value="0">
                <input type="hidden" name="desconto_valor" id="desconto_valor" value="0">
                <input type="hidden" name="tipo_acrescimo" id="tipo_acrescimo" value="">
                <input type="hidden" name="acrescimo_porcentagem" id="acrescimo_porcentagem" value="0">
                <input type="hidden" name="acrescimo_valor" id="acrescimo_valor" value="0">

                <!-- Valor Pago (calculado automaticamente) -->
                <input type="hidden" name="valor_pago" id="valor_pago" value="0">

                <!-- Resumo do Pagamento -->
                <div class="bg-gray-700 rounded-xl p-4 mb-4 space-y-2 border border-gray-600">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-300">Subtotal:</span>
                        <span class="text-white font-semibold" id="displaySubtotal">R$ {{ number_format($total, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm" id="descontoDiv">
                        <span class="text-gray-300">Desconto:</span>
                        <span class="text-red-400 font-semibold" id="displayDesconto">- R$ 0,00</span>
                    </div>
                    <div class="flex justify-between text-sm" id="acrescimoDiv">
                        <span class="text-gray-300">Acréscimo:</span>
                        <span class="text-yellow-400 font-semibold" id="displayAcrescimo">+ R$ 0,00</span>
                    </div>
                    <div class="flex justify-between border-t border-gray-600 pt-2">
                        <span class="text-white font-bold">Total a Pagar:</span>
                        <span class="text-green-400 font-bold text-xl" id="displayTotal">R$ {{ number_format($total, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm border-t border-gray-600 pt-2 mt-2">
                        <span class="text-gray-300">Total Pago:</span>
                        <span class="text-blue-400 font-semibold" id="displayTotalPago">R$ 0,00</span>
                    </div>
                    <div class="flex justify-between text-sm" id="restanteDiv">
                        <span class="text-gray-300">Restante:</span>
                        <span class="text-red-400 font-semibold" id="displayRestante">R$ {{ number_format($total, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm hidden" id="trocoDiv">
                        <span class="text-gray-300">Troco:</span>
                        <span class="text-green-400 font-semibold" id="displayTroco">R$ 0,00</span>
                    </div>
                </div>

                <!-- Observações -->
                <div class="mb-4">
                    <label for="observacoes" class="block text-sm font-medium text-gray-300 mb-2">Observações</label>
                    <textarea name="observacoes" id="observacoes" rows="2"
                        class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-xl text-white focus:ring-2 focus:ring-red-500 focus:border-transparent"></textarea>
                </div>

                <!-- Botão Finalizar -->
                <button type="submit" class="w-full px-6 py-4 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-xl font-bold text-lg shadow-xl transition">
                    Finalizar Pagamento
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Adicionar Forma de Pagamento -->
<div id="modalFormaPagamento" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-gray-800 rounded-2xl max-w-md w-full border border-gray-700 shadow-2xl transform transition-all">
        <!-- Header -->
        <div class="bg-gradient-to-r from-gray-700 to-gray-800 p-5 rounded-t-2xl border-b border-gray-600">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-2.5 rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white" id="modalFormaTitulo">Adicionar Pagamento</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Informe o valor recebido</p>
                    </div>
                </div>
                <button onclick="fecharModalPagamento()" class="text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg p-2 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Body -->
        <div class="p-6">
            <!-- Valor Restante (Destaque) -->
            <div class="bg-gradient-to-r from-red-900/30 to-orange-900/30 border border-red-500/30 rounded-xl p-3 mb-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-xs font-medium text-gray-300">Restante:</span>
                    </div>
                    <span class="text-xl font-bold text-red-400" id="valorRestanteModal">R$ 0,00</span>
                </div>
            </div>

            <!-- Input de Valor -->
            <div class="mb-5">
                <label for="valorFormaPagamento" class="block text-sm font-semibold text-gray-300 mb-2">Digite o Valor *</label>
                <div class="relative">
                    <div class="absolute left-4 top-1/2 -translate-y-1/2 flex items-center pointer-events-none">
                        <span class="text-xl font-bold text-gray-400">R$</span>
                    </div>
                    <input type="number" step="0.01" id="valorFormaPagamento"
                        class="w-full pl-14 pr-4 py-4 bg-gray-700 border-2 border-gray-600 rounded-xl text-white text-xl font-bold focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all"
                        placeholder="0,00" autofocus>
                </div>
                <p class="mt-2 text-xs text-gray-400 flex items-center space-x-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Enter para confirmar • ESC para cancelar</span>
                </p>
            </div>

            <!-- Botões -->
            <div class="flex gap-2.5">
                <button onclick="fecharModalPagamento()" class="flex-1 px-4 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-xl transition font-semibold flex items-center justify-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <span>Cancelar</span>
                </button>
                <button onclick="confirmarFormaPagamento()" class="flex-1 px-4 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-xl transition font-bold shadow-xl flex items-center justify-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Adicionar</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Desconto/Acréscimo -->
<div id="modalAjuste" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-gray-800 rounded-2xl max-w-md w-full border border-gray-700 shadow-2xl transform transition-all">
        <!-- Header -->
        <div class="bg-gradient-to-r from-gray-700 to-gray-800 p-5 rounded-t-2xl border-b border-gray-600">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div id="ajusteIcone" class="p-2.5 rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white" id="ajusteTitulo">Adicionar Ajuste</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Escolha o tipo e o valor</p>
                    </div>
                </div>
                <button onclick="fecharModalAjuste()" class="text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg p-2 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Body -->
        <div class="p-6">
            <!-- Tipo de Ajuste -->
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-300 mb-3">Tipo de Ajuste</label>
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" onclick="selecionarTipoAjuste('porcentagem')" id="btnPorcentagem"
                        class="tipo-ajuste-btn p-3 rounded-xl border-2 border-blue-500 bg-blue-500/20 transition">
                        <div class="flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                            </svg>
                            <span class="font-semibold text-white">%</span>
                        </div>
                    </button>
                    <button type="button" onclick="selecionarTipoAjuste('valor')" id="btnValor"
                        class="tipo-ajuste-btn p-3 rounded-xl border-2 border-gray-600 bg-gray-700 hover:border-blue-500 hover:bg-blue-500/20 transition">
                        <div class="flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-semibold text-white">R$</span>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Input de Valor -->
            <div class="mb-5">
                <label for="valorAjuste" class="block text-sm font-semibold text-gray-300 mb-2">Digite o Valor *</label>
                <div class="relative">
                    <div class="absolute left-4 top-1/2 -translate-y-1/2 flex items-center pointer-events-none">
                        <span class="text-xl font-bold text-gray-400" id="simboloAjuste">%</span>
                    </div>
                    <input type="number" step="0.01" id="valorAjuste"
                        class="w-full pl-14 pr-4 py-4 bg-gray-700 border-2 border-gray-600 rounded-xl text-white text-xl font-bold focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        placeholder="0,00" autofocus>
                </div>
                <p class="mt-2 text-xs text-gray-400 flex items-center space-x-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Enter para confirmar • ESC para cancelar</span>
                </p>
            </div>

            <!-- Botões -->
            <div class="flex gap-2.5">
                <button onclick="removerAjuste()" class="flex-1 px-4 py-3 bg-red-700 hover:bg-red-600 text-white rounded-xl transition font-semibold flex items-center justify-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    <span>Remover</span>
                </button>
                <button onclick="confirmarAjuste()" class="flex-1 px-4 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl transition font-bold shadow-xl flex items-center justify-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Aplicar</span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let subtotal = {{ $total }};
let totalFinal = subtotal;
let formasPagamento = []; // Array para armazenar as formas de pagamento adicionadas
let formasPagamentoIndex = 0;
let formaPagamentoAtual = null; // Forma sendo adicionada no modal

const nomeFormas = {
    'dinheiro': 'Dinheiro',
    'pix': 'PIX',
    'debito': 'Débito',
    'credito': 'Crédito'
};

const iconeFormas = {
    'dinheiro': 'text-green-400',
    'pix': 'text-blue-400',
    'debito': 'text-purple-400',
    'credito': 'text-yellow-400'
};

function adicionarFormaPagamento(forma) {
    const valorRestante = totalFinal - getTotalPago();

    if (valorRestante <= 0) {
        mostrarNotificacao('O valor total já foi atingido!', 'warning');
        return;
    }

    formaPagamentoAtual = forma;
    document.getElementById('modalFormaTitulo').textContent = `Adicionar ${nomeFormas[forma]}`;
    document.getElementById('valorFormaPagamento').value = valorRestante.toFixed(2);
    document.getElementById('valorRestanteModal').textContent = 'R$ ' + valorRestante.toFixed(2).replace('.', ',');

    // Abrir modal
    document.getElementById('modalFormaPagamento').classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // Focus no input
    setTimeout(() => {
        document.getElementById('valorFormaPagamento').focus();
        document.getElementById('valorFormaPagamento').select();
    }, 100);
}

function fecharModalPagamento() {
    document.getElementById('modalFormaPagamento').classList.add('hidden');
    document.body.style.overflow = 'auto';
    formaPagamentoAtual = null;
    document.getElementById('valorFormaPagamento').value = '';
}

function confirmarFormaPagamento() {
    const valorInput = document.getElementById('valorFormaPagamento').value;
    const valorFloat = parseFloat(valorInput);

    if (isNaN(valorFloat) || valorFloat <= 0) {
        mostrarNotificacao('Digite um valor válido!', 'error');
        return;
    }

    const valorRestante = totalFinal - getTotalPago();
    if (valorFloat > valorRestante + 100) { // Permite troco de até R$ 100 a mais
        if (!confirm(`O valor informado (R$ ${valorFloat.toFixed(2).replace('.', ',')}) é maior que o restante (R$ ${valorRestante.toFixed(2).replace('.', ',')}). Deseja continuar?`)) {
            return;
        }
    }

    const formaPagamento = {
        index: formasPagamentoIndex++,
        forma: formaPagamentoAtual,
        valor: valorFloat
    };

    formasPagamento.push(formaPagamento);
    renderizarFormasPagamento();
    atualizarTotais();
    fecharModalPagamento();
    mostrarNotificacao(`${nomeFormas[formaPagamentoAtual]} adicionado!`, 'success');
}

function removerFormaPagamento(index) {
    formasPagamento = formasPagamento.filter(f => f.index !== index);
    renderizarFormasPagamento();
    atualizarTotais();
}

function getTotalPago() {
    return formasPagamento.reduce((total, forma) => total + forma.valor, 0);
}

// ====================
// MODAL DE AJUSTES (DESCONTO/ACRÉSCIMO)
// ====================
let tipoAjusteAtual = 'desconto'; // 'desconto' ou 'acrescimo'
let tipoValorAjuste = 'porcentagem'; // 'porcentagem' ou 'valor'

function abrirModalDesconto() {
    tipoAjusteAtual = 'desconto';
    tipoValorAjuste = 'porcentagem';

    document.getElementById('ajusteTitulo').textContent = 'Adicionar Desconto';
    document.getElementById('ajusteIcone').className = 'p-2.5 rounded-xl bg-gradient-to-r from-red-500 to-red-600';

    // Carregar valor atual se existir
    const tipoAtual = document.getElementById('tipo_desconto').value;
    if (tipoAtual === 'porcentagem') {
        tipoValorAjuste = 'porcentagem';
        document.getElementById('valorAjuste').value = document.getElementById('desconto_porcentagem').value || '';
    } else if (tipoAtual === 'valor') {
        tipoValorAjuste = 'valor';
        document.getElementById('valorAjuste').value = document.getElementById('desconto_valor').value || '';
    } else {
        document.getElementById('valorAjuste').value = '';
    }

    selecionarTipoAjuste(tipoValorAjuste);
    abrirModalAjuste();
}

function abrirModalAcrescimo() {
    tipoAjusteAtual = 'acrescimo';
    tipoValorAjuste = 'porcentagem';

    document.getElementById('ajusteTitulo').textContent = 'Adicionar Acréscimo';
    document.getElementById('ajusteIcone').className = 'p-2.5 rounded-xl bg-gradient-to-r from-yellow-500 to-yellow-600';

    // Carregar valor atual se existir
    const tipoAtual = document.getElementById('tipo_acrescimo').value;
    if (tipoAtual === 'porcentagem') {
        tipoValorAjuste = 'porcentagem';
        document.getElementById('valorAjuste').value = document.getElementById('acrescimo_porcentagem').value || '';
    } else if (tipoAtual === 'valor') {
        tipoValorAjuste = 'valor';
        document.getElementById('valorAjuste').value = document.getElementById('acrescimo_valor').value || '';
    } else {
        document.getElementById('valorAjuste').value = '';
    }

    selecionarTipoAjuste(tipoValorAjuste);
    abrirModalAjuste();
}

function abrirModalAjuste() {
    document.getElementById('modalAjuste').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    setTimeout(() => {
        document.getElementById('valorAjuste').focus();
        document.getElementById('valorAjuste').select();
    }, 100);
}

function fecharModalAjuste() {
    document.getElementById('modalAjuste').classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('valorAjuste').value = '';
}

function selecionarTipoAjuste(tipo) {
    tipoValorAjuste = tipo;

    // Atualizar botões
    document.querySelectorAll('.tipo-ajuste-btn').forEach(btn => {
        btn.classList.remove('border-blue-500', 'bg-blue-500/20');
        btn.classList.add('border-gray-600', 'bg-gray-700');
    });

    if (tipo === 'porcentagem') {
        document.getElementById('btnPorcentagem').classList.remove('border-gray-600', 'bg-gray-700');
        document.getElementById('btnPorcentagem').classList.add('border-blue-500', 'bg-blue-500/20');
        document.getElementById('simboloAjuste').textContent = '%';
    } else {
        document.getElementById('btnValor').classList.remove('border-gray-600', 'bg-gray-700');
        document.getElementById('btnValor').classList.add('border-blue-500', 'bg-blue-500/20');
        document.getElementById('simboloAjuste').textContent = 'R$';
    }
}

function confirmarAjuste() {
    const valor = parseFloat(document.getElementById('valorAjuste').value) || 0;

    if (valor <= 0) {
        mostrarNotificacao('Digite um valor válido!', 'error');
        return;
    }

    if (tipoAjusteAtual === 'desconto') {
        document.getElementById('tipo_desconto').value = tipoValorAjuste;
        if (tipoValorAjuste === 'porcentagem') {
            document.getElementById('desconto_porcentagem').value = valor;
            document.getElementById('desconto_valor').value = '0';
            document.getElementById('descontoAplicadoTexto').textContent = valor + '% de desconto';
        } else {
            document.getElementById('desconto_valor').value = valor;
            document.getElementById('desconto_porcentagem').value = '0';
            document.getElementById('descontoAplicadoTexto').textContent = 'R$ ' + valor.toFixed(2).replace('.', ',') + ' de desconto';
        }
        mostrarNotificacao('Desconto aplicado!', 'success');
    } else {
        document.getElementById('tipo_acrescimo').value = tipoValorAjuste;
        if (tipoValorAjuste === 'porcentagem') {
            document.getElementById('acrescimo_porcentagem').value = valor;
            document.getElementById('acrescimo_valor').value = '0';
            document.getElementById('acrescimoAplicadoTexto').textContent = valor + '% de acréscimo';
        } else {
            document.getElementById('acrescimo_valor').value = valor;
            document.getElementById('acrescimo_porcentagem').value = '0';
            document.getElementById('acrescimoAplicadoTexto').textContent = 'R$ ' + valor.toFixed(2).replace('.', ',') + ' de acréscimo';
        }
        mostrarNotificacao('Acréscimo aplicado!', 'success');
    }

    calcularTotal();
    fecharModalAjuste();
}

function removerAjuste() {
    if (tipoAjusteAtual === 'desconto') {
        document.getElementById('tipo_desconto').value = '';
        document.getElementById('desconto_porcentagem').value = '0';
        document.getElementById('desconto_valor').value = '0';
        document.getElementById('descontoAplicadoTexto').textContent = 'Nenhum desconto';
        mostrarNotificacao('Desconto removido!', 'info');
    } else {
        document.getElementById('tipo_acrescimo').value = '';
        document.getElementById('acrescimo_porcentagem').value = '0';
        document.getElementById('acrescimo_valor').value = '0';
        document.getElementById('acrescimoAplicadoTexto').textContent = 'Nenhum acréscimo';
        mostrarNotificacao('Acréscimo removido!', 'info');
    }

    calcularTotal();
    fecharModalAjuste();
}

// Eventos de teclado para modal de ajuste
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('valorAjuste').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            confirmarAjuste();
        }
    });

    // Fechar modal ao clicar fora
    document.getElementById('modalAjuste').addEventListener('click', function(e) {
        if (e.target === this) {
            fecharModalAjuste();
        }
    });
});

// ====================
// FIM MODAL DE AJUSTES
// ====================

function renderizarFormasPagamento() {
    const lista = document.getElementById('formas-pagamento-lista');
    const inputsContainer = document.getElementById('formas-pagamento-inputs');

    if (formasPagamento.length === 0) {
        lista.innerHTML = '<div class="text-center text-gray-400 text-sm py-2">Nenhuma forma de pagamento adicionada</div>';
        inputsContainer.innerHTML = '';
        return;
    }

    // Renderizar lista visual
    lista.innerHTML = formasPagamento.map(forma => `
        <div class="bg-gray-600 rounded-lg p-3 border border-gray-500 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <svg class="w-5 h-5 ${iconeFormas[forma.forma]}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <span class="text-white font-semibold">${nomeFormas[forma.forma]}</span>
                    <div class="text-green-400 font-bold text-sm">R$ ${forma.valor.toFixed(2).replace('.', ',')}</div>
                </div>
            </div>
            <button type="button" onclick="removerFormaPagamento(${forma.index})"
                class="text-red-400 hover:text-red-300 transition p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        </div>
    `).join('');

    // Renderizar inputs hidden para enviar no formulário
    inputsContainer.innerHTML = formasPagamento.map((forma, idx) => `
        <input type="hidden" name="formas_pagamento[${idx}][forma]" value="${forma.forma}">
        <input type="hidden" name="formas_pagamento[${idx}][valor]" value="${forma.valor}">
    `).join('');
}

function atualizarTotais() {
    const totalPago = getTotalPago();
    const restante = Math.max(0, totalFinal - totalPago);
    const troco = Math.max(0, totalPago - totalFinal);

    // Atualizar displays
    document.getElementById('displayTotalPago').textContent = 'R$ ' + totalPago.toFixed(2).replace('.', ',');
    document.getElementById('displayRestante').textContent = 'R$ ' + restante.toFixed(2).replace('.', ',');
    document.getElementById('valor_pago').value = totalPago.toFixed(2);

    // Atualizar cor do restante
    const restanteElement = document.getElementById('displayRestante');
    if (restante > 0) {
        restanteElement.classList.remove('text-green-400');
        restanteElement.classList.add('text-red-400');
        document.getElementById('restanteDiv').classList.remove('hidden');
        document.getElementById('trocoDiv').classList.add('hidden');
    } else {
        restanteElement.classList.remove('text-red-400');
        restanteElement.classList.add('text-green-400');
        document.getElementById('restanteDiv').classList.add('hidden');

        if (troco > 0) {
            document.getElementById('displayTroco').textContent = 'R$ ' + troco.toFixed(2).replace('.', ',');
            document.getElementById('trocoDiv').classList.remove('hidden');
        } else {
            document.getElementById('trocoDiv').classList.add('hidden');
        }
    }

    // Atualizar forma_pagamento principal (usar a primeira ou "multiplo")
    const formaPagamentoInput = document.getElementById('forma_pagamento');
    if (!formaPagamentoInput) {
        // Criar o input se não existir
        const form = document.getElementById('formPagamento');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'forma_pagamento';
        input.id = 'forma_pagamento';
        form.appendChild(input);
    }

    if (formasPagamento.length === 1) {
        document.getElementById('forma_pagamento').value = formasPagamento[0].forma;
    } else if (formasPagamento.length > 1) {
        document.getElementById('forma_pagamento').value = 'multiplo';
    } else {
        document.getElementById('forma_pagamento').value = '';
    }
}

function calcularTotal() {
    // Calcular desconto
    let valorDesconto = 0;
    const tipoDesconto = document.getElementById('tipo_desconto').value;

    if (tipoDesconto === 'porcentagem') {
        const descontoPorcentagem = parseFloat(document.getElementById('desconto_porcentagem').value) || 0;
        valorDesconto = (subtotal * descontoPorcentagem) / 100;
    } else {
        valorDesconto = parseFloat(document.getElementById('desconto_valor').value) || 0;
    }

    // Calcular acréscimo
    let valorAcrescimo = 0;
    const tipoAcrescimo = document.getElementById('tipo_acrescimo').value;

    if (tipoAcrescimo === 'porcentagem') {
        const acrescimoPorcentagem = parseFloat(document.getElementById('acrescimo_porcentagem').value) || 0;
        valorAcrescimo = (subtotal * acrescimoPorcentagem) / 100;
    } else {
        valorAcrescimo = parseFloat(document.getElementById('acrescimo_valor').value) || 0;
    }

    totalFinal = subtotal - valorDesconto + valorAcrescimo;

    // Atualizar display
    document.getElementById('displayDesconto').textContent = '- R$ ' + valorDesconto.toFixed(2).replace('.', ',');
    document.getElementById('displayAcrescimo').textContent = '+ R$ ' + valorAcrescimo.toFixed(2).replace('.', ',');
    document.getElementById('displayTotal').textContent = 'R$ ' + totalFinal.toFixed(2).replace('.', ',');

    // Mostrar/ocultar linhas de desconto e acréscimo
    if (valorDesconto > 0) {
        document.getElementById('descontoDiv').classList.remove('hidden');
    } else {
        document.getElementById('descontoDiv').classList.add('hidden');
    }

    if (valorAcrescimo > 0) {
        document.getElementById('acrescimoDiv').classList.remove('hidden');
    } else {
        document.getElementById('acrescimoDiv').classList.add('hidden');
    }

    // Atualizar restante inicial
    document.getElementById('displayRestante').textContent = 'R$ ' + totalFinal.toFixed(2).replace('.', ',');

    // Atualizar totais de pagamento
    atualizarTotais();
}

// Inicializar com valores ocultos
document.getElementById('descontoDiv').classList.add('hidden');
document.getElementById('acrescimoDiv').classList.add('hidden');

// Inicializar formas de pagamento
renderizarFormasPagamento();
atualizarTotais();

// Validação do formulário antes de enviar
document.getElementById('formPagamento').addEventListener('submit', function(e) {
    const totalPago = getTotalPago();
    if (totalPago < totalFinal) {
        e.preventDefault();
        mostrarNotificacao(`O valor pago (R$ ${totalPago.toFixed(2).replace('.', ',')}) é menor que o total da conta (R$ ${totalFinal.toFixed(2).replace('.', ',')}). Adicione mais formas de pagamento.`, 'error');
        return false;
    }

    if (formasPagamento.length === 0) {
        e.preventDefault();
        mostrarNotificacao('Adicione pelo menos uma forma de pagamento!', 'error');
        return false;
    }
});

// Eventos de teclado para o modal
document.getElementById('valorFormaPagamento').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        confirmarFormaPagamento();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modalPagamento = document.getElementById('modalFormaPagamento');
        const modalAjuste = document.getElementById('modalAjuste');

        if (!modalPagamento.classList.contains('hidden')) {
            fecharModalPagamento();
        } else if (!modalAjuste.classList.contains('hidden')) {
            fecharModalAjuste();
        }
    }
});

// Fechar modal ao clicar fora
document.getElementById('modalFormaPagamento').addEventListener('click', function(e) {
    if (e.target === this) {
        fecharModalPagamento();
    }
});

// Sistema de notificações
function mostrarNotificacao(mensagem, tipo = 'success') {
    const cores = {
        success: 'from-green-600 to-green-700 border-green-500',
        error: 'from-red-600 to-red-700 border-red-500',
        warning: 'from-yellow-600 to-yellow-700 border-yellow-500',
        info: 'from-blue-600 to-blue-700 border-blue-500'
    };

    const icones = {
        success: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>',
        error: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>',
        warning: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>',
        info: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
    };

    // Remover notificação existente
    const existente = document.getElementById('toast-notification');
    if (existente) existente.remove();

    const toast = document.createElement('div');
    toast.id = 'toast-notification';
    toast.className = `fixed top-20 right-4 bg-gradient-to-r ${cores[tipo]} border-l-4 text-white px-6 py-4 rounded-xl shadow-2xl z-50 transform translate-x-0 transition-all duration-300 max-w-md`;
    toast.innerHTML = `
        <div class="flex items-center space-x-3">
            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${icones[tipo]}
            </svg>
            <span class="font-medium">${mensagem}</span>
        </div>
    `;

    document.body.appendChild(toast);

    setTimeout(() => toast.style.transform = 'translateX(0)', 10);
    setTimeout(() => {
        toast.style.transform = 'translateX(400px)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Alterar quantidade de item (AJAX - sem reload)
function alterarQuantidade(itemId, delta, precoUnitario) {
    const qtdSpan = document.getElementById(`item-qtd-${itemId}`);
    let qtdAtual = parseInt(qtdSpan.textContent);
    let novaQtd = qtdAtual + delta;

    if (novaQtd <= 0) {
        if (confirm('Deseja remover este item?')) {
            cancelarItem(itemId, '');
        }
        return;
    }

    // Desabilitar botões durante a requisição
    const botoes = document.querySelectorAll(`button[onclick*="${itemId}"]`);
    botoes.forEach(btn => btn.disabled = true);

    // Mostrar loading
    showLoading('Atualizando quantidade...');

    fetch(`/garcom/item/${itemId}/atualizar-quantidade`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ quantidade: novaQtd })
    })
    .then(response => response.json()) // Sempre tentar parsear JSON, independente do status
    .then(data => {
        if (data.success) {
            // Atualizar quantidade
            qtdSpan.textContent = novaQtd + 'x';

            // Atualizar subtotal do item
            const novoTotal = novaQtd * precoUnitario;
            document.getElementById(`item-total-${itemId}`).textContent =
                'R$ ' + novoTotal.toFixed(2).replace('.', ',');

            // Atualizar total geral dinamicamente
            atualizarTotalGeral();

            // Feedback visual
            qtdSpan.classList.add('text-yellow-400');
            setTimeout(() => qtdSpan.classList.remove('text-yellow-400'), 300);
        } else {
            alert(data.message || 'Erro ao atualizar quantidade');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao processar resposta do servidor');
    })
    .finally(() => {
        // Reabilitar botões
        botoes.forEach(btn => btn.disabled = false);
        // Esconder loading
        hideLoading();
    });
}

// Atualizar total geral sem reload
function atualizarTotalGeral() {
    let novoTotal = 0;

    // Somar todos os itens visíveis
    document.querySelectorAll('[id^="item-total-"]').forEach(el => {
        const valor = el.textContent.replace('R$', '').replace('.', '').replace(',', '.').trim();
        novoTotal += parseFloat(valor) || 0;
    });

    // Atualizar subtotal
    subtotal = novoTotal;
    document.getElementById('displaySubtotal').textContent = 'R$ ' + novoTotal.toFixed(2).replace('.', ',');
    document.getElementById('totalResumo').textContent = 'R$ ' + novoTotal.toFixed(2).replace('.', ',');

    // Recalcular total com desconto/acréscimo
    calcularTotal();
}

// Cancelar item (AJAX - sem reload)
function cancelarItem(itemId, nomeProduto) {
    const msg = nomeProduto ? `Cancelar "${nomeProduto}"?` : 'Cancelar este item?';

    if (!confirm(msg)) return;

    // Mostrar loading
    showLoading('Cancelando item...');

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
            // Se cancelou o último item do pedido, recarregar
            if (data.pedido_cancelado) {
                alert(data.message);
                window.location.href = '{{ isset($source) && $source === "garcom" ? route("garcom.index") : route("pdv.index") }}';
                return;
            }

            // Remover item da interface com animação
            const itemElement = document.querySelector(`#item-qtd-${itemId}`).closest('.mb-3');
            itemElement.style.opacity = '0';
            itemElement.style.transform = 'translateX(100%)';
            itemElement.style.transition = 'all 0.3s ease';

            setTimeout(() => {
                itemElement.remove();

                // Atualizar total
                atualizarTotalGeral();

                // Verificar se ainda há itens
                const itensRestantes = document.querySelectorAll('[id^="item-qtd-"]').length;
                if (itensRestantes === 0) {
                    alert('Todos os itens foram removidos. Redirecionando...');
                    window.location.href = '{{ isset($source) && $source === "garcom" ? route("garcom.index") : route("pdv.index") }}';
                }
            }, 300);
        } else {
            alert(data.message || 'Erro ao cancelar item');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao cancelar item');
    })
    .finally(() => {
        // Esconder loading
        hideLoading();
    });
}
</script>
@endpush
@endsection
