@extends('layouts.cliente')

@section('title', 'Finalizar Pedido')

@section('content')
<h1 class="text-3xl font-bold mb-6">Finalizar Pedido</h1>

<form action="{{ route('cliente.pedido.finalizar') }}" method="POST">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-bold text-xl mb-4">Tipo de Pedido</h3>

                <div class="space-y-3">
                    <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="tipo_pedido" value="delivery" class="mr-3" required>
                        <div>
                            <span class="font-semibold">Delivery</span>
                            <p class="text-sm text-gray-600">Entrega no endereço</p>
                        </div>
                    </label>

                    <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="tipo_pedido" value="retirada" class="mr-3">
                        <div>
                            <span class="font-semibold">Retirada</span>
                            <p class="text-sm text-gray-600">Retirar no local</p>
                        </div>
                    </label>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6" id="endereco-section" style="display:none;">
                <h3 class="font-bold text-xl mb-4">Endereço de Entrega</h3>

                @if($enderecos->count() > 0)
                    @foreach($enderecos as $endereco)
                        <label class="flex items-start p-4 border rounded-lg cursor-pointer hover:bg-gray-50 mb-3">
                            <input type="radio" name="cliente_endereco_id" value="{{ $endereco->id }}" class="mt-1 mr-3" {{ $endereco->padrao ? 'checked' : '' }}>
                            <div>
                                <span class="font-semibold">{{ $endereco->nome_endereco }}</span>
                                <p class="text-sm text-gray-600">{{ $endereco->getEnderecoCompleto() }}</p>
                            </div>
                        </label>
                    @endforeach
                @else
                    <p class="text-gray-600 mb-4">Você ainda não tem endereços cadastrados.</p>
                    <a href="{{ route('cliente.enderecos.create') }}" class="text-red-600 hover:underline">
                        Cadastrar endereço
                    </a>
                @endif
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-bold text-xl mb-4">Observações</h3>
                <textarea name="observacoes" rows="3" class="w-full border rounded-lg p-3" placeholder="Alguma observação sobre o pedido?"></textarea>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6 sticky top-4">
                <h3 class="font-bold text-xl mb-4">Resumo</h3>

                <div class="space-y-2 mb-4">
                    <div class="flex justify-between">
                        <span>Subtotal:</span>
                        <span>R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Taxa de Entrega:</span>
                        <span id="taxa-display">-</span>
                    </div>
                    <div class="border-t pt-2 flex justify-between font-bold text-lg">
                        <span>Total:</span>
                        <span class="text-red-600" id="total-display">R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                    </div>
                </div>

                <button type="submit" class="w-full bg-red-600 text-white py-3 rounded-lg hover:bg-red-700 font-semibold">
                    Confirmar Pedido
                </button>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
    const subtotal = {{ $subtotal }};
    const taxaEntrega = {{ $config->calcularTaxaEntrega($subtotal) }};

    document.querySelectorAll('input[name="tipo_pedido"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const enderecoSection = document.getElementById('endereco-section');
            const taxaDisplay = document.getElementById('taxa-display');
            const totalDisplay = document.getElementById('total-display');

            if (this.value === 'delivery') {
                enderecoSection.style.display = 'block';
                taxaDisplay.textContent = 'R$ ' + taxaEntrega.toFixed(2).replace('.', ',');
                const total = subtotal + taxaEntrega;
                totalDisplay.textContent = 'R$ ' + total.toFixed(2).replace('.', ',');
            } else {
                enderecoSection.style.display = 'none';
                taxaDisplay.textContent = 'R$ 0,00';
                totalDisplay.textContent = 'R$ ' + subtotal.toFixed(2).replace('.', ',');
            }
        });
    });
</script>
@endpush
@endsection
