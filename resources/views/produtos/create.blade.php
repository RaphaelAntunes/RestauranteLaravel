@extends('layouts.app')

@section('title', 'Novo Produto')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Novo Produto</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Adicione um novo produto ao cardápio</p>
        </div>
        <a href="{{ route('produtos.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-red-500 dark:hover:text-red-400 font-medium flex items-center space-x-1 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Voltar</span>
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-6">
        <form action="{{ route('produtos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="space-y-5">
                <div>
                    <label for="categoria_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Categoria *</label>
                    <select name="categoria_id" id="categoria_id" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 dark:focus:ring-red-400 focus:border-transparent transition-colors">
                        <option value="">Selecione uma categoria</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                {{ $categoria->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="nome" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nome do Produto *</label>
                    <input type="text" name="nome" id="nome" value="{{ old('nome') }}" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 dark:focus:ring-red-400 focus:border-transparent transition-colors">
                </div>

                <div>
                    <label for="descricao" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Descrição</label>
                    <textarea name="descricao" id="descricao" rows="4"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 dark:focus:ring-red-400 focus:border-transparent transition-colors">{{ old('descricao') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="preco" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Preço (R$) *</label>
                        <input type="number" step="0.01" name="preco" id="preco" value="{{ old('preco') }}" required
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 dark:focus:ring-red-400 focus:border-transparent transition-colors">
                    </div>

                    <div>
                        <label for="tempo_preparo" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tempo Preparo (min) *</label>
                        <input type="number" name="tempo_preparo" id="tempo_preparo" value="{{ old('tempo_preparo', 15) }}" required
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 dark:focus:ring-red-400 focus:border-transparent transition-colors">
                    </div>
                </div>

                <div>
                    <label for="imagem" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Imagem do Produto</label>
                    <input type="file" name="imagem" id="imagem" accept="image/*"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 dark:focus:ring-red-400 focus:border-transparent transition-colors file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 dark:file:bg-red-900/20 dark:file:text-red-400">
                </div>

                <div>
                    <label for="ordem" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Ordem de Exibicao</label>
                    <input type="number" name="ordem" id="ordem" value="{{ old('ordem', 0) }}" min="0"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 dark:focus:ring-red-400 focus:border-transparent transition-colors">
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Menor numero aparece primeiro no cardapio (0 = padrao)</p>
                </div>

                <div class="flex items-center space-x-6 pt-2">
                    <div class="flex items-center">
                        <input type="checkbox" name="ativo" id="ativo" value="1" checked
                            class="h-4 w-4 text-red-600 focus:ring-red-500 dark:focus:ring-red-400 border-gray-300 dark:border-gray-600 rounded">
                        <label for="ativo" class="ml-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Produto Ativo</label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="destaque" id="destaque" value="1"
                            class="h-4 w-4 text-red-600 focus:ring-red-500 dark:focus:ring-red-400 border-gray-300 dark:border-gray-600 rounded">
                        <label for="destaque" class="ml-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Produto em Destaque</label>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-end gap-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('produtos.index') }}" class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition-colors text-center">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white rounded-lg font-semibold shadow-lg shadow-red-500/30 hover:shadow-red-500/50 transition-all duration-200">
                    Criar Produto
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
