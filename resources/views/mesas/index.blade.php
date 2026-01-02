@extends('layouts.app')

@section('title', 'Mesas')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Mesas do Restaurante</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Gerencie as mesas do seu restaurante</p>
        </div>
        <a href="{{ route('mesas.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white font-semibold rounded-xl shadow-lg shadow-red-500/30 hover:shadow-red-500/50 transition-all duration-200">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Nova Mesa
        </a>
    </div>

    <!-- Legenda de Status -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <div class="flex flex-wrap items-center justify-center gap-6 text-sm">
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-green-500 rounded-full shadow-lg shadow-green-500/50"></div>
                <span class="text-gray-700 dark:text-gray-300 font-medium">Disponível</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-red-500 rounded-full shadow-lg shadow-red-500/50"></div>
                <span class="text-gray-700 dark:text-gray-300 font-medium">Ocupada</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-yellow-500 rounded-full shadow-lg shadow-yellow-500/50"></div>
                <span class="text-gray-700 dark:text-gray-300 font-medium">Reservada</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-gray-500 rounded-full shadow-lg shadow-gray-500/50"></div>
                <span class="text-gray-700 dark:text-gray-300 font-medium">Manutenção</span>
            </div>
        </div>
    </div>

    <!-- Grid de Mesas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($mesas as $mesa)
        <div class="group bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-200 dark:border-gray-700 hover:border-
            {{-- Status border color --}}
            @if($mesa->status == 'disponivel') green-400 dark:hover:border-green-500
            @elseif($mesa->status == 'ocupada') red-400 dark:hover:border-red-500
            @elseif($mesa->status == 'reservada') yellow-400 dark:hover:border-yellow-500
            @else gray-400 dark:hover:border-gray-500 @endif
            @if($mesa->status == 'disponivel' || $mesa->status == 'ocupada' || $mesa->pedidos_count > 0) cursor-pointer @endif"
            @if($mesa->status == 'disponivel' || $mesa->status == 'ocupada' || $mesa->pedidos_count > 0)
                onclick="showLoading('Abrindo comanda da Mesa {{ $mesa->numero }}...'); window.location.href='{{ route('mesas.comanda', $mesa) }}'"
            @endif>

            <!-- Header da Mesa -->
            <div class="p-6 relative
                @if($mesa->status == 'disponivel') bg-gradient-to-br from-green-500 to-green-600
                @elseif($mesa->status == 'ocupada') bg-gradient-to-br from-red-500 to-red-600
                @elseif($mesa->status == 'reservada') bg-gradient-to-br from-yellow-500 to-yellow-600
                @else bg-gradient-to-br from-gray-500 to-gray-600 @endif">

                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-4xl font-bold text-white mb-1">{{ $mesa->numero }}</h3>
                        <p class="text-sm text-white/90">{{ $mesa->localizacao ?? 'Sem localização' }}</p>
                    </div>
                    <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-white/20 backdrop-blur-sm text-white border border-white/30 shadow-lg">
                        {{ ucfirst($mesa->status) }}
                    </span>
                </div>
            </div>

            <!-- Conteúdo da Mesa -->
            <div class="p-6">
                <div class="space-y-3 mb-4">
                    <div class="flex items-center space-x-2 text-gray-700 dark:text-gray-300">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span class="font-semibold">{{ $mesa->capacidade }} pessoas</span>
                    </div>
                    @if($mesa->cliente_nome)
                    <div class="flex items-center space-x-2 bg-blue-50 dark:bg-blue-900/20 p-2.5 rounded-lg border border-blue-200 dark:border-blue-800">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="font-bold text-blue-700 dark:text-blue-300 text-sm truncate">{{ $mesa->cliente_nome }}</span>
                    </div>
                    @endif
                    @if($mesa->pedidos_count > 0)
                    <div class="flex items-center space-x-2 bg-red-50 dark:bg-red-900/20 p-2.5 rounded-lg border border-red-200 dark:border-red-800">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-bold text-red-600 dark:text-red-400 text-sm">{{ $mesa->pedidos_count }} pedido(s) ativo(s)</span>
                    </div>
                    @endif
                </div>

                <!-- Botões de Ação -->
                <div class="flex justify-between items-center pt-2 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('mesas.edit', $mesa) }}" class="text-gray-600 dark:text-gray-400 hover:text-red-500 dark:hover:text-red-400 font-medium flex items-center space-x-1 transition-colors" onclick="event.stopPropagation()">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span>Editar</span>
                    </a>
                    <form action="{{ route('mesas.destroy', $mesa) }}" method="POST" class="inline" onclick="event.stopPropagation()">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 font-medium flex items-center space-x-1 transition-colors" onclick="return confirm('Tem certeza que deseja excluir esta mesa?')">
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">Nenhuma mesa cadastrada</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Comece criando sua primeira mesa</p>
                <a href="{{ route('mesas.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white rounded-xl font-semibold shadow-lg shadow-red-500/30 hover:shadow-red-500/50 transition-all duration-200">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Criar Mesa
                </a>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
