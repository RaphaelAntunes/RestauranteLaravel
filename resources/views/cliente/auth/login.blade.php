@extends('layouts.cliente')

@section('title', 'Login - Pedidos Online')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h2 class="text-2xl font-bold text-center mb-6">Fazer Pedido</h2>
        <p class="text-gray-600 text-center mb-6">Digite seu número de celular para receber o código de acesso</p>

        <form action="{{ route('cliente.otp.enviar') }}" method="POST">
            @csrf
            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">Celular</label>
                <input type="text" name="celular" id="celular" placeholder="84996541082 ou (84) 99654-1082"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-red-500"
                    required>
                @error('celular')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-sm text-gray-500 mt-1">Digite apenas os números ou com formatação</p>
            </div>

            <button type="submit" class="w-full bg-red-600 text-white py-3 rounded-lg hover:bg-red-700 font-semibold">
                Enviar Código
            </button>
        </form>

        <p class="text-center text-gray-600 text-sm mt-6">
            Enviaremos um código de 6 dígitos para o seu celular
        </p>
    </div>
</div>

@push('scripts')
<script>
    // Permite apenas números no campo
    document.getElementById('celular').addEventListener('input', function(e) {
        // Remove tudo que não for número
        let value = this.value.replace(/\D/g, '');

        // Formata automaticamente enquanto digita (opcional)
        if (value.length >= 11) {
            value = value.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
        } else if (value.length >= 7) {
            value = value.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
        } else if (value.length >= 3) {
            value = value.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
        }

        this.value = value;
    });
</script>
@endpush
@endsection
