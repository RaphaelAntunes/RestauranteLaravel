@extends('layouts.cliente')

@section('title', 'Cardápio')

@section('content')
<div class="min-h-screen bg-gray-900">
    <!-- Top Navigation Bar -->
    <nav class="bg-gray-800 border-b border-gray-700 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-14 sm:h-16">
                <!-- Logo & Nav Links -->
                <div class="flex items-center gap-3 sm:gap-6">
                    <a href="{{ route('cliente.cardapio') }}" class="flex items-center gap-2 text-white hover:text-red-400 transition">
                        <div class="bg-gradient-to-r from-red-500 to-red-600 p-2 rounded-xl">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        </div>
                        <span class="font-semibold hidden sm:inline">Cardápio</span>
                    </a>

                    @auth('cliente')
                    <a href="{{ route('cliente.pedidos') }}" class="flex items-center gap-2 text-gray-400 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <span class="hidden sm:inline">Meus Pedidos</span>
                    </a>
                    @endauth
                </div>

                <!-- Right Side -->
                <div class="flex items-center gap-3 sm:gap-4">
                    <!-- Cart -->
                    <button onclick="abrirModalCarrinho()" class="relative p-2 text-gray-400 hover:text-white transition" id="nav-cart-btn">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        <span id="nav-cart-count" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
                    </button>

                    @auth('cliente')
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 text-gray-400 hover:text-white transition">
                            <div class="w-8 h-8 bg-gray-600 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <span class="hidden sm:inline text-sm text-white">{{ auth('cliente')->user()->nome }}</span>
                            <svg class="w-4 h-4 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak
                             class="absolute right-0 mt-2 w-48 bg-gray-800 rounded-xl shadow-2xl border border-gray-700 py-2 z-50">
                            <a href="{{ route('cliente.pedidos') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition">
                                Meus Pedidos
                            </a>
                            <form action="{{ route('cliente.logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition">
                                    Sair
                                </button>
                            </form>
                        </div>
                    </div>
                    @else
                    <a href="{{ route('cliente.login') }}" class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition shadow-lg">
                        Entrar
                    </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Restaurant Header -->
    <header class="bg-gray-800 border-b border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                <div class="bg-gradient-to-r from-red-500 to-red-600 p-3 rounded-xl shadow-xl flex-shrink-0">
                    <svg class="w-8 h-8 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-white">
                        {{ \App\Models\Configuracao::obter('nome_restaurante', 'Restaurante') }}
                    </h1>
                    <div class="flex flex-wrap items-center gap-3 sm:gap-4 text-xs sm:text-sm text-gray-400 mt-1">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Abre às {{ $config->horario_inicio ? \Carbon\Carbon::parse($config->horario_inicio)->format('H:i') : '10:00' }}</span>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>{{ \App\Models\Configuracao::obter('cidade', 'Sua Cidade') }} - {{ \App\Models\Configuracao::obter('estado', 'UF') }}</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Categories - Improved touch targets -->
    <div class="lg:hidden bg-gray-800/95 backdrop-blur-sm border-b border-gray-700 sticky top-14 sm:top-16 z-40">
        <div class="max-w-7xl mx-auto px-3">
            <div class="flex gap-2 py-2.5 overflow-x-auto scrollbar-hide" id="categorias-mobile-container">
                @foreach($categorias as $categoria)
                    @if($categoria->produtosAtivos->count() > 0)
                    <button type="button"
                       onclick="scrollToCategoria('{{ $categoria->id }}')"
                       class="touch-feedback flex-shrink-0 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all category-link-mobile
                              bg-gray-700/80 text-gray-300 border border-gray-600 min-touch"
                       data-categoria="{{ $categoria->id }}">
                        {{ $categoria->nome }}
                    </button>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
        <div class="flex flex-col lg:flex-row gap-4 sm:gap-6">
            <!-- Sidebar - Categories (Desktop) -->
            <aside class="hidden lg:block w-64 flex-shrink-0">
                <div class="bg-gray-800 rounded-xl border border-gray-700 p-4 sticky top-24">
                    <h3 class="text-lg font-bold text-white mb-4">Categorias</h3>
                    <nav class="space-y-1">
                        @foreach($categorias as $categoria)
                            @if($categoria->produtosAtivos->count() > 0)
                            <a href="#categoria-{{ $categoria->id }}"
                               class="flex items-center justify-between px-4 py-3 rounded-lg text-gray-300 border border-transparent hover:bg-gray-700/50 hover:border-red-500/30 hover:text-white transition-all category-link group"
                               data-categoria="{{ $categoria->id }}">
                                <span class="font-medium">{{ $categoria->nome }}</span>
                                <span class="text-xs text-gray-500 group-hover:text-gray-400">{{ $categoria->produtosAtivos->count() }}</span>
                            </a>
                            @endif
                        @endforeach
                    </nav>
                </div>
            </aside>

            <!-- Products -->
            <section class="flex-1 min-w-0">
                <!-- Search -->
                <div class="mb-4 sm:mb-6">
                    <div class="relative">
                        <input type="text"
                               id="search-input"
                               placeholder="Busque por um produto"
                               class="w-full pl-12 pr-4 py-3 bg-gray-800 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Products by Category -->
                <div class="space-y-6 sm:space-y-8">
                    @foreach($categorias as $categoria)
                        @if($categoria->produtosAtivos->count() > 0)
                        <div id="categoria-{{ $categoria->id }}" class="category-section scroll-mt-32">
                            <div class="text-red-400 font-semibold mb-3 sticky top-28 sm:top-32 lg:top-4 bg-gray-900 py-2 z-10">
                                {{ $categoria->nome }}
                            </div>

                            <div class="space-y-2">
                                @foreach($categoria->produtosAtivos as $produto)
                                <button type="button"
                                   onclick="@if($produto->tamanhos->count() > 0) abrirModalPizza({{ $produto->id }}, '{{ addslashes($produto->nome) }}', {{ $produto->tamanhos->toJson() }}, '{{ addslashes($categoria->nome) }}') @else adicionarProdutoSimples({{ $produto->id }}, '{{ addslashes($produto->nome) }}', {{ $produto->preco }}) @endif"
                                   class="w-full text-left bg-gray-800 hover:bg-gray-700/80 p-3 sm:p-4 rounded-xl border border-gray-700 hover:border-red-500/50 hover:shadow-lg transition-all duration-200 product-card"
                                   data-produto="{{ strtolower($produto->nome) }}">
                                    <div class="flex gap-3 sm:gap-4">
                                        <!-- Product Image -->
                                        <div class="flex-shrink-0 w-20 h-20 sm:w-24 sm:h-24 rounded-lg overflow-hidden bg-gray-700">
                                            @if($produto->imagem)
                                            <img src="{{ asset('storage/' . $produto->imagem) }}"
                                                 alt="{{ $produto->nome }}"
                                                 class="w-full h-full object-cover">
                                            @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                            @endif
                                        </div>

                                        <!-- Product Info -->
                                        <div class="flex-1 min-w-0 flex flex-col justify-between">
                                            <div>
                                                <h3 class="text-white font-medium text-sm sm:text-base break-words">{{ $produto->nome }}</h3>
                                                @if($produto->descricao)
                                                <p class="text-gray-400 text-xs sm:text-sm line-clamp-2 mt-1">{{ $produto->descricao }}</p>
                                                @endif
                                                @if($produto->tamanhos->count() > 0)
                                                <span class="inline-flex mt-2 px-2 py-0.5 text-xs font-semibold rounded-full bg-orange-900/40 text-orange-300 border border-orange-800">
                                                    {{ $produto->tamanhos->count() }} tamanhos
                                                </span>
                                                @endif
                                            </div>
                                            <div class="flex items-center justify-between mt-2">
                                                <div class="text-green-400 font-bold text-sm sm:text-base">
                                                    @if($produto->tamanhos->count() > 0)
                                                    <span class="text-gray-500 font-normal text-xs">A partir de </span>R$ {{ number_format($produto->tamanhos->min('preco'), 2, ',', '.') }}
                                                    @else
                                                    R$ {{ number_format($produto->preco, 2, ',', '.') }}
                                                    @endif
                                                </div>
                                                <div class="bg-gradient-to-r from-red-500 to-red-600 text-white p-2 rounded-lg">
                                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </button>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </section>

            <!-- Sidebar - Cart (Desktop) -->
            <aside class="hidden lg:block w-80 flex-shrink-0">
                <div class="sticky top-24 space-y-4">
                    <!-- Cart -->
                    <div class="bg-gray-800 border border-gray-700 rounded-xl p-4 sm:p-5" id="sidebar-cart">
                        <div id="sidebar-cart-content">
                            <!-- Preenchido via JS -->
                        </div>

                        <!-- Coupon -->
                        <div class="mt-4 pt-4 border-t border-gray-700">
                            <button class="flex items-center justify-between w-full p-3 rounded-lg hover:bg-gray-700/50 transition group">
                                <div class="flex items-center gap-3">
                                    <div class="bg-gray-700 p-2 rounded-lg">
                                        <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                        </svg>
                                    </div>
                                    <div class="text-left">
                                        <p class="text-sm font-medium text-white">Tem um cupom?</p>
                                        <p class="text-xs text-gray-500">Clique para adicionar</p>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-gray-500 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </main>

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

