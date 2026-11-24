<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Painel') | {{ $lojaAtual->nome ?? 'Restaurante' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
<link rel="icon" href="{{ asset('dobra/mark.svg') }}"></head>
<body class="db-ops min-h-screen"><a class="db-skip" href="#conteudo">Ir para o conteúdo</a>
    @auth
    <div class="flex h-screen overflow-hidden">
        <!-- Mobile Sidebar Overlay -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 lg:hidden hidden" onclick="toggleSidebar()"></div>

        <!-- Sidebar -->
        <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex-shrink-0 overflow-y-auto transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <a href="{{ route('home') }}" aria-label="{{ $lojaAtual->nome ?? 'Restaurante' }}, início do painel"><x-dobra-brand/></a>
                        <!-- Close Button (Mobile Only) -->
                        <button type="button" aria-label="Navegação" onclick="toggleSidebar()" class="lg:hidden p-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="flex-1 px-4 py-6 space-y-1">
                    <a href="{{ route('home') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('home') ? 'bg-gradient-to-r from-red-500 to-orange-500 text-white shadow-lg' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <x-dobra-icon name="plate"/>
                        <span>Visão do turno</span>
                    </a>

                    @if(auth()->user()->isAdmin() || auth()->user()->isCaixa())
                    <a href="{{ route('pdv.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('pdv.*') ? 'bg-gradient-to-r from-red-500 to-orange-500 text-white shadow-lg' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <x-dobra-icon name="cash"/>
                        <span>PDV</span>
                    </a>
                    @endif

                    @if(auth()->user()->isAdmin() || auth()->user()->isGarcom())
                    <a href="{{ route('pedidos.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('pedidos.*') ? 'bg-gradient-to-r from-red-500 to-orange-500 text-white shadow-lg' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <x-dobra-icon name="bill"/>
                        <span>Pedidos</span>
                    </a>

                    <a href="{{ route('mesas.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('mesas.*') ? 'bg-gradient-to-r from-red-500 to-orange-500 text-white shadow-lg' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <x-dobra-icon name="table"/>
                        <span>Mesas</span>
                    </a>

                    <a href="{{ route('garcom.index') }}" target="_blank" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <x-dobra-icon name="phone"/>
                        <span>Modo Garçom</span>
                        <svg class="w-4 h-4 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                    </a>

                    <div class="pt-4 pb-2">
                        <div class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Cadastros</div>
                    </div>

                    <a href="{{ route('produtos.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('produtos.*') ? 'bg-gradient-to-r from-red-500 to-orange-500 text-white shadow-lg' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <x-dobra-icon name="menu"/>
                        <span>Produtos</span>
                    </a>

                    <a href="{{ route('categorias.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('categorias.*') ? 'bg-gradient-to-r from-red-500 to-orange-500 text-white shadow-lg' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <x-dobra-icon name="tag"/>
                        <span>Categorias</span>
                    </a>

                    <a href="{{ route('sabores.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('sabores.*') ? 'bg-gradient-to-r from-red-500 to-orange-500 text-white shadow-lg' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <x-dobra-icon name="leaf"/>
                        <span>Sabores</span>
                    </a>

                    <a href="{{ route('produto-tamanhos.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('produto-tamanhos.*') ? 'bg-gradient-to-r from-red-500 to-orange-500 text-white shadow-lg' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <x-dobra-icon name="size"/>
                        <span>Tamanhos</span>
                    </a>
                    @endif

                    <div class="pt-4 pb-2">
                        <div class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Operações</div>
                    </div>

                    <a href="{{ route('cozinha.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('cozinha.*') ? 'bg-gradient-to-r from-red-500 to-orange-500 text-white shadow-lg' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <x-dobra-icon name="pan"/>
                        <span>Cozinha</span>
                    </a>

                    <a href="{{ route('preparo.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('preparo.*') ? 'bg-gradient-to-r from-red-500 to-orange-500 text-white shadow-lg' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <x-dobra-icon name="clock"/>
                        <span>Preparo</span>
                    </a>

                    @if(auth()->user()->isAdmin())
                    <div class="pt-4 pb-2">
                        <div class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Gerência</div>
                    </div>

                    <a href="{{ route('admin.loja.edit') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.loja.*') ? 'bg-gradient-to-r from-red-500 to-orange-500 text-white shadow-lg' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <x-dobra-icon name="plate"/>
                        <span>Minha loja</span>
                    </a>

                    <a href="{{ route('cliente.cardapio') }}" target="_blank" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <x-dobra-icon name="menu"/>
                        <span>Ver cardápio</span>
                    </a>

                    <a href="{{ route('garcons.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('garcons.*') ? 'bg-gradient-to-r from-red-500 to-orange-500 text-white shadow-lg' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <x-dobra-icon name="person"/>
                        <span>Garçons</span>
                    </a>

                    <a href="{{ route('relatorios.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('relatorios.*') ? 'bg-gradient-to-r from-red-500 to-orange-500 text-white shadow-lg' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <x-dobra-icon name="chart"/>
                        <span>Relatórios</span>
                    </a>
                    @endif
                </nav>

                <!-- User Info & Logout -->
                <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ auth()->user()->nome }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->role->nome }}</div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 shadow-lg flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <span>Sair</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 overflow-x-hidden overflow-y-auto">
            <!-- Topbar -->
            <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-30 shadow-sm">
                <div class="px-4 sm:px-6 py-4 flex items-center gap-4">
                    <!-- Hamburger Button (Mobile Only) -->
                    <button type="button" aria-label="Navegação" onclick="toggleSidebar()" class="lg:hidden p-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">@yield('title', 'Sistema Restaurante')</h1>
                </div>
            </header>

            <!-- Page Content -->
            <main id="conteudo" class="db-workspace">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 text-green-800 dark:text-green-200 p-4 mb-6 rounded-r-lg shadow-md animate-slide-in" role="alert">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <p class="ml-3 font-medium">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 text-red-800 dark:text-red-200 p-4 mb-6 rounded-r-lg shadow-md animate-slide-in" role="alert">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <p class="ml-3 font-medium">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500 text-yellow-800 dark:text-yellow-200 p-4 mb-6 rounded-r-lg shadow-md animate-slide-in" role="alert">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <p class="ml-3 font-medium">{{ session('warning') }}</p>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 text-red-800 dark:text-red-200 p-4 mb-6 rounded-r-lg shadow-md" role="alert">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li class="font-medium">{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
    @endauth

    @guest
    <main class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>
    @endguest

    <!-- Loading Overlay Component -->
    @include('components.loading-overlay')

    @stack('scripts')

    <script>
        // Sidebar toggle functionality
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        // Close sidebar when clicking a link on mobile
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const sidebarLinks = sidebar.querySelectorAll('a');

            sidebarLinks.forEach(link => {
                link.addEventListener('click', function() {
                    // Only close on mobile
                    if (window.innerWidth < 1024) {
                        sidebar.classList.add('-translate-x-full');
                        overlay.classList.add('hidden');
                    }
                });
            });
        });

    </script>

    <style>
        @keyframes slide-in {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        .animate-slide-in {
            animation: slide-in 0.3s ease-out;
        }
    </style>
</body>
</html>
