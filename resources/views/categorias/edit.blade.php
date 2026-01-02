@extends('layouts.app')

@section('title', 'Editar Categoria')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Editar Categoria</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Atualize as informações da categoria</p>
        </div>
        <a href="{{ route('categorias.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-red-500 dark:hover:text-red-400 font-medium flex items-center space-x-1 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Voltar</span>
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-6">
        <form action="{{ route('categorias.update', $categoria) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-5">
                <div>
                    <label for="nome" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nome da Categoria *</label>
                    <input type="text" name="nome" id="nome" value="{{ old('nome', $categoria->nome) }}" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 dark:focus:ring-red-400 focus:border-transparent transition-colors">
                </div>

                <div>
                    <label for="descricao" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Descrição</label>
                    <textarea name="descricao" id="descricao" rows="4"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 dark:focus:ring-red-400 focus:border-transparent transition-colors">{{ old('descricao', $categoria->descricao) }}</textarea>
                </div>

                <div>
                    <label for="ordem" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Ordem de Exibição *</label>
                    <input type="number" name="ordem" id="ordem" value="{{ old('ordem', $categoria->ordem) }}" required min="1"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 dark:focus:ring-red-400 focus:border-transparent transition-colors">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Define a ordem em que esta categoria aparecerá no cardápio</p>
                </div>

                <div class="flex items-center pt-2">
                    <input type="checkbox" name="ativo" id="ativo" value="1" {{ old('ativo', $categoria->ativo) ? 'checked' : '' }}
                        class="h-4 w-4 text-red-600 focus:ring-red-500 dark:focus:ring-red-400 border-gray-300 dark:border-gray-600 rounded">
                    <label for="ativo" class="ml-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Categoria Ativa
                    </label>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-end gap-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('categorias.index') }}" class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition-colors text-center">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white rounded-lg font-semibold shadow-lg shadow-red-500/30 hover:shadow-red-500/50 transition-all duration-200">
                    Atualizar Categoria
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
