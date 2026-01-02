@extends('layouts.app')

@section('title', 'Histórico de Pagamentos')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Histórico de Pagamentos</h1>
        <a href="{{ route('pdv.index') }}" class="text-gray-600 hover:text-gray-900">← Voltar ao PDV</a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data/Hora</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mesa</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Atendente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Forma Pgto</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($pagamentos as $pagamento)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $pagamento->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $pagamento->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">Mesa {{ $pagamento->mesa->numero }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $pagamento->user->nome }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ ucwords(str_replace('_', ' ', $pagamento->forma_pagamento)) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600 text-right">R$ {{ number_format($pagamento->total, 2, ',', '.') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                        <a href="{{ route('pdv.comprovante', $pagamento) }}" class="text-indigo-600 hover:text-indigo-900">Ver Comprovante</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">Nenhum pagamento registrado</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $pagamentos->links() }}
    </div>
</div>
@endsection
