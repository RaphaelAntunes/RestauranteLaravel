@extends('layouts.cliente')

@section('title', 'Meus endereços')

@section('content')
<div class="customer-page">
    <header class="customer-page-heading">
        <div>
            <p class="customer-kicker">Entrega sem redigitar</p>
            <h1>Meus endereços</h1>
            <p>Salve locais de entrega e escolha um endereço padrão para seus próximos pedidos.</p>
        </div>
        <a href="{{ route('cliente.enderecos.create') }}" class="customer-button customer-button-primary">Adicionar endereço</a>
    </header>

    @if($enderecos->isEmpty())
        <section class="customer-panel customer-empty-state">
            <span class="customer-empty-icon" aria-hidden="true">⌂</span>
            <h2>Nenhum endereço cadastrado</h2>
            <p>Adicione seu primeiro endereço para agilizar o checkout do delivery.</p>
            <a href="{{ route('cliente.enderecos.create') }}" class="customer-button customer-button-primary">Cadastrar endereço</a>
        </section>
    @else
        <div class="customer-address-list">
            @foreach($enderecos as $endereco)
                <article class="customer-panel customer-address-card">
                    <div class="customer-address-copy">
                        <div class="customer-address-title">
                            <h2>{{ $endereco->nome_endereco }}</h2>
                            @if($endereco->padrao)<span class="customer-badge">Padrão</span>@endif
                        </div>
                        <p>{{ $endereco->logradouro }}, {{ $endereco->numero }}@if($endereco->complemento) — {{ $endereco->complemento }}@endif</p>
                        <p>{{ $endereco->bairro }}, {{ $endereco->cidade }}/{{ $endereco->estado }} · CEP {{ $endereco->cep }}</p>
                        @if($endereco->referencia)<p class="customer-address-reference">Referência: {{ $endereco->referencia }}</p>@endif
                    </div>
                    <div class="customer-address-actions">
                        <a href="{{ route('cliente.enderecos.edit', ['endereco' => $endereco]) }}" class="customer-button customer-button-secondary">Editar</a>
                        @unless($endereco->padrao)
                            <form action="{{ route('cliente.enderecos.padrao', ['endereco' => $endereco]) }}" method="POST">
                                @csrf
                                <button class="customer-button customer-button-quiet" type="submit">Tornar padrão</button>
                            </form>
                            <form action="{{ route('cliente.enderecos.destroy', ['endereco' => $endereco]) }}" method="POST" onsubmit="return confirm('Excluir este endereço?')">
                                @csrf
                                @method('DELETE')
                                <button class="customer-button customer-button-danger" type="submit">Excluir</button>
                            </form>
                        @endunless
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</div>
@endsection