<!-- Modal de Produto Simples -->
<div id="modalSimples" class="modal hidden fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-2 sm:p-4" onclick="fecharModalSimples()">
    <div class="bg-gray-800 rounded-2xl max-w-md w-full overflow-hidden border border-gray-700 shadow-2xl" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="p-4 sm:p-6 border-b border-gray-700 bg-gradient-to-r from-red-500 to-orange-500">
            <div class="flex justify-between items-center gap-3">
                <h3 class="text-lg sm:text-xl font-bold text-white" id="modalSimplesNome">Produto</h3>
                <button onclick="fecharModalSimples()" class="text-white hover:bg-white/20 rounded-lg p-1.5 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div class="p-4 sm:p-6 space-y-4">
            <div class="text-center">
                <p class="text-gray-400 text-sm mb-2">Preço unitário</p>
                <p class="text-green-400 font-bold text-2xl" id="modalSimplesPreco">R$ 0,00</p>
            </div>

            <!-- Quantidade -->
            <div>
                <h4 class="text-sm font-semibold text-white mb-3">Quantidade</h4>
                <div class="flex items-center justify-center space-x-5">
                    <button type="button" onclick="alterarQuantidadeSimples(-1)" class="touch-feedback w-14 h-14 rounded-xl bg-gray-700 hover:bg-gray-600 flex items-center justify-center text-white font-bold transition shadow-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"></path>
                        </svg>
                    </button>
                    <span id="simples-quantidade" class="text-white font-bold text-3xl w-16 text-center">1</span>
                    <button type="button" onclick="alterarQuantidadeSimples(1)" class="touch-feedback w-14 h-14 rounded-xl bg-gray-700 hover:bg-gray-600 flex items-center justify-center text-white font-bold transition shadow-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Observações -->
            <div>
                <label for="simples-observacoes" class="block text-xs sm:text-sm font-medium text-white mb-2">Observações</label>
                <textarea id="simples-observacoes" rows="2" class="w-full px-3 py-2 text-sm bg-gray-700 border border-gray-600 rounded-lg text-gray-100 focus:ring-2 focus:ring-red-500" placeholder="Ex: Sem gelo, bem passado, etc"></textarea>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-4 border-t border-gray-700 safe-bottom">
            <div class="flex flex-col gap-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Total:</span>
                    <span class="text-white font-bold text-2xl" id="simples-preco-total">R$ 0,00</span>
                </div>
                <button onclick="confirmarProdutoSimples()" id="btn-adicionar-simples" class="touch-feedback w-full py-4 bg-gradient-to-r from-red-500 to-orange-500 text-white rounded-xl font-bold shadow-lg shadow-red-500/30 transition text-base">
                    Adicionar ao Carrinho
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container - Centered on mobile, right on desktop -->
<div id="toast-container" class="fixed top-20 left-4 right-4 sm:left-auto sm:right-4 sm:w-80 z-[100] space-y-2 pointer-events-none"></div>

