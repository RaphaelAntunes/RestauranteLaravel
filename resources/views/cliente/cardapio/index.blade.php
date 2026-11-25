@extends('layouts.cliente')
@section('title', 'Cardápio')
@section('content')
<div class="db-menu">
    <nav class="db-bar"><div class="db-wrap db-row">
        <a href="{{ route('cliente.cardapio') }}" aria-label="{{ $lojaAtual->nome }}, início do cardápio"><x-dobra-brand/></a>
        <div class="db-actions">
            @auth('cliente')
            <details class="db-account"><summary><x-dobra-icon name="person"/><span>Minha conta</span></summary><div>
                <a href="{{ route('cliente.pedidos') }}">Meus pedidos</a>
                <a href="{{ route('cliente.perfil.edit') }}">Meu perfil</a>
                <a href="{{ route('cliente.enderecos.index') }}">Meus endereços</a>
                <form method="POST" action="{{ route('cliente.logout') }}">@csrf<button>Sair</button></form>
            </div></details>
            @else
            <a class="db-nav-link" href="{{ route('cliente.pedidos') }}"><x-dobra-icon name="bill"/>Meus pedidos</a>
            @endauth
            <button class="db-bag-link" onclick="abrirModalCarrinho()" id="nav-cart-btn" aria-label="Abrir sacola"><x-dobra-icon name="bag"/> Sacola <span id="nav-cart-count" class="hidden">0</span></button>
        </div>
    </div></nav>
    @php
        $casa = $lojaAtual->nome;
        $aberto = $config->isDeliveryAberto();
        $visiveis = $categorias->filter(fn ($categoria) => $categoria->produtosAtivos->isNotEmpty());
        $heroBannerDesktop = $lojaAtual->banner_path
            ? asset('storage/'.$lojaAtual->banner_path)
            : asset('dobra/banner-padrao.avif');
        $heroBannerMobile = $lojaAtual->banner_mobile_path
            ? asset('storage/'.$lojaAtual->banner_mobile_path)
            : $heroBannerDesktop;
    @endphp
    <header class="db-hero {{ $lojaAtual->exibir_textos_banner ? '' : 'db-hero-art-only' }}" style="--db-banner-desktop:url('{{ $heroBannerDesktop }}');--db-banner-mobile:url('{{ $heroBannerMobile }}')" aria-label="Banner da {{ $lojaAtual->nome }}"><div class="db-wrap db-hero-inner">
    @if($lojaAtual->exibir_textos_banner)<div class="db-hero-copy">
        @if($casa)<p>{{ $casa }}</p>@endif
        <h1>{{ $lojaAtual->titulo_cardapio ?: 'Cardápio da casa' }}</h1>
        @if($lojaAtual->descricao)<p style="max-width:36rem">{{ $lojaAtual->descricao }}</p>@endif
    </div>@endif
    <div class="db-hero-ops" aria-label="Informações de atendimento">
        <span class="db-status {{ $aberto ? 'is-open' : 'is-closed' }}">{{ $aberto ? 'Aberto para pedidos' : 'Pedidos fechados' }}</span>
        @if($config->tempo_medio_preparo)<span class="db-prep"><x-dobra-icon name="clock"/> Preparo: {{ $config->tempo_medio_preparo }} min</span>@endif
    </div>
    </div></header>

    <div class="db-mobile-cats"><div class="db-wrap" id="categorias-mobile-container">
        @foreach($visiveis as $categoria)<button type="button" class="category-link-mobile" data-categoria="{{ $categoria->id }}" onclick="scrollToCategoria('{{ $categoria->id }}')">{{ $categoria->nome }}</button>@endforeach
    </div></div>
    <div class="db-wrap db-catalog">
        <aside class="db-index"><h2>No cardápio</h2><nav aria-label="Categorias">@foreach($visiveis as $categoria)
            <a class="category-link" href="#categoria-{{ $categoria->id }}" data-categoria="{{ $categoria->id }}">{{ $categoria->nome }} <small>{{ $categoria->produtosAtivos->count() }}</small></a>
        @endforeach</nav></aside>
        <section class="db-dishes" aria-label="Produtos"><label class="db-search" for="search-input"><svg class="db-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="10" cy="10" r="6"/><path d="m15 15 6 6"/></svg><input type="search" id="search-input" placeholder="Buscar no cardápio" aria-label="Buscar no cardápio"></label>
        <p id="db-no-results" class="db-empty" role="status" hidden>Nenhum prato encontrado. Tente outro nome.</p>
        @forelse($visiveis as $categoria)
            <section id="categoria-{{ $categoria->id }}" class="category-section"><h2>{{ $categoria->nome }}</h2>
            @foreach($categoria->produtosAtivos as $produto)
                @php
                    $variavel = $produto->tamanhos->isNotEmpty();
                    $tipo = \Illuminate\Support\Str::ascii(mb_strtolower($categoria->nome.' '.$produto->nome));
                    $icone = match(true) {
                        str_contains($tipo,'pizz') => 'pizza', str_contains($tipo,'bebida') => 'cup',
                        str_contains($tipo,'batata') => 'fries', str_contains($tipo,'sobremesa') => 'dessert',
                        str_contains($tipo,'salada') => 'leaf', default => 'plate'
                    };
                @endphp
                <button type="button" class="product-card db-product" data-produto="{{ mb_strtolower($produto->nome) }}"
                    onclick="@if($variavel) abrirModalPizza({{ $produto->id }}, {{ \Illuminate\Support\Js::from($produto->nome) }}, {{ \Illuminate\Support\Js::from($produto->tamanhos) }}, {{ \Illuminate\Support\Js::from($categoria->nome) }}) @else adicionarProdutoSimples({{ $produto->id }}, {{ \Illuminate\Support\Js::from($produto->nome) }}, {{ $produto->preco }}, this) @endif">
                    <span class="db-thumb db-thumb-{{ $icone }}">@if($produto->imagem)<img src="{{ asset('storage/' . $produto->imagem) }}" alt="" loading="lazy">@else<x-dobra-icon :name="$icone"/>@endif</span>
                    <span class="db-dish-copy"><strong>{{ $produto->nome }}</strong>@if($produto->descricao)<span>{{ $produto->descricao }}</span>@endif
                        <b>@if($variavel)<small>A partir de </small>@endif R$ {{ number_format($variavel ? $produto->tamanhos->min('preco') : $produto->preco, 2, ',', '.') }}</b>
                    </span><span class="db-add" aria-hidden="true">+</span>
                </button>
            @endforeach</section>
        @empty<p class="db-empty">Ainda não há itens disponíveis no cardápio.</p>@endforelse
        </section>
        <aside class="db-ticket"><header><x-dobra-icon name="bill"/><h2>Sua sacola</h2></header><div id="sidebar-cart"><div id="sidebar-cart-content"></div></div></aside>
    </div>

    <!-- Mobile Cart Button - Fixed at bottom with safe area -->
    <div id="mobile-cart-btn" class="lg:hidden fixed bottom-0 left-0 right-0 p-3 bg-gray-900/98 backdrop-blur-lg border-t border-gray-700 z-50 hidden safe-bottom">
        <button onclick="abrirModalCarrinho()"
           class="touch-feedback flex items-center justify-between w-full bg-gradient-to-r from-red-500 to-red-600 text-white px-5 py-4 rounded-2xl font-bold shadow-2xl shadow-red-500/30">
            <div class="flex items-center gap-3">
                <div class="bg-white/20 rounded-xl p-2.5">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <div class="text-left">
                    <span class="text-base">Ver Carrinho</span>
                    <span class="text-white/80 text-sm ml-1">(<span id="mobile-cart-count">0</span> itens)</span>
                </div>
            </div>
            <div class="text-right">
                <span class="font-bold text-xl" id="mobile-cart-total">R$ 0,00</span>
            </div>
        </button>
    </div>

    @if($whatsappLink = $lojaAtual->whatsappLink())
        <a class="db-whatsapp-float" href="{{ $whatsappLink }}" target="_blank" rel="noopener noreferrer" aria-label="Conversar com {{ $lojaAtual->nome }} pelo WhatsApp">
            <svg viewBox="0 0 32 32" aria-hidden="true"><path fill="currentColor" d="M16.04 3C8.86 3 3.02 8.82 3.02 15.98c0 2.29.6 4.53 1.74 6.5L3 29l6.67-1.75a13.02 13.02 0 0 0 6.36 1.62h.01C23.2 28.87 29 23.04 29 15.98 29 8.82 23.2 3 16.04 3Zm7.63 18.34c-.32.9-1.87 1.71-2.58 1.82-.66.1-1.5.15-2.42-.15-.56-.18-1.29-.42-2.22-.82-3.9-1.68-6.44-5.6-6.64-5.87-.2-.26-1.58-2.1-1.58-4s.99-2.84 1.34-3.23c.35-.39.76-.49 1.02-.49h.73c.23.01.55-.08.86.66.32.77 1.09 2.66 1.18 2.85.1.2.16.43.03.69-.13.26-.2.42-.39.65-.2.23-.41.51-.58.68-.2.2-.4.41-.17.8.23.39 1.02 1.68 2.19 2.72 1.5 1.34 2.77 1.76 3.16 1.95.39.2.62.16.85-.1.23-.26.99-1.15 1.25-1.54.26-.39.52-.32.88-.2.36.13 2.3 1.09 2.69 1.28.39.2.65.29.75.45.1.16.1.94-.22 1.84Z"/></svg>
            <span>Fale com a loja</span>
        </a>
    @endif
