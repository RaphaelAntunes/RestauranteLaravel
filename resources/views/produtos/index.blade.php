@extends('layouts.app')

@section('title', 'Produtos')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Produtos do Cardápio</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Gerencie os produtos do seu restaurante</p>
        </div>
        <a href="{{ route('produtos.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white font-semibold rounded-xl shadow-lg shadow-red-500/30 hover:shadow-red-500/50 transition-all duration-200">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Novo Produto
        </a>
    </div>

    <!-- Grid de Produtos -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($produtos as $produto)
        <div class="group bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-200 dark:border-gray-700 hover:border-red-400 dark:hover:border-red-500">
            <!-- Imagem do Produto -->
            @if($produto->imagem)
            <div class="relative h-48 overflow-hidden">
                <img src="{{ asset('storage/' . $produto->imagem) }}" alt="{{ $produto->nome }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                @if($produto->destaque)
                <div class="absolute top-2 right-2">
                    <span class="inline-flex px-3 py-1.5 text-xs font-bold rounded-full bg-yellow-500 text-white shadow-lg shadow-yellow-500/50">
                        ⭐ Destaque
                    </span>
                </div>
                @endif
            </div>
            @else
            <div class="relative h-48 bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-700 dark:to-gray-800 flex items-center justify-center">
                <svg class="h-16 w-16 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                @if($produto->destaque)
                <div class="absolute top-2 right-2">
                    <span class="inline-flex px-3 py-1.5 text-xs font-bold rounded-full bg-yellow-500 text-white shadow-lg shadow-yellow-500/50">
                        ⭐ Destaque
                    </span>
                </div>
                @endif
            </div>
            @endif

            <!-- Conteúdo do Card -->
            <div class="p-5">
                <div class="mb-3">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">{{ $produto->nome }}</h3>
                    <p class="text-sm text-red-600 dark:text-red-400 font-medium">{{ $produto->categoria->nome }}</p>
                </div>

                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-2 min-h-[2.5rem]">{{ $produto->descricao }}</p>

                <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                    <span class="text-2xl font-bold text-red-600 dark:text-red-400">R$ {{ number_format($produto->preco, 2, ',', '.') }}</span>
                    <div class="flex items-center space-x-1 text-gray-600 dark:text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-xs font-medium">{{ $produto->tempo_preparo }} min</span>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="flex justify-between items-center">
                    <a href="{{ route('produtos.edit', $produto) }}" class="text-gray-600 dark:text-gray-400 hover:text-red-500 dark:hover:text-red-400 font-medium flex items-center space-x-1 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span>Editar</span>
                    </a>
                    <form action="{{ route('produtos.destroy', $produto) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 font-medium flex items-center space-x-1 transition-colors" onclick="return confirm('Tem certeza que deseja excluir este produto?')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            <span>Excluir</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">Nenhum produto cadastrado</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Comece criando seu primeiro produto</p>
                <a href="{{ route('produtos.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white rounded-xl font-semibold shadow-lg shadow-red-500/30 hover:shadow-red-500/50 transition-all duration-200">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Criar Produto
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Paginação -->
    @if($produtos->hasPages())
    <div class="mt-6">
        {{ $produtos->links() }}
    </div>
    @endif
</div>
@endsection