<!-- Modal do Carrinho -->
<div id="modalCarrinho" class="modal hidden fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-2 sm:p-4" onclick="fecharModalCarrinho()">
    <div class="bg-gray-800 rounded-2xl max-w-lg w-full max-h-[95vh] sm:max-h-[90vh] overflow-hidden border border-gray-700 shadow-2xl flex flex-col" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="p-4 sm:p-6 border-b border-gray-700 bg-gradient-to-r from-red-500 to-orange-500 flex-shrink-0">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    <h3 class="text-lg sm:text-xl font-bold text-white">Meu Carrinho</h3>
                </div>
                <button onclick="fecharModalCarrinho()" class="text-white hover:bg-white/20 rounded-lg p-1.5 sm:p-2 transition">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Body -->
        <div class="flex-1 overflow-y-auto p-4" id="carrinho-body">
            <div class="text-center py-8" id="carrinho-loading">
                <svg class="animate-spin h-8 w-8 mx-auto text-red-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-gray-400 mt-2">Carregando...</p>
            </div>
            <div id="carrinho-vazio" class="hidden text-center py-8">
                <svg class="mx-auto w-16 h-16 text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <p class="text-gray-400 font-medium mb-1">Seu carrinho está vazio</p>
                <p class="text-gray-500 text-sm">Adicione produtos para continuar</p>
            </div>
            <div id="carrinho-itens" class="space-y-3 hidden"></div>
        </div>

        <!-- Footer -->
        <div class="p-4 border-t border-gray-700 bg-gray-800/50 flex-shrink-0 safe-bottom" id="carrinho-footer">
            <div id="carrinho-resumo" class="hidden">
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-gray-400 text-sm">
                        <span>Subtotal</span>
                        <span id="carrinho-subtotal">R$ 0,00</span>
                    </div>
                    <div class="flex justify-between text-gray-400 text-sm">
                        <span>Taxa de entrega</span>
                        <span id="carrinho-taxa">R$ 0,00</span>
                    </div>
                    <div class="flex justify-between text-white font-bold text-xl pt-3 border-t border-gray-700">
                        <span>Total</span>
                        <span id="carrinho-total">R$ 0,00</span>
                    </div>
                </div>
                <button onclick="abrirCheckout()" id="btn-checkout" class="touch-feedback w-full py-4 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl font-bold shadow-lg shadow-green-500/30 transition text-base disabled:opacity-50 disabled:shadow-none">
                    Finalizar Pedido
                </button>
                <p id="pedido-minimo-aviso" class="text-yellow-400 text-sm text-center mt-3 hidden"></p>
            </div>
        </div>
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
            <a href="{{ route('cliente.login') }}" class="block w-full py-3 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-xl font-bold transition">
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
        background-color: rgba(55, 65, 81, 0.8) !important;
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
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
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

    // Sidebar cart
    const sidebarContent = document.getElementById('sidebar-cart-content');
    if (cartCount > 0) {
        sidebarContent.innerHTML = `
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-white">Carrinho</h3>
                <span class="bg-red-500/20 text-red-400 text-xs font-bold px-2.5 py-1 rounded-full border border-red-500/30">
                    ${cartCount} ${cartCount == 1 ? 'item' : 'itens'}
                </span>
            </div>

            <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-lg p-4 mb-4">
                <div class="flex justify-between items-center">
                    <span class="text-white font-bold">Total:</span>
                    <span class="text-white font-bold text-2xl">${formatarPreco(cartTotal)}</span>
                </div>
            </div>

            <button onclick="abrirModalCarrinho()"
               class="flex items-center justify-center gap-2 w-full bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white py-3 rounded-xl font-bold transition-all shadow-xl hover:shadow-2xl active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                Ver Carrinho
            </button>
        `;
    } else {
        sidebarContent.innerHTML = `
            <div class="text-center py-8">
                <svg class="mx-auto w-16 h-16 text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <p class="text-gray-400 font-medium mb-1">Sacola vazia</p>
                <p class="text-gray-500 text-sm">Adicione produtos ao carrinho</p>
            </div>
        `;
    }
}