</div>

<!-- Modal de Seleção de Pizza -->
<div id="modalPizza" class="modal hidden fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-2 sm:p-4" onclick="fecharModalPizza()">
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

            <!-- Quantidade -->
            <div>
                <h4 class="text-base sm:text-lg font-semibold text-white mb-3">Quantidade</h4>
                <div class="flex items-center justify-center space-x-4">
                    <button type="button" onclick="alterarQuantidadeModal(-1)" class="touch-feedback w-12 h-12 rounded-xl bg-gray-700 hover:bg-gray-600 flex items-center justify-center text-white font-bold transition shadow-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"></path>
                        </svg>
                    </button>
                    <span id="modal-quantidade" class="text-white font-bold text-2xl w-14 text-center">1</span>
                    <button type="button" onclick="alterarQuantidadeModal(1)" class="touch-feedback w-12 h-12 rounded-xl bg-gray-700 hover:bg-gray-600 flex items-center justify-center text-white font-bold transition shadow-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Observações -->
            <div>
                <label for="pizza-observacoes" class="block text-xs sm:text-sm font-medium text-white mb-2">Observações</label>
                <textarea id="pizza-observacoes" rows="2" class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base bg-gray-700 border border-gray-600 rounded-lg text-gray-100 focus:ring-2 focus:ring-red-500" placeholder="Ex: Sem cebola, borda recheada, etc"></textarea>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-4 border-t border-gray-700 bg-gray-800/50 safe-bottom">
            <div class="flex flex-col gap-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Total:</span>
                    <span class="text-white font-bold text-2xl" id="modal-preco-total">R$ 0,00</span>
                </div>
                <button onclick="adicionarPizzaAoCarrinho()" id="btn-adicionar-pizza" class="touch-feedback w-full py-4 text-base bg-gradient-to-r from-red-500 to-orange-500 text-white rounded-xl font-bold shadow-lg shadow-red-500/30 transition">
                    Adicionar ao Carrinho
                </button>
            </div>
        </div>
    </div>
</div>

@include('cliente.cardapio.ficha-item')

<!-- Toast Container - Centered on mobile, right on desktop -->
<div id="toast-container" class="fixed top-20 left-4 right-4 sm:left-auto sm:right-4 sm:w-80 z-[100] space-y-2 pointer-events-none"></div>

<!-- Modal do Carrinho -->
<div id="modalCarrinho" class="modal db-cart-modal hidden fixed inset-0 flex items-center justify-center z-50 p-2 sm:p-4" onclick="fecharModalCarrinho()">
    <div class="db-cart-panel" role="dialog" aria-modal="true" aria-labelledby="carrinho-titulo" onclick="event.stopPropagation()">
        <!-- Header -->
        <header class="db-cart-header">
            <div class="db-cart-heading">
                <span class="db-cart-icon" aria-hidden="true">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </span>
                <div><h3 id="carrinho-titulo">Minha sacola</h3><p>Revise os itens antes de continuar</p></div>
            </div>
            <button type="button" onclick="fecharModalCarrinho()" class="db-cart-close" aria-label="Fechar sacola">×</button>
        </header>

        <!-- Body -->
        <div class="db-cart-body" id="carrinho-body">
            <div class="text-center py-8" id="carrinho-loading">
                <svg class="animate-spin h-8 w-8 mx-auto text-blue-700" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="db-cart-muted mt-2">Carregando sua sacola...</p>
            </div>
            <div id="carrinho-vazio" class="hidden text-center py-8">
                <svg class="mx-auto w-16 h-16 db-cart-empty-icon mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <p class="db-cart-empty-title">Sua sacola está vazia</p>
                <p class="db-cart-muted text-sm">Escolha um item do cardápio para começar.</p>
            </div>
            <div id="carrinho-itens" class="db-cart-items hidden"></div>
        </div>

        <!-- Footer -->
        <footer class="db-cart-footer safe-bottom" id="carrinho-footer">
            <div id="carrinho-resumo" class="hidden">
                <div class="db-cart-summary">
                    <div>
                        <span>Subtotal</span>
                        <span id="carrinho-subtotal">R$ 0,00</span>
                    </div>
                    <div>
                        <span>Taxa de entrega</span>
                        <span id="carrinho-taxa">R$ 0,00</span>
                    </div>
                    <div class="db-cart-total">
                        <span>Total</span>
                        <span id="carrinho-total">R$ 0,00</span>
                    </div>
                </div>
                <button onclick="abrirCheckout()" id="btn-checkout" class="touch-feedback db-cart-checkout">
                    Finalizar Pedido
                </button>
                <p id="pedido-minimo-aviso" class="db-cart-warning hidden"></p>
            </div>
        </footer>
    </div>
</div>

