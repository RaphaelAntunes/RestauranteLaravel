@extends('layouts.app')

@section('title', 'Categorias')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Categorias de Produtos</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Organize os produtos do seu cardápio</p>
        </div>
        <a href="{{ route('categorias.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white font-semibold rounded-xl shadow-lg shadow-red-500/30 hover:shadow-red-500/50 transition-all duration-200">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Nova Categoria
        </a>
    </div>

    <!-- Grid de Categorias -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($categorias as $categoria)
        <div class="group bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-200 dark:border-gray-700 hover:border-red-400 dark:hover:border-red-500">
            <!-- Header da Categoria -->
            <div class="p-6 bg-gradient-to-br from-red-500 to-orange-600 relative">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold text-white mb-1">{{ $categoria->nome }}</h3>
                        <p class="text-sm text-white/90">Ordem: {{ $categoria->ordem }}</p>
                    </div>
                    <span class="px-3 py-1.5 rounded-full text-xs font-bold {{ $categoria->ativo ? 'bg-green-500/20 text-white border border-white/30' : 'bg-gray-500/20 text-white border border-white/30' }} backdrop-blur-sm shadow-lg">
                        {{ $categoria->ativo ? 'Ativo' : 'Inativo' }}
                    </span>
                </div>
            </div>

            <!-- Conteúdo da Categoria -->
            <div class="p-6">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 min-h-[3rem]">{{ $categoria->descricao ?? 'Sem descrição' }}</p>

                <div class="flex items-center justify-between mb-5 pb-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <span class="text-lg font-bold text-red-600 dark:text-red-400">{{ $categoria->produtos_count }}</span>
                        <span class="text-sm text-gray-600 dark:text-gray-400">produto(s)</span>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="flex justify-between items-center">
                    <a href="{{ route('categorias.edit', $categoria) }}" class="text-gray-600 dark:text-gray-400 hover:text-red-500 dark:hover:text-red-400 font-medium flex items-center space-x-1 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span>Editar</span>
                    </a>
                    <form action="{{ route('categorias.destroy', $categoria) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 font-medium flex items-center space-x-1 transition-colors" onclick="return confirm('Tem certeza que deseja excluir esta categoria?')">
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">Nenhuma categoria cadastrada</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Comece criando sua primeira categoria</p>
                <a href="{{ route('categorias.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white rounded-xl font-semibold shadow-lg shadow-red-500/30 hover:shadow-red-500/50 transition-all duration-200">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Criar Categoria
                </a>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
