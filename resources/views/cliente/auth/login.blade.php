@extends('layouts.cliente')
@section('title', 'Entrar no cardápio')
@section('content')
<section class="db-entry"><x-dobra-icon name="bag"/><h1>Entre para pedir</h1>
<p>{{ $demo ? 'Neste teste, basta informar seu celular. Você entra sem senha e sem código.' : 'Informe seu celular para iniciar a verificação.' }}</p>
<form method="POST" action="{{ route('cliente.otp.enviar') }}">@csrf
<label for="celular">Celular com DDD</label>
<input type="tel" name="celular" id="celular" autocomplete="tel" inputmode="tel" maxlength="20" placeholder="(84) 99999-0000" value="{{ old('celular') }}" required @error('celular') aria-invalid="true" aria-describedby="erro-celular" @enderror>
@error('celular')<p id="erro-celular" role="alert">{{ $message }}</p>@enderror
<button class="db-primary">{{ $demo ? 'Acessar meus pedidos' : 'Enviar Código' }}</button>
</form>@if($demo)<small>Seu pedido fica nesta sessão. Não abrimos o histórico de outra pessoa pelo número informado.</small>@endif
</section>
@endsection
