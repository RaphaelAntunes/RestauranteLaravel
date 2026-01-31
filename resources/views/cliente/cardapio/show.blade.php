@extends('layouts.cliente')

@section('title', $produto->nome)

@section('content')
<div class="max-w-4xl mx-auto">
    <a href="{{ route('cliente.cardapio') }}" class="text-red-600 hover:underline mb-4 inline-block">
        ← Voltar ao Cardápio
    </a>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($produto->imagem)
            <img src="{{ asset('storage/' . $produto->imagem) }}" alt="{{ $produto->nome }}" class="w-full h-64 object-cover">
        @endif

        <form action="{{ route('cliente.carrinho.adicionar.publico') }}" method="POST" class="p-6">
            @csrf
            <input type="hidden" name="produto_id" value="{{ $produto->id }}">

            <h1 class="text-3xl font-bold mb-4">{{ $produto->nome }}</h1>
            <p class="text-gray-600 mb-6">{{ $produto->descricao }}</p>

            @if($produto->tamanhos->count() > 0)
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-3">Escolha o Tamanho</label>
                    @foreach($produto->tamanhos as $tamanho)
                        <label class="flex items-center justify-between p-4 border rounded-lg mb-2 cursor-pointer hover:bg-gray-50">
                            <div class="flex items-center">
                                <input type="radio" name="produto_tamanho_id" value="{{ $tamanho->id }}" class="mr-3" required>
                                <span class="font-semibold">{{ $tamanho->nome }}</span>
                                <span class="text-gray-600 text-sm ml-2">{{ $tamanho->descricao }}</span>
                            </div>
                            <span class="font-bold text-red-600">R$ {{ number_format($tamanho->preco, 2, ',', '.') }}</span>
                        </label>
                    @endforeach
                </div>

                @if($sabores->count() > 0)
                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-3">Escolha até 4 Sabores</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($sabores as $sabor)
                                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="checkbox" name="sabores[]" value="{{ $sabor->id }}" class="mr-3 sabor-checkbox">
                                    <span>{{ $sabor->nome }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif

            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-3">Quantidade</label>
                <input type="number" name="quantidade" value="1" min="1" max="10" class="border rounded-lg px-4 py-2 w-32">
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-3">Observações</label>
                <textarea name="observacoes" rows="3" class="w-full border rounded-lg p-3" placeholder="Sem cebola, bem passado, etc..."></textarea>
            </div>

            <button type="submit" class="w-full bg-red-600 text-white py-4 rounded-lg hover:bg-red-700 font-semibold text-lg">
                Adicionar ao Carrinho
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const checkboxes = document.querySelectorAll('.sabor-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checked = document.querySelectorAll('.sabor-checkbox:checked');
            if (checked.length >= 4) {
                checkboxes.forEach(cb => {
                    if (!cb.checked) cb.disabled = true;
                });
            } else {
                checkboxes.forEach(cb => cb.disabled = false);
            }
        });
    });
</script>
@endpush
@endsection
