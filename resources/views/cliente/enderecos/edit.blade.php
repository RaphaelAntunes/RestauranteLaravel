@extends('layouts.cliente')

@section('title', 'Editar endereço')

@section('content')
<div class="customer-page customer-form-page">
    <header class="customer-page-heading">
        <div>
            <p class="customer-kicker">Endereço salvo</p>
            <h1>Editar endereço</h1>
            <p>Altere os dados deste local de entrega.</p>
        </div>
    </header>

    @include('cliente.enderecos._form', ['endereco' => $endereco])
</div>
@endsection
