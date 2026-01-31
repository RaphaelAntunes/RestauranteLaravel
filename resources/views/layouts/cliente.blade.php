<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Pedidos Online')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @if(request()->routeIs('cliente.cardapio*'))
    <style>
        /* Fallback styles for dark theme - ensures dark background even if Tailwind CSS fails to load */
        html, body { background-color: #111827 !important; min-height: 100vh; }
        .bg-gray-900 { background-color: #111827 !important; }
        .bg-gray-800 { background-color: #1f2937 !important; }
        .bg-gray-700 { background-color: #374151 !important; }
        .text-white { color: #ffffff !important; }
        .text-gray-400 { color: #9ca3af !important; }
        .text-gray-300 { color: #d1d5db !important; }
        .text-red-400 { color: #f87171 !important; }
        .text-green-400 { color: #4ade80 !important; }
        .border-gray-700 { border-color: #374151 !important; }
        .border-gray-600 { border-color: #4b5563 !important; }
    </style>
    @endif
    @stack('styles')
</head>
<body class="{{ request()->routeIs('cliente.cardapio*') ? 'bg-gray-900' : 'bg-gray-50' }}">
    <!-- Navigation - Only show on non-cardapio pages -->
    @if(!request()->routeIs('cliente.cardapio*'))
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('cliente.cardapio') }}" class="flex items-center space-x-3 group">
                        <div class="bg-gradient-to-br from-red-500 to-orange-600 p-3 rounded-xl shadow-lg group-hover:shadow-red-500/50 transition-all duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <span class="text-2xl font-bold bg-gradient-to-r from-red-600 to-orange-600 bg-clip-text text-transparent">
                            Restaurante
                        </span>
                    </a>
                </div>

                <div class="flex items-center space-x-6">
                    <a href="{{ route('cliente.cardapio') }}" 
                       class="hidden md:flex items-center space-x-2 text-gray-700 hover:text-red-600 font-medium transition-colors {{ request()->routeIs('cliente.cardapio*') ? 'text-red-600' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <span>Cardápio</span>
                    </a>

                    @auth('cliente')
                        <a href="{{ route('cliente.carrinho.index') }}" 
                           class="relative flex items-center space-x-2 text-gray-700 hover:text-red-600 font-medium transition-colors {{ request()->routeIs('cliente.carrinho*') ? 'text-red-600' : '' }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span class="hidden md:inline">Carrinho</span>
                            @php
                                $cartCount = 0;
                                if (auth('cliente')->check()) {
                                    $cartCount = \App\Models\CarrinhoItem::where('cliente_id', auth('cliente')->id())->sum('quantidade');
                                } else {
                                    $sessionId = session('carrinho_session_id', '');
                                    if ($sessionId) {
                                        $cartCount = \App\Models\CarrinhoItem::where('session_id', $sessionId)->sum('quantidade');
                                    }
                                }
                            @endphp
                            <span id="cart-count" class="absolute -top-2 -right-2 bg-gradient-to-r from-red-500 to-orange-500 text-white text-xs font-bold rounded-full h-6 w-6 flex items-center justify-center shadow-lg">
                                {{ $cartCount }}
                            </span>
                        </a>

                        <a href="{{ route('cliente.pedidos') }}" 
                           class="hidden md:flex items-center space-x-2 text-gray-700 hover:text-red-600 font-medium transition-colors {{ request()->routeIs('cliente.pedidos*') ? 'text-red-600' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span>Meus Pedidos</span>
                        </a>

                        <form action="{{ route('cliente.logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="hidden md:flex items-center space-x-2 text-gray-700 hover:text-red-600 font-medium transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                <span>Sair</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('cliente.login') }}" 
                           class="bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white px-6 py-2.5 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-200">
                            <span class="flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                </svg>
                                <span>Entrar</span>
                            </span>
                        </a>
                    @endauth

                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-btn" class="md:hidden p-2 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden pb-4 border-t border-gray-200">
                <div class="flex flex-col space-y-2 mt-4">
                    <a href="{{ route('cliente.cardapio') }}" 
                       class="flex items-center space-x-2 px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-100 font-medium {{ request()->routeIs('cliente.cardapio*') ? 'bg-red-50 text-red-600' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <span>Cardápio</span>
                    </a>

                    @auth('cliente')
                    <a href="{{ route('cliente.pedidos') }}" 
                       class="flex items-center space-x-2 px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-100 font-medium {{ request()->routeIs('cliente.pedidos*') ? 'bg-red-50 text-red-600' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Meus Pedidos</span>
                    </a>

                    <form action="{{ route('cliente.logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="w-full flex items-center space-x-2 px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-100 font-medium text-left">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <span>Sair</span>
                        </button>
                    </form>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
    @endif

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-50 border-l-4 border-green-500 text-green-800 px-4 py-3 rounded-lg shadow-md animate-slide-in" role="alert">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="font-medium">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded-lg shadow-md animate-slide-in" role="alert">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="font-medium">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-16 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p class="text-gray-400">&copy; {{ date('Y') }} Restaurante. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    @stack('scripts')

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });

        // Slide in animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slide-in {
                from {
                    opacity: 0;
                    transform: translateY(-20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            .animate-slide-in {
                animation: slide-in 0.3s ease-out;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>