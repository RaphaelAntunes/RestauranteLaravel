@extends('layouts.app')

@section('title', 'Novo Sabor')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('sabores.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Novo Sabor</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Adicione um novo sabor ao cardápio</p>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('sabores.store') }}" method="POST" enctype="multipart/form-data" class="dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-6">
        @csrf

        <!-- Categoria -->
        <div>
            <label for="categoria_id" class="block text-sm font-medium text-gray-100 mb-2">Categoria *</label>
            <select name="categoria_id" id="categoria_id" required class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-100 focus:ring-2 focus:ring-red-500 focus:border-transparent transition">
                <option value="">Selecione uma categoria</option>
                @foreach($categorias as $categoria)
                    <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                        {{ $categoria->nome }}
                    </option>
                @endforeach
            </select>
            @error('categoria_id')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Nome -->
        <div>
            <label for="nome" class="block text-sm font-medium text-gray-100 mb-2">Nome do Sabor *</label>
            <input type="text" name="nome" id="nome" value="{{ old('nome') }}" required class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-100 focus:ring-2 focus:ring-red-500 focus:border-transparent transition" placeholder="Ex: Calabresa, Marguerita">
            @error('nome')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Descrição -->
        <div>
            <label for="descricao" class="block text-sm font-medium text-gray-100 mb-2">Descrição</label>
            <textarea name="descricao" id="descricao" rows="3" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-100 focus:ring-2 focus:ring-red-500 focus:border-transparent transition" placeholder="Breve descrição do sabor">{{ old('descricao') }}</textarea>
            @error('descricao')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Ingredientes -->
        <div>
            <label for="ingredientes" class="block text-sm font-medium text-gray-100 mb-2">Ingredientes</label>
            <textarea name="ingredientes" id="ingredientes" rows="3" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-100 focus:ring-2 focus:ring-red-500 focus:border-transparent transition" placeholder="Liste os ingredientes principais">{{ old('ingredientes') }}</textarea>
            @error('ingredientes')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Imagem -->
        <div>
            <label for="imagem" class="block text-sm font-medium text-gray-100 mb-2">Imagem</label>
            <input type="file" name="imagem" id="imagem" accept="image/*" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-100 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-red-500 file:text-white hover:file:bg-red-600 transition">
            <p class="mt-1 text-sm text-gray-400">Formatos aceitos: JPG, PNG. Tamanho máximo: 2MB</p>
            @error('imagem')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Ordem -->
        <div>
            <label for="ordem" class="block text-sm font-medium text-gray-100 mb-2">Ordem de Exibição</label>
            <input type="number" name="ordem" id="ordem" value="{{ old('ordem', 0) }}" min="0" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-100 focus:ring-2 focus:ring-red-500 focus:border-transparent transition">
            <p class="mt-1 text-sm text-gray-400">Ordem em que o sabor aparecerá no cardápio</p>
            @error('ordem')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Ativo -->
        <div class="flex items-center">
            <input type="checkbox" name="ativo" id="ativo" value="1" {{ old('ativo', true) ? 'checked' : '' }} class="h-4 w-4 text-red-500 bg-gray-700 border-gray-600 rounded focus:ring-red-500 focus:ring-2">
            <label for="ativo" class="ml-2 block text-sm text-gray-100">Sabor ativo e disponível para pedidos</label>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-700">
            <a href="{{ route('sabores.index') }}" class="px-4 py-2 text-gray-300 hover:text-gray-100 transition">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2 bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white rounded-lg font-semibold shadow-lg transition-all duration-200">
                Criar Sabor
            </button>
        </div>
    </form>
</div>
@endsection
