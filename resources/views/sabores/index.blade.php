@extends('layouts.app')

@section('title', 'Sabores')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Gerenciar Sabores</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Configure os sabores disponíveis para pizzas</p>
        </div>
        <a href="{{ route('sabores.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white rounded-lg font-semibold shadow-lg transition-all duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Novo Sabor
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="dark:bg-gray-800 border-l-4 border-green-500 p-4 rounded-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-400">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Table -->
    <div class="dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Imagem</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Nome</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Categoria</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Ingredientes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Precos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Ordem</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-300 uppercase tracking-wider">Acoes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($sabores as $sabor)
                    <tr class="dark:bg-gray-800 hover:border-red-500 dark:hover:border-red-500 transition-all duration-200 border-l-4 border-transparent">
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($sabor->imagem)
                                <img src="{{ asset('storage/' . $sabor->imagem) }}" alt="{{ $sabor->nome }}" class="h-12 w-12 rounded-lg object-cover border border-gray-600">
                            @else
                                <div class="h-12 w-12 rounded-lg bg-gray-700 flex items-center justify-center border border-gray-600">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-100">{{ $sabor->nome }}</div>
                            @if($sabor->descricao)
                                <div class="text-sm text-gray-400">{{ Str::limit($sabor->descricao, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-900/40 text-purple-300 border border-purple-800">
                                {{ $sabor->categoria->nome }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-300">{{ Str::limit($sabor->ingredientes ?? 'Não informado', 60) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($sabor->preco_p || $sabor->preco_m || $sabor->preco_g || $sabor->preco_gg)
                                <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold rounded-full bg-yellow-900/40 text-yellow-300 border border-yellow-800">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                                    </svg>
                                    Especial
                                </span>
                                <div class="mt-1 text-xs text-gray-400">
                                    @if($sabor->preco_p)P: R$ {{ number_format($sabor->preco_p, 2, ',', '.') }} @endif
                                    @if($sabor->preco_m)M: R$ {{ number_format($sabor->preco_m, 2, ',', '.') }} @endif
                                    @if($sabor->preco_g)G: R$ {{ number_format($sabor->preco_g, 2, ',', '.') }} @endif
                                    @if($sabor->preco_gg)GG: R$ {{ number_format($sabor->preco_gg, 2, ',', '.') }}@endif
                                </div>
                            @else
                                <span class="text-xs text-gray-500">Padrao</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($sabor->ativo)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-900/40 text-green-300 border border-green-800">Ativo</span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-900/40 text-red-300 border border-red-800">Inativo</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                            {{ $sabor->ordem }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('sabores.edit', $sabor) }}" class="text-blue-400 hover:text-blue-300 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('sabores.destroy', $sabor) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir este sabor?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-400">Nenhum sabor cadastrado</h3>
                            <p class="mt-1 text-sm text-gray-500">Comece criando um novo sabor.</p>
                            <div class="mt-6">
                                <a href="{{ route('sabores.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white rounded-lg font-semibold shadow-lg transition-all duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Novo Sabor
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