// ========== MODAL CARRINHO ==========
let carrinhoData = null;
let checkoutData = null;
let tipoPedidoSelecionado = null;
let enderecoSelecionado = null;
let pedidoAtual = null;

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

    fetch('{{ route("cliente.carrinho.data") }}')
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
        <div class="bg-gray-700/50 rounded-2xl p-4">
            <div class="flex gap-3">
                <div class="w-20 h-20 flex-shrink-0 rounded-xl overflow-hidden bg-gray-600">
                    ${item.produto_imagem ?
                        `<img src="${item.produto_imagem}" alt="${item.produto_nome}" class="w-full h-full object-cover">` :
                        `<div class="w-full h-full flex items-center justify-center"><svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></div>`
                    }
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-start">
                        <h4 class="text-white font-semibold text-base pr-2">${item.produto_nome}</h4>
                        <button onclick="removerItem(${item.id})" class="touch-feedback text-gray-500 hover:text-red-400 transition p-2 -mr-2 -mt-1 min-touch">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    ${item.tamanho_nome ? `<p class="text-gray-400 text-sm">Tamanho ${item.tamanho_nome}</p>` : ''}
                    ${item.sabores ? `<p class="text-gray-400 text-sm truncate">${item.sabores}</p>` : ''}
                    ${item.observacoes ? `<p class="text-yellow-400/80 text-xs italic mt-1">${item.observacoes}</p>` : ''}
                </div>
            </div>
            <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-600">
                <div class="flex items-center gap-3">
                    <button onclick="alterarQuantidadeItem(${item.id}, -1)" class="touch-feedback w-10 h-10 rounded-xl bg-gray-600 hover:bg-gray-500 flex items-center justify-center text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"></path></svg>
                    </button>
                    <span class="text-white font-bold text-lg w-8 text-center">${item.quantidade}</span>
                    <button onclick="alterarQuantidadeItem(${item.id}, 1)" class="touch-feedback w-10 h-10 rounded-xl bg-gray-600 hover:bg-gray-500 flex items-center justify-center text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    </button>
                </div>
                <span class="text-green-400 font-bold text-lg">${formatarPreco(item.subtotal)}</span>
            </div>
        </div>
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

    fetch(`/cliente/carrinho/${itemId}`, {
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
    fetch(`/cliente/carrinho/${itemId}`, {
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
            fecharModalCheckout();
            abrirModalAcompanhamento(data.pedido_id);
            showToast('Pedido realizado com sucesso!', 'success');

            // Limpar carrinho local
            cartCount = 0;
            cartTotal = 0;
            carrinhoData = null;
            atualizarUICarrinho();
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

    fetch(`/cliente/pedido/${pedidoAtual}/status`)
        .then(response => response.json())
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
function adicionarProdutoSimples(id, nome, preco) {
    simplesAtual = {
        produtoId: id,
        produtoNome: nome,
        preco: preco,
        quantidade: 1
    };

    document.getElementById('modalSimplesNome').textContent = nome;
    document.getElementById('modalSimplesPreco').textContent = formatarPreco(preco);
    document.getElementById('simples-quantidade').textContent = 1;
    document.getElementById('simples-preco-total').textContent = formatarPreco(preco);
    document.getElementById('simples-observacoes').value = '';
    document.getElementById('modalSimples').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function fecharModalSimples() {
    document.getElementById('modalSimples').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function alterarQuantidadeSimples(delta) {
    simplesAtual.quantidade = Math.max(1, Math.min(10, simplesAtual.quantidade + delta));
    document.getElementById('simples-quantidade').textContent = simplesAtual.quantidade;
    document.getElementById('simples-preco-total').textContent = formatarPreco(simplesAtual.preco * simplesAtual.quantidade);
}

function confirmarProdutoSimples() {
    const btn = document.getElementById('btn-adicionar-simples');
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

    const formData = new FormData();
    formData.append('produto_id', simplesAtual.produtoId);
    formData.append('quantidade', simplesAtual.quantidade);
    formData.append('observacoes', document.getElementById('simples-observacoes').value);

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
            showToast(`✓ ${simplesAtual.produtoNome} adicionado!`, 'success');
            fecharModalSimples();
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
        btn.textContent = 'Adicionar ao Carrinho';
    });
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
    const searchTerm = e.target.value.toLowerCase();
    const productCards = document.querySelectorAll('.product-card');

    productCards.forEach(card => {
        const produtoNome = card.getAttribute('data-produto');
        if (produtoNome.includes(searchTerm)) {
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
});

// Scroll to category function (used by mobile buttons)
function scrollToCategoria(categoriaId) {
    const targetSection = document.getElementById('categoria-' + categoriaId);
    if (targetSection) {
        // Add small vibration feedback on mobile
        if (navigator.vibrate) navigator.vibrate(10);
        targetSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
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
        if (!document.getElementById('modalSimples').classList.contains('hidden')) {
            fecharModalSimples();
        }
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