<!-- Modal de Checkout -->
<div id="modalCheckout" class="modal hidden fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-2 sm:p-4" onclick="fecharModalCheckout()">
    <div class="bg-gray-800 rounded-2xl max-w-lg w-full max-h-[95vh] sm:max-h-[90vh] overflow-hidden border border-gray-700 shadow-2xl flex flex-col" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="p-4 sm:p-6 border-b border-gray-700 bg-gradient-to-r from-green-500 to-green-600 flex-shrink-0">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    <h3 class="text-lg sm:text-xl font-bold text-white">Finalizar Pedido</h3>
                </div>
                <button onclick="fecharModalCheckout()" class="text-white hover:bg-white/20 rounded-lg p-1.5 sm:p-2 transition">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Body -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4" id="checkout-body">
            <div id="nome-cliente-section" class="hidden">
                <label for="checkout-nome-cliente" class="text-white font-semibold mb-2 block">Como podemos chamar você?</label>
                <input id="checkout-nome-cliente" type="text" maxlength="100" autocomplete="name"
                       class="w-full px-3 py-3 bg-gray-700 border border-gray-600 rounded-lg text-gray-100 focus:ring-2 focus:ring-green-500"
                       placeholder="Digite seu nome">
                <p class="text-gray-400 text-xs mt-2">Você só precisa informar uma vez.</p>
            </div>

            <!-- Tipo de Pedido -->
            <div>
                <h4 class="text-white font-semibold mb-3">Como você quer receber?</h4>
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" onclick="selecionarTipoPedido('delivery')" id="tipo-delivery" class="touch-feedback p-5 border-2 border-gray-600 rounded-2xl text-center transition-all">
                        <div class="w-14 h-14 mx-auto mb-3 bg-gray-700 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <span class="text-white font-semibold block">Delivery</span>
                        <span class="text-gray-500 text-xs">Receba em casa</span>
                    </button>
                    <button type="button" onclick="selecionarTipoPedido('retirada')" id="tipo-retirada" class="touch-feedback p-5 border-2 border-gray-600 rounded-2xl text-center transition-all">
                        <div class="w-14 h-14 mx-auto mb-3 bg-gray-700 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <span class="text-white font-semibold block">Retirada</span>
                        <span class="text-gray-500 text-xs">Busque no local</span>
                    </button>
                </div>
            </div>

            <!-- Endereço (apenas para delivery) -->
            <div id="endereco-section" class="hidden">
                <h4 class="text-white font-semibold mb-3">Endereço de Entrega</h4>
                <div id="enderecos-lista" class="space-y-2"></div>
                <button type="button" onclick="abrirNovoEndereco()" class="w-full mt-3 py-2 border-2 border-dashed border-gray-600 rounded-xl text-gray-400 hover:border-green-500 hover:text-green-400 transition">
                    + Adicionar novo endereço
                </button>
            </div>

            <!-- Observações -->
            <div>
                <label class="text-white font-semibold mb-2 block">Observações do Pedido</label>
                <textarea id="checkout-observacoes" rows="2" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-100 focus:ring-2 focus:ring-green-500" placeholder="Alguma observação especial?"></textarea>
            </div>

            <!-- Resumo do Pedido -->
            <div class="bg-gray-700/50 rounded-xl p-4">
                <h4 class="text-white font-semibold mb-3">Resumo</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-gray-400">
                        <span>Subtotal</span>
                        <span id="checkout-subtotal">R$ 0,00</span>
                    </div>
                    <div class="flex justify-between text-gray-400" id="checkout-taxa-row">
                        <span>Taxa de entrega</span>
                        <span id="checkout-taxa">R$ 0,00</span>
                    </div>
                    <div class="flex justify-between text-white font-bold text-lg pt-2 border-t border-gray-600">
                        <span>Total</span>
                        <span id="checkout-total">R$ 0,00</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-4 border-t border-gray-700 bg-gray-800/50 flex-shrink-0 safe-bottom">
            <button onclick="finalizarPedido()" id="btn-finalizar" class="touch-feedback w-full py-4 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl font-bold shadow-lg shadow-green-500/30 transition text-base disabled:opacity-50 disabled:shadow-none disabled:cursor-not-allowed">
                Confirmar Pedido
            </button>
        </div>
    </div>
</div>

<!-- Modal de Acompanhamento do Pedido -->
<div id="modalAcompanhamento" class="modal hidden fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-2 sm:p-4">
    <div class="bg-gray-800 rounded-2xl max-w-lg w-full max-h-[95vh] sm:max-h-[90vh] overflow-hidden border border-gray-700 shadow-2xl flex flex-col">
        <!-- Header -->
        <div class="p-4 sm:p-6 border-b border-gray-700 bg-gradient-to-r from-blue-500 to-blue-600 flex-shrink-0">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg sm:text-xl font-bold text-white">Acompanhar Pedido</h3>
                    <p class="text-white/80 text-sm" id="acomp-numero"></p>
                </div>
                <button onclick="fecharModalAcompanhamento()" class="text-white hover:bg-white/20 rounded-lg p-1.5 sm:p-2 transition">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Body -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4">
            <!-- Status Progress -->
            <div class="bg-gray-700/50 rounded-xl p-4">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-white font-semibold" id="acomp-status-label">Aguardando...</span>
                    <span class="text-gray-400 text-sm" id="acomp-previsao"></span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="flex-1 h-2 bg-gray-600 rounded-full overflow-hidden">
                        <div id="acomp-progress-bar" class="h-full bg-gradient-to-r from-green-500 to-green-400 transition-all duration-500" style="width: 0%"></div>
                    </div>
                </div>
                <div class="flex justify-between mt-2 text-xs text-gray-500">
                    <span>Recebido</span>
                    <span>Preparando</span>
                    <span>Pronto</span>
                    <span>Entregue</span>
                </div>
            </div>

            <!-- Detalhes do Pedido -->
            <div class="bg-gray-700/50 rounded-xl p-4">
                <h4 class="text-white font-semibold mb-3">Itens do Pedido</h4>
                <div id="acomp-itens" class="space-y-2 text-sm"></div>
            </div>

            <!-- Endereço -->
            <div id="acomp-endereco-section" class="bg-gray-700/50 rounded-xl p-4 hidden">
                <h4 class="text-white font-semibold mb-2">Endereço de Entrega</h4>
                <p id="acomp-endereco" class="text-gray-400 text-sm"></p>
            </div>

            <!-- Total -->
            <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-xl p-4">
                <div class="flex justify-between items-center">
                    <span class="text-white font-bold">Total do Pedido</span>
                    <span class="text-white font-bold text-xl" id="acomp-total">R$ 0,00</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-4 border-t border-gray-700 bg-gray-750 flex-shrink-0">
            <button onclick="fecharModalAcompanhamento()" class="w-full py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-xl font-bold transition">
                Continuar Comprando
            </button>
        </div>
    </div>
</div>

<!-- Modal de Login Necessário -->
<div id="modalLoginNecessario" class="modal hidden fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4" onclick="fecharModalLogin()">
    <div class="bg-gray-800 rounded-2xl max-w-sm w-full overflow-hidden border border-gray-700 shadow-2xl" onclick="event.stopPropagation()">
        <div class="p-6 text-center">
            <div class="w-16 h-16 mx-auto mb-4 bg-yellow-500/20 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">Login Necessário</h3>
            <p class="text-gray-400 mb-6">Faça login para finalizar seu pedido</p>
            <a href="{{ route('cliente.login', ['origem' => 'checkout']) }}" class="block w-full py-3 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-xl font-bold transition">
                Fazer Login
            </a>
            <button onclick="fecharModalLogin()" class="w-full mt-3 py-2 text-gray-400 hover:text-white transition">
                Continuar Comprando
            </button>
        </div>
    </div>
</div>

