@extends('layouts.app')

@section('title', 'Novo Tamanho')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('produto-tamanhos.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Novo Tamanho</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Adicione um novo tamanho para um produto</p>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('produto-tamanhos.store') }}" method="POST" class="dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-6">
        @csrf

        <!-- Produto -->
        <div>
            <label for="produto_id" class="block text-sm font-medium text-gray-100 mb-2">Produto *</label>
            <select name="produto_id" id="produto_id" required class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-100 focus:ring-2 focus:ring-red-500 focus:border-transparent transition">
                <option value="">Selecione um produto</option>
                @foreach($produtos as $produto)
                    <option value="{{ $produto->id }}" {{ old('produto_id') == $produto->id ? 'selected' : '' }}>
                        {{ $produto->nome }}
                    </option>
                @endforeach
            </select>
            @error('produto_id')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nome -->
            <div>
                <label for="nome" class="block text-sm font-medium text-gray-100 mb-2">Tamanho *</label>
                <input type="text" name="nome" id="nome" value="{{ old('nome') }}" required class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-100 focus:ring-2 focus:ring-red-500 focus:border-transparent transition" placeholder="Ex: P, M, G, GG">
                @error('nome')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Descrição -->
            <div>
                <label for="descricao" class="block text-sm font-medium text-gray-100 mb-2">Descrição</label>
                <input type="text" name="descricao" id="descricao" value="{{ old('descricao') }}" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-100 focus:ring-2 focus:ring-red-500 focus:border-transparent transition" placeholder="Ex: Pequena, Média, Grande">
                @error('descricao')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Preço -->
            <div>
                <label for="preco" class="block text-sm font-medium text-gray-100 mb-2">Preço *</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">R$</span>
                    <input type="number" name="preco" id="preco" value="{{ old('preco') }}" step="0.01" min="0" required class="w-full pl-12 pr-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-100 focus:ring-2 focus:ring-red-500 focus:border-transparent transition" placeholder="0,00">
                </div>
                @error('preco')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Máximo de Sabores -->
            <div>
                <label for="max_sabores" class="block text-sm font-medium text-gray-100 mb-2">Máximo de Sabores *</label>
                <input type="number" name="max_sabores" id="max_sabores" value="{{ old('max_sabores', 1) }}" min="1" max="5" required class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-100 focus:ring-2 focus:ring-red-500 focus:border-transparent transition">
                <p class="mt-1 text-sm text-gray-400">Quantos sabores podem ser escolhidos neste tamanho (1-5)</p>
                @error('max_sabores')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Ordem -->
        <div>
            <label for="ordem" class="block text-sm font-medium text-gray-100 mb-2">Ordem de Exibição</label>
            <input type="number" name="ordem" id="ordem" value="{{ old('ordem', 0) }}" min="0" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-100 focus:ring-2 focus:ring-red-500 focus:border-transparent transition">
            <p class="mt-1 text-sm text-gray-400">Ordem em que o tamanho aparecerá na seleção</p>
            @error('ordem')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Ativo -->
        <div class="flex items-center">
            <input type="checkbox" name="ativo" id="ativo" value="1" {{ old('ativo', true) ? 'checked' : '' }} class="h-4 w-4 text-red-500 bg-gray-700 border-gray-600 rounded focus:ring-red-500 focus:ring-2">
            <label for="ativo" class="ml-2 block text-sm text-gray-100">Tamanho ativo e disponível para pedidos</label>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-700">
            <a href="{{ route('produto-tamanhos.index') }}" class="px-4 py-2 text-gray-300 hover:text-gray-100 transition">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2 bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white rounded-lg font-semibold shadow-lg transition-all duration-200">
                Criar Tamanho
            </button>
        </div>
    </form>
</div>
@endsection
