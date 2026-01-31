@extends('layouts.cliente')

@section('title', 'Cadastrar Endereço')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">Cadastrar Endereço</h1>

    <form action="{{ route('cliente.enderecos.store') }}" method="POST" class="bg-white rounded-lg shadow p-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div class="md:col-span-2">
                <label class="block text-gray-700 font-semibold mb-2">Nome do Endereço</label>
                <input type="text" name="nome_endereco" placeholder="Casa, Trabalho, etc" class="w-full border rounded-lg px-4 py-2" required>
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">CEP</label>
                <input type="text" name="cep" id="cep" placeholder="00000-000" class="w-full border rounded-lg px-4 py-2" required>
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Número</label>
                <input type="text" name="numero" placeholder="123" class="w-full border rounded-lg px-4 py-2" required>
            </div>

            <div class="md:col-span-2">
                <label class="block text-gray-700 font-semibold mb-2">Logradouro</label>
                <input type="text" name="logradouro" id="logradouro" placeholder="Rua, Avenida..." class="w-full border rounded-lg px-4 py-2" required>
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Bairro</label>
                <input type="text" name="bairro" id="bairro" class="w-full border rounded-lg px-4 py-2" required>
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Complemento</label>
                <input type="text" name="complemento" placeholder="Apto, Bloco..." class="w-full border rounded-lg px-4 py-2">
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Cidade</label>
                <input type="text" name="cidade" id="cidade" class="w-full border rounded-lg px-4 py-2" required>
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Estado</label>
                <input type="text" name="estado" id="estado" maxlength="2" placeholder="SP" class="w-full border rounded-lg px-4 py-2" required>
            </div>

            <div class="md:col-span-2">
                <label class="block text-gray-700 font-semibold mb-2">Ponto de Referência</label>
                <textarea name="referencia" rows="2" class="w-full border rounded-lg px-4 py-2" placeholder="Próximo ao mercado..."></textarea>
            </div>

            <div class="md:col-span-2">
                <label class="flex items-center">
                    <input type="checkbox" name="padrao" value="1" class="mr-2">
                    <span class="text-gray-700">Marcar como endereço padrão</span>
                </label>
            </div>
        </div>

        <div class="flex gap-4">
            <button type="submit" class="flex-1 bg-red-600 text-white py-3 rounded-lg hover:bg-red-700 font-semibold">
                Salvar Endereço
            </button>
            <a href="{{ route('cliente.enderecos.index') }}" class="flex-1 bg-gray-300 text-gray-700 py-3 rounded-lg hover:bg-gray-400 font-semibold text-center">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