@push('styles')
<style>
    [x-cloak] { display: none !important; }

    /* Prevent overscroll/bounce on iOS */
    html, body {
        overscroll-behavior: none;
        -webkit-overflow-scrolling: touch;
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    html {
        scroll-behavior: smooth;
    }

    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    /* Category links */
    .category-link.active {
        background-color: rgba(239, 68, 68, 0.1);
        color: #f87171;
        border-color: rgba(239, 68, 68, 0.3);
    }

    .category-link-mobile.active {
        background-color: rgba(239, 68, 68, 0.2);
        color: #f87171;
        border-color: rgba(239, 68, 68, 0.5);
    }

    /* Touch feedback for buttons */
    .touch-feedback {
        -webkit-tap-highlight-color: transparent;
        transition: transform 0.1s ease, opacity 0.1s ease;
    }
    .touch-feedback:active {
        transform: scale(0.97);
        opacity: 0.9;
    }

    /* Product card hover/active states */
    .product-card {
        -webkit-tap-highlight-color: transparent;
        transition: all 0.2s ease;
    }
    .product-card:active {
        transform: scale(0.98);
        background-color: #e8ece9;
    }

    /* Improved toast animations */
    .toast-enter {
        animation: toastSlideIn 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }
    .toast-exit {
        animation: toastSlideOut 0.3s ease-in forwards;
    }
    @keyframes toastSlideIn {
        from { transform: translateY(-100%); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    @keyframes toastSlideOut {
        from { transform: translateY(0); opacity: 1; }
        to { transform: translateY(-100%); opacity: 0; }
    }

    /* Modal animations */
    .modal {
        transition: opacity 0.2s ease;
    }
    .modal > div {
        animation: modalSlideUp 0.3s ease-out;
    }
    @keyframes modalSlideUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    /* Skeleton loading animation */
    .skeleton {
        background: linear-gradient(90deg, #374151 25%, #4b5563 50%, #374151 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
    }
    @keyframes shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    /* Pulse animation for cart badge */
    .cart-badge-pulse {
        animation: pulse 0.5s ease-out;
    }
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.3); }
        100% { transform: scale(1); }
    }

    /* Button loading state */
    .btn-loading {
        position: relative;
        pointer-events: none;
    }
    .btn-loading::after {
        content: '';
        position: absolute;
        inset: 0;
        background: inherit;
        border-radius: inherit;
    }

    /* Safe area for mobile devices with notch */
    .safe-bottom {
        padding-bottom: max(1rem, env(safe-area-inset-bottom));
    }

    /* Improved focus states for accessibility */
    button:focus-visible, a:focus-visible, input:focus-visible, textarea:focus-visible {
        outline: 2px solid #ef4444;
        outline-offset: 2px;
    }

    /* Minimum touch target size (44x44px) */
    .min-touch {
        min-width: 44px;
        min-height: 44px;
    }

    /* Success animation */
    .success-checkmark {
        animation: checkmark 0.5s ease-out;
    }
    @keyframes checkmark {
        0% { transform: scale(0) rotate(-45deg); opacity: 0; }
        50% { transform: scale(1.2) rotate(-45deg); }
        100% { transform: scale(1) rotate(0deg); opacity: 1; }
    }

    /* A sacola usa a mesma linguagem clara e direta da ficha do produto. */
    .db-cart-modal {
        color-scheme: light;
        background: rgb(24 30 52 / 45%);
        backdrop-filter: blur(3px);
    }
    .db-cart-panel {
        width: min(520px, calc(100% - 32px));
        max-height: 90vh;
        max-height: 90dvh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        border: 1px solid var(--linha);
        border-radius: 22px;
        background: var(--papel);
        color: var(--tinta);
        box-shadow: 0 18px 70px #131c4229;
    }
    .db-cart-header,
    .db-cart-heading,
    .db-cart-summary > div,
    .db-cart-item-top,
    .db-cart-item-bottom,
    .db-cart-stepper {
        display: flex;
        align-items: center;
    }
    .db-cart-header {
        flex: none;
        justify-content: space-between;
        gap: 16px;
        padding: 20px 22px 16px;
        border-bottom: 1px solid var(--linha);
    }
    .db-cart-heading { gap: 12px; min-width: 0; }
    .db-cart-heading h3 {
        font: 750 1.55rem/1.05 var(--display);
        letter-spacing: -.04em;
    }
    .db-cart-heading p { margin-top: 3px; color: var(--sec); font-size: .82rem; }
    .db-cart-icon {
        width: 46px;
        height: 46px;
        flex: none;
        display: grid;
        place-items: center;
        border-radius: 14px 9px 14px 9px;
        background: var(--manteiga);
        color: #75521b;
    }
    .db-cart-icon svg { width: 23px; height: 23px; }
    .db-cart-close {
        width: 44px;
        height: 44px;
        flex: none;
        border: 1px solid var(--linha);
        border-radius: 50%;
        background: #fff;
        color: var(--sec);
        font: 400 26px/1 var(--texto);
    }
    .db-cart-close:hover { border-color: #b6bed9; color: var(--louca); }
    .db-cart-body {
        min-height: 0;
        flex: 1;
        overflow-y: auto;
        padding: 18px 22px;
        background: #fafbf9;
        overscroll-behavior: contain;
    }
    .db-cart-muted { color: var(--sec); }
    .db-cart-empty-icon { color: #a9b1bd; }
    .db-cart-empty-title { margin-bottom: 4px; font-weight: 700; color: var(--tinta); }
    .db-cart-items { display: grid; gap: 12px; }
    .db-cart-item {
        padding: 14px;
        border: 1px solid var(--linha);
        border-radius: 16px;
        background: #fff;
    }
    .db-cart-item-top { align-items: flex-start; gap: 12px; }
    .db-cart-item-thumb {
        width: 66px;
        height: 66px;
        flex: none;
        display: grid;
        place-items: center;
        overflow: hidden;
        border-radius: 17px 10px 17px 10px;
        background: #ffe6a8;
        color: #8a6b2e;
    }
    .db-cart-item-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .db-cart-item-thumb svg { width: 28px; height: 28px; }
    .db-cart-item-copy { flex: 1; min-width: 0; }
    .db-cart-item-title { font: 720 1rem/1.25 var(--display); letter-spacing: -.02em; overflow-wrap: anywhere; }
    .db-cart-item-detail { margin-top: 2px; color: var(--sec); font-size: .82rem; line-height: 1.35; }
    .db-cart-item-note { margin-top: 5px; color: #75521b; font-size: .78rem; font-style: italic; }
    .db-cart-remove {
        width: 40px;
        height: 40px;
        flex: none;
        display: grid;
        place-items: center;
        border-radius: 50%;
        color: #7a8391;
    }
    .db-cart-remove:hover { background: #fff0ed; color: #923b30; }
    .db-cart-remove svg { width: 18px; height: 18px; }
    .db-cart-item-bottom {
        justify-content: space-between;
        gap: 16px;
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid var(--linha);
    }
    .db-cart-stepper { overflow: hidden; border: 1px solid #c4cbe3; border-radius: 12px; }
    .db-cart-stepper button { width: 42px; height: 40px; color: var(--louca); font-size: 1.35rem; }
    .db-cart-stepper button:hover { background: #eff1ff; }
    .db-cart-stepper output { min-width: 32px; text-align: center; font-weight: 700; font-variant-numeric: tabular-nums; }
    .db-cart-price { color: var(--louca); font-weight: 750; font-variant-numeric: tabular-nums; white-space: nowrap; }
    .db-cart-footer { flex: none; padding: 15px 22px 20px; border-top: 1px solid var(--linha); background: #fff; }
    .db-cart-summary { display: grid; gap: 8px; margin-bottom: 14px; }
    .db-cart-summary > div { justify-content: space-between; gap: 20px; color: var(--sec); font-size: .88rem; }
    .db-cart-summary .db-cart-total { margin-top: 2px; padding-top: 12px; border-top: 1px solid var(--linha); color: var(--tinta); font: 750 1.25rem/1.2 var(--display); }
    .db-cart-checkout {
        width: 100%;
        min-height: 50px;
        padding: 10px 16px;
        border-radius: 12px;
        background: var(--louca);
        color: #fff;
        font-weight: 700;
    }
    .db-cart-checkout:hover { background: #20328e; }
    .db-cart-checkout:disabled { opacity: .48; box-shadow: none; }
    .db-cart-warning { margin-top: 10px; color: #8b5c12; text-align: center; font-size: .84rem; }
    @media (max-width: 640px) {
        .db-cart-modal { align-items: flex-end; padding: 0; }
        .db-cart-panel { width: 100%; max-width: none; max-height: 94vh; max-height: 94dvh; border-width: 1px 0 0; border-radius: 22px 22px 0 0; }
        .db-cart-header { padding: 16px 18px 13px; }
        .db-cart-body { padding: 14px 18px; }
        .db-cart-footer { padding: 13px 18px max(14px, env(safe-area-inset-bottom)); }
        .db-cart-heading h3 { font-size: 1.35rem; }
        .db-cart-heading p { display: none; }
    }
</style>
@endpush

@push('scripts')
{{-- Native details handles the account menu without an external script. --}}
<script>
// Estado global
let cartCount = 0;
let cartTotal = 0;

// Dados da pizza sendo configurada
let pizzaAtual = {
    produtoId: null,
    produtoNome: null,
    categoria: null,
    tamanhos: [],
    tamanhoSelecionado: null,
    saboresSelecionados: [],
    quantidade: 1
};

// Dados do produto simples
let simplesAtual = {
    produtoId: null,
    produtoNome: null,
    preco: 0,
    quantidade: 1
};

// Sabores disponíveis
const saboresDisponiveis = @json($sabores);

// Carregar info do carrinho ao iniciar
document.addEventListener('DOMContentLoaded', function() {
    atualizarCarrinhoInfo();

    const pagina = new URL(window.location.href);
    if (pagina.searchParams.get('checkout') === '1') {
        pagina.searchParams.delete('checkout');
        window.history.replaceState({}, '', pagina.pathname + pagina.search + pagina.hash);

        carregarCarrinho().then(() => {
            if (carrinhoData?.itens?.length) {
                abrirCheckout();
            } else {
                showToast('Sua sacola está vazia', 'warning');
            }
        });
    }
});

// Função para atualizar info do carrinho via AJAX
function atualizarCarrinhoInfo() {
    fetch('{{ route("cliente.carrinho.info") }}')
        .then(response => response.json())
        .then(data => {
            cartCount = data.cart_count;
            cartTotal = data.cart_total;
            atualizarUICarrinho();
        })
        .catch(error => console.error('Erro ao carregar carrinho:', error));
}

// Atualizar UI do carrinho
function atualizarUICarrinho() {
    // Nav cart count
    const navCount = document.getElementById('nav-cart-count');
    if (cartCount > 0) {
        navCount.textContent = cartCount;
        navCount.classList.remove('hidden');
    } else {
        navCount.classList.add('hidden');
    }

    // Mobile cart button
    const mobileBtn = document.getElementById('mobile-cart-btn');
    const mobileCount = document.getElementById('mobile-cart-count');
    const mobileTotal = document.getElementById('mobile-cart-total');
    if (cartCount > 0) {
        mobileBtn.classList.remove('hidden');
        mobileCount.textContent = cartCount;
        mobileTotal.textContent = formatarPreco(cartTotal);
    } else {
        mobileBtn.classList.add('hidden');
    }

    const sidebarContent = document.getElementById('sidebar-cart-content');
    sidebarContent.innerHTML = cartCount > 0
        ? `<p>${cartCount} ${cartCount == 1 ? 'item' : 'itens'}</p><strong>${formatarPreco(cartTotal)}</strong><button class="db-primary" onclick="abrirModalCarrinho()">Ver sacola</button>`
        : '<p>A sacola está vazia</p><small>Escolha um prato para começar seu pedido.</small>';
}

// ========== MODAL CARRINHO ==========
let carrinhoData = null;
let checkoutData = null;
let tipoPedidoSelecionado = null;
let enderecoSelecionado = null;
let pedidoAtual = null;
const carrinhoItemUrl = @json(route('cliente.carrinho.atualizar', ['loja' => $lojaAtual->slug, 'item' => '__ITEM__']));

function urlDoItemNoCarrinho(itemId) {
    return carrinhoItemUrl.replace('__ITEM__', encodeURIComponent(itemId));
}

function abrirModalCarrinho() {
    document.getElementById('modalCarrinho').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    carregarCarrinho();
}

function fecharModalCarrinho() {
    document.getElementById('modalCarrinho').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function carregarCarrinho() {
    document.getElementById('carrinho-loading').classList.remove('hidden');
    document.getElementById('carrinho-vazio').classList.add('hidden');
    document.getElementById('carrinho-itens').classList.add('hidden');
    document.getElementById('carrinho-resumo').classList.add('hidden');

    return fetch('{{ route("cliente.carrinho.data") }}')
        .then(response => response.json())
        .then(data => {
            carrinhoData = data;
            cartCount = data.cart_count;
            cartTotal = data.subtotal;
            atualizarUICarrinho();
            renderizarCarrinho();
        })
        .catch(error => {
            console.error('Erro ao carregar carrinho:', error);
            showToast('Erro ao carregar carrinho', 'error');
        });
}

function renderizarCarrinho() {
    document.getElementById('carrinho-loading').classList.add('hidden');

    if (!carrinhoData || carrinhoData.itens.length === 0) {
        document.getElementById('carrinho-vazio').classList.remove('hidden');
        document.getElementById('carrinho-resumo').classList.add('hidden');
        return;
    }

    const itensContainer = document.getElementById('carrinho-itens');
    itensContainer.innerHTML = carrinhoData.itens.map(item => `
        <article class="db-cart-item">
            <div class="db-cart-item-top">
                <div class="db-cart-item-thumb">
                    ${item.produto_imagem ?
                        `<img src="${item.produto_imagem}" alt="${item.produto_nome}">` :
                        `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>`
                    }
                </div>
                <div class="db-cart-item-copy">
                    <h4 class="db-cart-item-title">${item.produto_nome}</h4>
                    ${item.tamanho_nome ? `<p class="db-cart-item-detail">Tamanho ${item.tamanho_nome}</p>` : ''}
                    ${item.sabores ? `<p class="db-cart-item-detail">${item.sabores}</p>` : ''}
                    ${item.observacoes ? `<p class="db-cart-item-note">${item.observacoes}</p>` : ''}
                </div>
                <button type="button" onclick="removerItem(${item.id})" class="touch-feedback db-cart-remove" aria-label="Remover ${item.produto_nome} da sacola">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="db-cart-item-bottom">
                <div class="db-cart-stepper" role="group" aria-label="Quantidade de ${item.produto_nome}">
                    <button type="button" onclick="alterarQuantidadeItem(${item.id}, -1)" aria-label="Diminuir quantidade">−</button>
                    <output aria-live="polite">${item.quantidade}</output>
                    <button type="button" onclick="alterarQuantidadeItem(${item.id}, 1)" aria-label="Aumentar quantidade">+</button>
                </div>
                <strong class="db-cart-price">${formatarPreco(item.subtotal)}</strong>
            </div>
        </article>
    `).join('');

    itensContainer.classList.remove('hidden');

    // Atualizar resumo
    document.getElementById('carrinho-subtotal').textContent = formatarPreco(carrinhoData.subtotal);
    document.getElementById('carrinho-taxa').textContent = formatarPreco(carrinhoData.taxa_entrega);
    document.getElementById('carrinho-total').textContent = formatarPreco(carrinhoData.total);
    document.getElementById('carrinho-resumo').classList.remove('hidden');

    // Verificar pedido mínimo
    const avisoMinimo = document.getElementById('pedido-minimo-aviso');
    if (carrinhoData.subtotal < carrinhoData.config.pedido_minimo) {
        avisoMinimo.textContent = `Pedido mínimo: ${formatarPreco(carrinhoData.config.pedido_minimo)}`;
        avisoMinimo.classList.remove('hidden');
        document.getElementById('btn-checkout').disabled = true;
    } else {
        avisoMinimo.classList.add('hidden');
        document.getElementById('btn-checkout').disabled = false;
    }
}

function alterarQuantidadeItem(itemId, delta) {
    const item = carrinhoData.itens.find(i => i.id === itemId);
    if (!item) return;

    const novaQtd = item.quantidade + delta;
    if (novaQtd < 1 || novaQtd > 10) return;

    fetch(urlDoItemNoCarrinho(itemId), {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ quantidade: novaQtd })
    })
    .then(response => response.json())
    .then(data => {
        carrinhoData = data;
        cartCount = data.cart_count;
        cartTotal = data.subtotal;
        atualizarUICarrinho();
        renderizarCarrinho();
    })
    .catch(error => {
        console.error('Erro:', error);
        showToast('Erro ao atualizar quantidade', 'error');
    });
}

function removerItem(itemId) {
    fetch(urlDoItemNoCarrinho(itemId), {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        carrinhoData = data;
        cartCount = data.cart_count;
        cartTotal = data.subtotal;
        atualizarUICarrinho();
        renderizarCarrinho();
        showToast('Item removido', 'success');
    })
    .catch(error => {
        console.error('Erro:', error);
        showToast('Erro ao remover item', 'error');
    });
}

// ========== MODAL CHECKOUT ==========
function abrirCheckout() {
    @auth('cliente')
    fecharModalCarrinho();
    document.getElementById('modalCheckout').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    carregarDadosCheckout();
    @else
    fecharModalCarrinho();
    document.getElementById('modalLoginNecessario').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    @endauth
}

function fecharModalCheckout() {
    document.getElementById('modalCheckout').classList.add('hidden');
    document.body.style.overflow = 'auto';
    tipoPedidoSelecionado = null;
    enderecoSelecionado = null;
}

function fecharModalLogin() {
    document.getElementById('modalLoginNecessario').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function carregarDadosCheckout() {
    fetch('{{ route("cliente.checkout.data") }}')
        .then(response => response.json())
        .then(data => {
            checkoutData = data;
            const nomeSection = document.getElementById('nome-cliente-section');
            const nomeInput = document.getElementById('checkout-nome-cliente');
            nomeSection.classList.toggle('hidden', !data.cliente?.precisa_nome);
            nomeInput.value = data.cliente?.nome || '';
            renderizarEnderecos();
            atualizarResumoCheckout();
        })
        .catch(error => {
            console.error('Erro:', error);
            showToast('Erro ao carregar dados', 'error');
        });
}

function renderizarEnderecos() {
    if (!checkoutData || !checkoutData.enderecos) return;

    const container = document.getElementById('enderecos-lista');
    container.innerHTML = checkoutData.enderecos.map(endereco => `
        <button type="button" onclick="selecionarEndereco(${endereco.id})"
                class="w-full p-3 text-left border-2 ${enderecoSelecionado === endereco.id ? 'border-green-500 bg-green-900/20' : 'border-gray-600'} rounded-xl hover:border-green-400 transition">
            <div class="flex items-center gap-3">
                <div class="w-4 h-4 rounded-full border-2 ${enderecoSelecionado === endereco.id ? 'border-green-500 bg-green-500' : 'border-gray-500'}"></div>
                <span class="text-gray-300 text-sm">${endereco.label}</span>
            </div>
        </button>
    `).join('');

    // Se não tem endereço selecionado, selecionar o padrão
    if (!enderecoSelecionado && checkoutData.enderecos.length > 0) {
        const padrao = checkoutData.enderecos.find(e => e.padrao);
        if (padrao) {
            selecionarEndereco(padrao.id);
        }
    }
}

function selecionarTipoPedido(tipo) {
    tipoPedidoSelecionado = tipo;

    document.getElementById('tipo-delivery').classList.toggle('border-green-500', tipo === 'delivery');
    document.getElementById('tipo-delivery').classList.toggle('bg-green-900/20', tipo === 'delivery');
    document.getElementById('tipo-retirada').classList.toggle('border-green-500', tipo === 'retirada');
    document.getElementById('tipo-retirada').classList.toggle('bg-green-900/20', tipo === 'retirada');

    if (tipo === 'delivery') {
        document.getElementById('endereco-section').classList.remove('hidden');
        document.getElementById('checkout-taxa-row').classList.remove('hidden');
    } else {
        document.getElementById('endereco-section').classList.add('hidden');
        document.getElementById('checkout-taxa-row').classList.add('hidden');
    }

    atualizarResumoCheckout();
}

function selecionarEndereco(id) {
    enderecoSelecionado = id;
    renderizarEnderecos();
}

function atualizarResumoCheckout() {
    if (!carrinhoData) return;

    document.getElementById('checkout-subtotal').textContent = formatarPreco(carrinhoData.subtotal);

    let taxa = tipoPedidoSelecionado === 'delivery' ? carrinhoData.taxa_entrega : 0;
    let total = carrinhoData.subtotal + taxa;

    document.getElementById('checkout-taxa').textContent = formatarPreco(taxa);
    document.getElementById('checkout-total').textContent = formatarPreco(total);
}

function abrirNovoEndereco() {
    // Redireciona para página de endereços
    window.location.href = '{{ route("cliente.enderecos.create") }}';
}

function finalizarPedido() {
    const precisaNome = checkoutData?.cliente?.precisa_nome === true;
    const nomeCliente = document.getElementById('checkout-nome-cliente').value.trim();
    if (precisaNome && nomeCliente.length < 2) {
        document.getElementById('checkout-nome-cliente').focus();
        showToast('Informe seu nome para finalizar o pedido', 'warning');
        return;
    }

    if (!tipoPedidoSelecionado) {
        showToast('Selecione o tipo de pedido', 'warning');
        return;
    }

    if (tipoPedidoSelecionado === 'delivery' && !enderecoSelecionado) {
        showToast('Selecione um endereço de entrega', 'warning');
        return;
    }

    const btn = document.getElementById('btn-finalizar');
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin h-5 w-5 inline-block mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processando...';

    const formData = new FormData();
    formData.append('tipo_pedido', tipoPedidoSelecionado);
    if (precisaNome) {
        formData.append('nome_cliente', nomeCliente);
    }
    if (enderecoSelecionado) {
        formData.append('cliente_endereco_id', enderecoSelecionado);
    }
    formData.append('observacoes', document.getElementById('checkout-observacoes').value);

    fetch('{{ route("cliente.pedido.finalizar") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(async response => {
        const data = await response.json();
        if (!response.ok) {
            throw new Error(data.message || 'Erro ao finalizar pedido');
        }
        return data;
    })
    .then(data => {
        if (data.success) {
            pedidoAtual = data.pedido_id;
            cartCount = 0;
            cartTotal = 0;
            carrinhoData = null;
            atualizarUICarrinho();

            window.location.assign(data.redirect_url);
        } else {
            showToast(data.message || 'Erro ao finalizar pedido', 'error');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showToast(error.message || 'Erro ao finalizar pedido. Tente novamente.', 'error');
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = 'Confirmar Pedido';
    });
}

// ========== MODAL ACOMPANHAMENTO ==========
let acompanhamentoInterval = null;

function abrirModalAcompanhamento(pedidoId) {
    pedidoAtual = pedidoId;
    document.getElementById('modalAcompanhamento').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    carregarStatusPedido();

    // Atualizar status a cada 10 segundos
    acompanhamentoInterval = setInterval(carregarStatusPedido, 10000);
}

function fecharModalAcompanhamento() {
    document.getElementById('modalAcompanhamento').classList.add('hidden');
    document.body.style.overflow = 'auto';
    if (acompanhamentoInterval) {
        clearInterval(acompanhamentoInterval);
        acompanhamentoInterval = null;
    }
}

function carregarStatusPedido() {
    if (!pedidoAtual) return;

    const statusUrl = @json(route('cliente.pedido.status', ['loja' => $lojaAtual->slug, 'pedido' => '__PEDIDO_ID__']))
        .replace('__PEDIDO_ID__', encodeURIComponent(pedidoAtual));

    fetch(statusUrl, {
        headers: {
            'Accept': 'application/json'
        }
    })
        .then(async response => {
            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.message || data.error || 'Não foi possível carregar o pedido');
            }
            return data;
        })
        .then(data => {
            if (data.success) {
                renderizarAcompanhamento(data.pedido);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
        });
}

function renderizarAcompanhamento(pedido) {
    document.getElementById('acomp-numero').textContent = pedido.numero_pedido;
    document.getElementById('acomp-status-label').textContent = pedido.status_label;

    if (pedido.previsao_entrega) {
        document.getElementById('acomp-previsao').textContent = `Previsão: ${pedido.previsao_entrega}`;
    }

    // Progress bar (1-5 steps)
    const progress = (pedido.status_progress / 5) * 100;
    document.getElementById('acomp-progress-bar').style.width = `${progress}%`;

    // Itens
    const itensContainer = document.getElementById('acomp-itens');
    itensContainer.innerHTML = pedido.itens.map(item => `
        <div class="flex justify-between text-gray-300">
            <span>${item.quantidade}x ${item.nome}${item.tamanho ? ` (${item.tamanho})` : ''}${item.sabores ? ` - ${item.sabores}` : ''}</span>
            <span>${formatarPreco(item.subtotal)}</span>
        </div>
    `).join('');

    // Endereço
    if (pedido.endereco && pedido.tipo_pedido === 'delivery') {
        document.getElementById('acomp-endereco-section').classList.remove('hidden');
        document.getElementById('acomp-endereco').textContent =
            `${pedido.endereco.logradouro}, ${pedido.endereco.numero}${pedido.endereco.complemento ? ' - ' + pedido.endereco.complemento : ''} - ${pedido.endereco.bairro}`;
    } else {
        document.getElementById('acomp-endereco-section').classList.add('hidden');
    }

    // Total
    document.getElementById('acomp-total').textContent = formatarPreco(pedido.total);

    // Se pedido finalizado, parar de atualizar
    if (['entregue', 'fechado', 'cancelado'].includes(pedido.status)) {
        if (acompanhamentoInterval) {
            clearInterval(acompanhamentoInterval);
            acompanhamentoInterval = null;
        }
    }
}

// Toast notification
function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');

    // Vibrate on mobile for feedback (if supported)
    if (navigator.vibrate) {
        navigator.vibrate(type === 'success' ? 50 : type === 'error' ? [50, 50, 50] : 30);
    }

    const icons = {
        success: `<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>`,
        error: `<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>`,
        warning: `<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>`
    };

    const colors = {
        success: 'bg-green-600 border-green-500',
        error: 'bg-red-600 border-red-500',
        warning: 'bg-yellow-600 border-yellow-500'
    };

    const toast = document.createElement('div');
    toast.className = `toast-enter flex items-center gap-3 px-4 py-3 rounded-xl shadow-2xl border ${colors[type] || colors.success} text-white font-medium pointer-events-auto`;
    toast.innerHTML = `${icons[type] || icons.success}<span class="flex-1">${message}</span>`;
    container.appendChild(toast);

    setTimeout(() => {
        toast.classList.remove('toast-enter');
        toast.classList.add('toast-exit');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Formatar preço
function formatarPreco(valor) {
    return 'R$ ' + parseFloat(valor).toFixed(2).replace('.', ',');
}

// ========== PRODUTO SIMPLES ==========
const fichaItem = document.getElementById('modalSimples');
let fichaOrigem;
function adicionarProdutoSimples(id, nome, preco, origem) {
    simplesAtual = { produtoId: id, produtoNome: nome, preco: preco, quantidade: 1 };
    fichaOrigem = origem;
    document.getElementById('ficha-miniatura').replaceChildren(origem.querySelector('.db-thumb').cloneNode(true));
    const descricao = document.getElementById('ficha-descricao');
    descricao.textContent = origem.querySelector('.db-dish-copy > span')?.textContent.trim() || '';
    descricao.hidden = !descricao.textContent;
    document.getElementById('modalSimplesNome').textContent = nome;
    document.getElementById('modalSimplesPreco').textContent = formatarPreco(preco);
    document.getElementById('simples-observacoes').value = '';
    document.getElementById('ficha-erro').hidden = true;
    atualizarFicha();
    fichaItem.showModal();
    document.body.style.overflow = 'hidden';
}
function fecharModalSimples() { fichaItem.close(); }
fichaItem.addEventListener('close', () => {
    document.body.style.overflow = '';
    fichaOrigem?.focus({preventScroll:true});
});
fichaItem.addEventListener('click', event => {
    if(event.target !== fichaItem) return;
    const r = fichaItem.getBoundingClientRect();
    if(event.clientX < r.left || event.clientX > r.right || event.clientY < r.top || event.clientY > r.bottom) fecharModalSimples();
});
function atualizarFicha() {
    const { quantidade, preco, enviando } = simplesAtual;
    document.getElementById('simples-quantidade').textContent = quantidade;
    document.getElementById('simples-preco-total').textContent = formatarPreco(preco * quantidade);
    document.getElementById('ficha-menos').disabled = quantidade <= 1 || !!enviando;
    document.getElementById('ficha-mais').disabled = quantidade >= 10 || !!enviando;
    document.getElementById('btn-adicionar-simples').disabled = !!enviando;
    document.getElementById('ficha-acao').textContent = enviando ? 'Adicionando…' : 'Adicionar à sacola';
}
function alterarQuantidadeSimples(delta) {
    simplesAtual.quantidade = Math.max(1, Math.min(10, simplesAtual.quantidade + delta));
    atualizarFicha();
}
async function confirmarProdutoSimples() {
    const item = simplesAtual;
    if (item.enviando) return;
    item.enviando = true;
    atualizarFicha();
    const erro = document.getElementById('ficha-erro');
    erro.hidden = true;
    const formData = new FormData();
    formData.append('produto_id', item.produtoId);
    formData.append('quantidade', item.quantidade);
    formData.append('observacoes', document.getElementById('simples-observacoes').value);
    try {
        const response = await fetch('{{ route("cliente.carrinho.adicionar.publico") }}', {
            method: 'POST', headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept':'application/json'}, body: formData
        });
        const data = await response.json();
        if (!response.ok || !data.success) throw new Error(data.message || 'Não foi possível adicionar o item.');
        cartCount = data.cart_count;
        cartTotal = data.cart_total;
        atualizarUICarrinho();
        if (fichaItem.open && simplesAtual === item) fecharModalSimples();
        showToast('Item adicionado à sacola.', 'success');
    } catch (e) {
        if (fichaItem.open && simplesAtual === item) {
            erro.textContent = e.message || 'Confira sua conexão e tente novamente.';
            erro.hidden = false;
        } else showToast('Não foi possível adicionar o item.', 'error');
    } finally {
        item.enviando = false;
        if (simplesAtual === item) atualizarFicha();
    }
}

// ========== PIZZA ==========
function abrirModalPizza(id, nome, tamanhos, categoria) {
    pizzaAtual = {
        produtoId: id,
        produtoNome: nome,
        categoria: categoria,
        tamanhos: tamanhos,
        tamanhoSelecionado: null,
        saboresSelecionados: [],
        quantidade: 1
    };

    document.getElementById('modalPizzaNome').textContent = nome;
    document.getElementById('modal-quantidade').textContent = 1;
    document.getElementById('modal-preco-total').textContent = formatarPreco(0);
    document.getElementById('pizza-observacoes').value = '';
    document.getElementById('modalPizza').classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    renderizarTamanhos();
    document.getElementById('sabores-section').classList.add('hidden');
}

function fecharModalPizza() {
    document.getElementById('modalPizza').classList.add('hidden');
    document.body.style.overflow = 'auto';
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
    atualizarPrecoModal();
}

function renderizarSabores() {
    const container = document.getElementById('sabores-container');
    const tamanhoNome = pizzaAtual.tamanhoSelecionado.nome.toLowerCase();
    const campoPreco = `preco_${tamanhoNome}`;
    const categoriaProduto = pizzaAtual.categoria;

    const isPizzaDoce = categoriaProduto && categoriaProduto.toLowerCase().includes('doce');

    let html = '';
    for (const [categoriaSabor, sabores] of Object.entries(saboresDisponiveis)) {
        if (isPizzaDoce) {
            if (!categoriaSabor.toLowerCase().includes('doce')) {
                continue;
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
    atualizarPrecoModal();
}

function alterarQuantidadeModal(delta) {
    pizzaAtual.quantidade = Math.max(1, Math.min(10, pizzaAtual.quantidade + delta));
    document.getElementById('modal-quantidade').textContent = pizzaAtual.quantidade;
    atualizarPrecoModal();
}

function atualizarPrecoModal() {
    if (!pizzaAtual.tamanhoSelecionado) {
        document.getElementById('modal-preco-total').textContent = formatarPreco(0);
        return;
    }

    const tamanhoNome = pizzaAtual.tamanhoSelecionado.nome.toLowerCase();
    const campoPreco = `preco_${tamanhoNome}`;
    let maiorPreco = parseFloat(pizzaAtual.tamanhoSelecionado.preco);

    pizzaAtual.saboresSelecionados.forEach(id => {
        for (const sabores of Object.values(saboresDisponiveis)) {
            const sabor = sabores.find(s => s.id === id);
            if (sabor) {
                const precoSabor = sabor[campoPreco] ? parseFloat(sabor[campoPreco]) : 0;
                if (precoSabor > maiorPreco) {
                    maiorPreco = precoSabor;
                }
                break;
            }
        }
    });

    document.getElementById('modal-preco-total').textContent = formatarPreco(maiorPreco * pizzaAtual.quantidade);
}

function adicionarPizzaAoCarrinho() {
    if (!pizzaAtual.tamanhoSelecionado) {
        showToast('Selecione um tamanho', 'warning');
        return;
    }

    if (pizzaAtual.saboresSelecionados.length === 0) {
        showToast('Selecione pelo menos um sabor', 'warning');
        return;
    }

    const btn = document.getElementById('btn-adicionar-pizza');
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

    const formData = new FormData();
    formData.append('produto_id', pizzaAtual.produtoId);
    formData.append('produto_tamanho_id', pizzaAtual.tamanhoSelecionado.id);
    formData.append('quantidade', pizzaAtual.quantidade);
    formData.append('observacoes', document.getElementById('pizza-observacoes').value);
    pizzaAtual.saboresSelecionados.forEach((id, index) => {
        formData.append(`sabores[${index}]`, id);
    });

    fetch('{{ route("cliente.carrinho.adicionar.publico") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            cartCount = data.cart_count;
            cartTotal = data.cart_total;
            atualizarUICarrinho();
            showToast(`✓ ${pizzaAtual.produtoNome} adicionada!`, 'success');
            fecharModalPizza();
        } else {
            showToast(data.message || 'Erro ao adicionar', 'error');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showToast('Erro ao adicionar ao carrinho', 'error');
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = 'Adicionar';
    });
}

// ========== SEARCH & NAVIGATION ==========
document.getElementById('search-input')?.addEventListener('input', function(e) {
    const normalizar = texto => texto.normalize('NFD').replace(/[\u0300-\u036f]/g, '').trim().toLowerCase();
    const searchTerm = normalizar(e.target.value);
    const productCards = document.querySelectorAll('.product-card');

    productCards.forEach(card => {
        const produtoNome = card.getAttribute('data-produto');
        if (normalizar(produtoNome).includes(searchTerm)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });

    document.querySelectorAll('.category-section').forEach(section => {
        const cards = section.querySelectorAll('.product-card');
        const visibleCards = Array.from(cards).filter(card => card.style.display !== 'none');
        section.style.display = visibleCards.length === 0 && searchTerm !== '' ? 'none' : '';
    });
    document.getElementById('db-no-results').hidden = !searchTerm || Array.from(productCards).some(card => card.style.display !== 'none');
});

// Scroll to category function (used by mobile buttons)
function scrollToCategoria(categoriaId) {
    const targetSection = document.getElementById('categoria-' + categoriaId);
    if (targetSection) {
        // Add small vibration feedback on mobile
        if (navigator.vibrate) navigator.vibrate(10);
        targetSection.scrollIntoView({ behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth', block: 'start' });
    }
}

// Category navigation (desktop sidebar)
document.querySelectorAll('.category-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const categoriaId = this.getAttribute('data-categoria');
        scrollToCategoria(categoriaId);
    });
});

// Highlight active category on scroll
const observerOptions = {
    root: null,
    rootMargin: '-150px 0px -70% 0px',
    threshold: 0
};

let isUserScrolling = false;
let scrollTimeout = null;

// Detect when user is actively scrolling
window.addEventListener('scroll', () => {
    isUserScrolling = true;
    clearTimeout(scrollTimeout);
    scrollTimeout = setTimeout(() => {
        isUserScrolling = false;
    }, 150);
}, { passive: true });

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const id = entry.target.getAttribute('id');
            const categoriaId = id.replace('categoria-', '');

            document.querySelectorAll('.category-link').forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('data-categoria') === categoriaId) {
                    link.classList.add('active');
                }
            });

            document.querySelectorAll('.category-link-mobile').forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('data-categoria') === categoriaId) {
                    link.classList.add('active');
                    // Only scroll the category link horizontally if user is not actively scrolling
                    if (!isUserScrolling) {
                        const container = link.parentElement;
                        if (container) {
                            const linkLeft = link.offsetLeft;
                            const linkWidth = link.offsetWidth;
                            const containerWidth = container.offsetWidth;
                            const scrollLeft = linkLeft - (containerWidth / 2) + (linkWidth / 2);
                            container.scrollTo({ left: scrollLeft, behavior: 'smooth' });
                        }
                    }
                }
            });
        }
    });
}, observerOptions);

document.querySelectorAll('[id^="categoria-"]').forEach(section => {
    observer.observe(section);
});

// Close modal on ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        if (!document.getElementById('modalPizza').classList.contains('hidden')) {
            fecharModalPizza();
        }
        // O diálogo nativo do produto simples trata Escape e contenção de foco.
        if (!document.getElementById('modalCarrinho').classList.contains('hidden')) {
            fecharModalCarrinho();
        }
        if (!document.getElementById('modalCheckout').classList.contains('hidden')) {
            fecharModalCheckout();
        }
        if (!document.getElementById('modalAcompanhamento').classList.contains('hidden')) {
            fecharModalAcompanhamento();
        }
        if (!document.getElementById('modalLoginNecessario').classList.contains('hidden')) {
            fecharModalLogin();
        }
    }
});
</script>
@endpush
@endsection
