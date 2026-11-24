@extends('layouts.cliente')

@section('title', 'Novo endereço')

@section('content')
<div class="customer-page customer-form-page">
    <header class="customer-page-heading">
        <div>
            <p class="customer-kicker">Endereços salvos</p>
            <h1>Novo endereço</h1>
            <p>Cadastre onde você quer receber seus pedidos.</p>
        </div>
    </header>

    @include('cliente.enderecos._form')
</div>
@endsection
