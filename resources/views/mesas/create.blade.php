@extends('layouts.app')

@section('title', 'Nova Mesa')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Nova Mesa</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Adicione uma nova mesa ao restaurante</p>
        </div>
        <a href="{{ route('mesas.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-red-500 dark:hover:text-red-400 font-medium flex items-center space-x-1 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Voltar</span>
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-6">
        <form action="{{ route('mesas.store') }}" method="POST">
            @csrf

            <div class="space-y-5">
                <div>
                    <label for="numero" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Número da Mesa *</label>
                    <input type="number" name="numero" id="numero" value="{{ old('numero') }}" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 dark:focus:ring-red-400 focus:border-transparent transition-colors">
                </div>

                <div>
                    <label for="capacidade" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Capacidade (pessoas) *</label>
                    <input type="number" name="capacidade" id="capacidade" value="{{ old('capacidade', 4) }}" required min="1"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 dark:focus:ring-red-400 focus:border-transparent transition-colors">
                </div>

                <div>
                    <label for="localizacao" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Localização</label>
                    <input type="text" name="localizacao" id="localizacao" value="{{ old('localizacao') }}"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 dark:focus:ring-red-400 focus:border-transparent transition-colors" placeholder="Ex: Área externa, Salão principal">
                </div>

                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Status *</label>
                    <select name="status" id="status" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 dark:focus:ring-red-400 focus:border-transparent transition-colors">
                        <option value="disponivel" {{ old('status') == 'disponivel' ? 'selected' : '' }}>Disponível</option>
                        <option value="ocupada" {{ old('status') == 'ocupada' ? 'selected' : '' }}>Ocupada</option>
                        <option value="reservada" {{ old('status') == 'reservada' ? 'selected' : '' }}>Reservada</option>
                        <option value="manutencao" {{ old('status') == 'manutencao' ? 'selected' : '' }}>Manutenção</option>
                    </select>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-end gap-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('mesas.index') }}" class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition-colors text-center">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white rounded-lg font-semibold shadow-lg shadow-red-500/30 hover:shadow-red-500/50 transition-all duration-200">
                    Criar Mesa
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
