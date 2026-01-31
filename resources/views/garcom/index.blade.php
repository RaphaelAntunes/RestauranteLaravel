@extends('layouts.garcom')

@section('title', 'Mesas')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-white">Mesas do Restaurante</h1>
            <p class="mt-1 text-xs sm:text-sm text-gray-400">Selecione uma mesa para atender</p>
        </div>
    </div>

    <!-- Legenda de Status -->
    <div class="bg-gray-800 rounded-xl shadow-sm border border-gray-700 p-4">
        <div class="flex flex-wrap items-center justify-center gap-4 sm:gap-6 text-sm">
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-green-500 rounded-full shadow-lg shadow-green-500/50"></div>
                <span class="text-gray-300 font-medium">Disponível</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-red-500 rounded-full shadow-lg shadow-red-500/50"></div>
                <span class="text-gray-300 font-medium">Ocupada</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-purple-500 rounded-full shadow-lg shadow-purple-500/50"></div>
                <span class="text-gray-300 font-medium">Delivery</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-indigo-500 rounded-full shadow-lg shadow-indigo-500/50"></div>
                <span class="text-gray-300 font-medium">Retirada</span>
            </div>
        </div>
    </div>

    @php
        $mesasNormais = $mesas->where('tipo', 'normal');
        $mesasOnline = $mesas->whereIn('tipo', ['delivery', 'retirada']);
    @endphp

    <!-- Pedidos Online (Delivery/Retirada) -->
    @if($mesasOnline->count() > 0)
    <div class="bg-gradient-to-r from-purple-900/30 to-indigo-900/30 rounded-xl p-4 sm:p-6 border border-purple-700">
        <h2 class="text-lg sm:text-xl font-bold text-white mb-4 flex items-center">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Pedidos Online ({{ $mesasOnline->count() }})
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($mesasOnline as $mesa)
            <div class="group bg-gray-800 rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border-2 cursor-pointer
                {{ $mesa->tipo == 'delivery' ? 'border-purple-500' : 'border-indigo-500' }}"
                onclick="showLoading('Abrindo comanda...'); window.location.href='{{ route('garcom.comanda', $mesa) }}'">

                <div class="p-4 {{ $mesa->tipo == 'delivery' ? 'bg-gradient-to-br from-purple-600 to-purple-700' : 'bg-gradient-to-br from-indigo-600 to-indigo-700' }}">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-xs font-bold text-white/80 uppercase tracking-wider">{{ $mesa->tipo == 'delivery' ? 'Delivery' : 'Retirada' }}</span>
                            <h3 class="text-xl sm:text-2xl font-bold text-white">{{ $mesa->pedidoOnline ? $mesa->pedidoOnline->numero_pedido : '#' . $mesa->numero }}</h3>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    @if($mesa->cliente_nome)
                    <div class="flex items-center space-x-2 mb-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="font-semibold text-gray-300 text-sm truncate">{{ $mesa->cliente_nome }}</span>
                    </div>
                    @endif
                    @if($mesa->pedidos_count > 0)
                    <div class="flex items-center space-x-2 bg-red-900/30 p-2 rounded-lg border border-red-800">
                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-bold text-red-400 text-sm">{{ $mesa->pedidos_count }} pedido(s)</span>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Grid de Mesas Normais -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
        @forelse($mesasNormais as $mesa)
        <div class="group bg-gray-800 rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-700 hover:border-
            {{-- Status border color --}}
            @if($mesa->status == 'disponivel') green-400
            @elseif($mesa->status == 'ocupada') red-400
            @elseif($mesa->status == 'reservada') yellow-400
            @else gray-400 @endif
            @if($mesa->status == 'disponivel' || $mesa->status == 'ocupada' || $mesa->pedidos_count > 0) cursor-pointer @endif"
            @if($mesa->status == 'disponivel' || $mesa->status == 'ocupada' || $mesa->pedidos_count > 0)
                onclick="showLoading('Abrindo comanda da Mesa {{ $mesa->numero }}...'); window.location.href='{{ route('garcom.comanda', $mesa) }}'"
            @endif>

            <!-- Header da Mesa -->
            <div class="p-4 sm:p-6 relative
                @if($mesa->status == 'disponivel') bg-gradient-to-br from-green-500 to-green-600
                @elseif($mesa->status == 'ocupada') bg-gradient-to-br from-red-500 to-red-600
                @elseif($mesa->status == 'reservada') bg-gradient-to-br from-yellow-500 to-yellow-600
                @else bg-gradient-to-br from-gray-500 to-gray-600 @endif">

                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-3xl sm:text-4xl font-bold text-white mb-1">{{ $mesa->numero }}</h3>
                        <p class="text-xs sm:text-sm text-white/90 truncate">{{ $mesa->localizacao ?? 'Sem localização' }}</p>
                    </div>
                    <span class="px-2 sm:px-3 py-1 sm:py-1.5 rounded-full text-xs font-bold bg-white/20 backdrop-blur-sm text-white border border-white/30 shadow-lg whitespace-nowrap">
                        {{ ucfirst($mesa->status) }}
                    </span>
                </div>
            </div>

            <!-- Conteúdo da Mesa -->
            <div class="p-4 sm:p-6">
                <div class="space-y-2 sm:space-y-3">
                    <div class="flex items-center space-x-2 text-gray-300">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span class="font-semibold text-sm sm:text-base">{{ $mesa->capacidade }} pessoas</span>
                    </div>
                    @if($mesa->cliente_nome)
                    <div class="flex items-center space-x-2 bg-blue-900/20 p-2 sm:p-2.5 rounded-lg border border-blue-800">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="font-bold text-blue-300 text-xs sm:text-sm truncate">{{ $mesa->cliente_nome }}</span>
                    </div>
                    @endif
                    @if($mesa->pedidos_count > 0)
                    <div class="flex items-center space-x-2 bg-red-900/20 p-2 sm:p-2.5 rounded-lg border border-red-800">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-bold text-red-400 text-xs sm:text-sm">{{ $mesa->pedidos_count }} pedido(s) ativo(s)</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="bg-gray-800 border border-gray-700 rounded-xl shadow-sm p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-100 mb-2">Nenhuma mesa cadastrada</h3>
                <p class="text-gray-400">Entre em contato com o administrador</p>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
