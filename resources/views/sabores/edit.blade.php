@extends('layouts.app')

@section('title', 'Editar Sabor')

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
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Editar Sabor</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Atualize as informações do sabor</p>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('sabores.update', $sabor) }}" method="POST" enctype="multipart/form-data" class="dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-6">
        @csrf
        @method('PUT')

        <!-- Categoria -->
        <div>
            <label for="categoria_id" class="block text-sm font-medium text-gray-100 mb-2">Categoria *</label>
            <select name="categoria_id" id="categoria_id" required class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-100 focus:ring-2 focus:ring-red-500 focus:border-transparent transition">
                <option value="">Selecione uma categoria</option>
                @foreach($categorias as $categoria)
                    <option value="{{ $categoria->id }}" {{ old('categoria_id', $sabor->categoria_id) == $categoria->id ? 'selected' : '' }}>
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
            <input type="text" name="nome" id="nome" value="{{ old('nome', $sabor->nome) }}" required class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-100 focus:ring-2 focus:ring-red-500 focus:border-transparent transition" placeholder="Ex: Calabresa, Marguerita">
            @error('nome')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Descrição -->
        <div>
            <label for="descricao" class="block text-sm font-medium text-gray-100 mb-2">Descrição</label>
            <textarea name="descricao" id="descricao" rows="3" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-100 focus:ring-2 focus:ring-red-500 focus:border-transparent transition" placeholder="Breve descrição do sabor">{{ old('descricao', $sabor->descricao) }}</textarea>
            @error('descricao')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Ingredientes -->
        <div>
            <label for="ingredientes" class="block text-sm font-medium text-gray-100 mb-2">Ingredientes</label>
            <textarea name="ingredientes" id="ingredientes" rows="3" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-100 focus:ring-2 focus:ring-red-500 focus:border-transparent transition" placeholder="Liste os ingredientes principais">{{ old('ingredientes', $sabor->ingredientes) }}</textarea>
            @error('ingredientes')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Preços Especiais -->
        <div class="border border-yellow-500/30 rounded-lg p-4 bg-yellow-500/5">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm font-medium text-yellow-500">Precos Especiais (opcional)</span>
            </div>
            <p class="text-xs text-gray-400 mb-4">Deixe em branco para usar o preco padrao do tamanho. Preencha apenas se este sabor tiver preco diferenciado (ex: Carne de Sol, Palmito).</p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label for="preco_p" class="block text-sm font-medium text-gray-100 mb-2">Pequena (P)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">R$</span>
                        <input type="number" name="preco_p" id="preco_p" value="{{ old('preco_p', $sabor->preco_p) }}" step="0.01" min="0" class="w-full pl-10 pr-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-100 focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition" placeholder="0,00">
                    </div>
                    @error('preco_p')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="preco_m" class="block text-sm font-medium text-gray-100 mb-2">Media (M)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">R$</span>
                        <input type="number" name="preco_m" id="preco_m" value="{{ old('preco_m', $sabor->preco_m) }}" step="0.01" min="0" class="w-full pl-10 pr-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-100 focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition" placeholder="0,00">
                    </div>
                    @error('preco_m')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="preco_g" class="block text-sm font-medium text-gray-100 mb-2">Grande (G)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">R$</span>
                        <input type="number" name="preco_g" id="preco_g" value="{{ old('preco_g', $sabor->preco_g) }}" step="0.01" min="0" class="w-full pl-10 pr-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-100 focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition" placeholder="0,00">
                    </div>
                    @error('preco_g')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="preco_gg" class="block text-sm font-medium text-gray-100 mb-2">Gigante (GG)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">R$</span>
                        <input type="number" name="preco_gg" id="preco_gg" value="{{ old('preco_gg', $sabor->preco_gg) }}" step="0.01" min="0" class="w-full pl-10 pr-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-100 focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition" placeholder="0,00">
                    </div>
                    @error('preco_gg')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Imagem Atual -->
        @if($sabor->imagem)
        <div>
            <label class="block text-sm font-medium text-gray-100 mb-2">Imagem Atual</label>
            <img src="{{ asset('storage/' . $sabor->imagem) }}" alt="{{ $sabor->nome }}" class="h-32 w-32 rounded-lg object-cover border-2 border-gray-600">
        </div>
        @endif

        <!-- Nova Imagem -->
        <div>
            <label for="imagem" class="block text-sm font-medium text-gray-100 mb-2">{{ $sabor->imagem ? 'Substituir Imagem' : 'Imagem' }}</label>
            <input type="file" name="imagem" id="imagem" accept="image/*" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-100 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-red-500 file:text-white hover:file:bg-red-600 transition">
            <p class="mt-1 text-sm text-gray-400">Formatos aceitos: JPG, PNG. Tamanho máximo: 2MB</p>
            @error('imagem')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Ordem -->
        <div>
            <label for="ordem" class="block text-sm font-medium text-gray-100 mb-2">Ordem de Exibição</label>
            <input type="number" name="ordem" id="ordem" value="{{ old('ordem', $sabor->ordem) }}" min="0" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-100 focus:ring-2 focus:ring-red-500 focus:border-transparent transition">
            <p class="mt-1 text-sm text-gray-400">Ordem em que o sabor aparecerá no cardápio</p>
            @error('ordem')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Ativo -->
        <div class="flex items-center">
            <input type="checkbox" name="ativo" id="ativo" value="1" {{ old('ativo', $sabor->ativo) ? 'checked' : '' }} class="h-4 w-4 text-red-500 bg-gray-700 border-gray-600 rounded focus:ring-red-500 focus:ring-2">
            <label for="ativo" class="ml-2 block text-sm text-gray-100">Sabor ativo e disponível para pedidos</label>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-700">
            <a href="{{ route('sabores.index') }}" class="px-4 py-2 text-gray-300 hover:text-gray-100 transition">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2 bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white rounded-lg font-semibold shadow-lg transition-all duration-200">
                Atualizar Sabor
            </button>
        </div>
    </form>
</div>
@endsection
