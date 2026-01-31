@extends('layouts.cliente')

@section('title', 'Verificar Código')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h2 class="text-2xl font-bold text-center mb-6">Digite o Código</h2>
        <p class="text-gray-600 text-center mb-6">
            Enviamos um código de 6 dígitos para<br>
            <strong>{{ session('otp_celular') }}</strong>
        </p>

        <form action="{{ route('cliente.otp.verificar') }}" method="POST">
            @csrf
            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">Código</label>
                <input type="text" name="codigo" id="codigo" maxlength="6" placeholder="000000"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 text-center text-2xl tracking-widest focus:outline-none focus:ring-2 focus:ring-red-500"
                    required autofocus>
                @error('codigo')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full bg-red-600 text-white py-3 rounded-lg hover:bg-red-700 font-semibold">
                Verificar e Entrar
            </button>
        </form>

        <div class="text-center mt-6">
            <a href="{{ route('cliente.login') }}" class="text-red-600 hover:underline">
                Voltar e solicitar novo código
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('codigo').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
</script>
@endpush
@endsection
