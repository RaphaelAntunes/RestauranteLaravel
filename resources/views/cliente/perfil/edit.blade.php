@extends('layouts.cliente')

@section('title', 'Meu perfil')

@section('content')
<div class="customer-page customer-profile">
    <header class="customer-page-heading">
        <div>
            <p class="customer-kicker">Sua conta</p>
            <h1>Meu perfil</h1>
            <p>Atualize seus dados e mantenha seus endereços de entrega organizados.</p>
        </div>
        <a class="customer-button customer-button-secondary" href="{{ route('cliente.enderecos.index') }}">Meus endereços</a>
    </header>

    <form action="{{ route('cliente.perfil.update') }}" method="POST" class="customer-panel customer-form">
        @csrf
        @method('PUT')

        <div class="customer-form-grid">
            <div class="customer-field customer-field-wide">
                <label for="nome">Nome completo</label>
                <input id="nome" name="nome" type="text" value="{{ old('nome', $cliente->nome) }}" maxlength="100" autocomplete="name" required>
                @error('nome') <p class="customer-field-error">{{ $message }}</p> @enderror
            </div>

            <div class="customer-field">
                <label for="celular">Celular usado para entrar</label>
                <input id="celular" type="tel" value="{{ session('demo_cliente_celular', $cliente->celular) }}" readonly aria-describedby="celular-ajuda">
                <p id="celular-ajuda" class="customer-field-help">{{ session()->has('demo_cliente_celular') ? 'Número informado para esta sessão de demonstração, sem verificação.' : 'O celular é confirmado por código de acesso.' }}</p>
            </div>

            <div class="customer-field">
                <label for="email">E-mail <span>(opcional)</span></label>
                <input id="email" name="email" type="email" value="{{ old('email', $cliente->email) }}" maxlength="100" autocomplete="email">
                @error('email') <p class="customer-field-error">{{ $message }}</p> @enderror
            </div>

            <div class="customer-field">
                <label for="cpf">CPF <span>(opcional)</span></label>
                <input id="cpf" name="cpf" type="text" value="{{ old('cpf', $cliente->cpf) }}" inputmode="numeric" maxlength="14" autocomplete="off" placeholder="000.000.000-00">
                @error('cpf') <p class="customer-field-error">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="customer-form-actions">
            <button type="submit" class="customer-button customer-button-primary">Salvar meus dados</button>
        </div>
    </form>

    <section class="customer-panel customer-address-shortcut">
        <div>
            <h2>Endereços salvos</h2>
            <p>{{ $cliente->enderecos()->count() }} {{ $cliente->enderecos()->count() === 1 ? 'endereço cadastrado' : 'endereços cadastrados' }}</p>
        </div>
        <a class="customer-button customer-button-secondary" href="{{ route('cliente.enderecos.index') }}">Gerenciar endereços</a>
    </section>
</div>
@endsection
