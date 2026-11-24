@extends('layouts.platform')
@section('title', 'Lojas | Antunes Lab')
@section('content')
<main class="pl-main">
    <header class="pl-head">
        <div><h1>Lojas da plataforma</h1><p>Cadastre uma operação e entregue ao proprietário um painel separado, com cardápio, equipe e pedidos próprios.</p></div>
        <a class="pl-btn" href="{{ route('platform.lojas.create') }}">Cadastrar loja</a>
    </header>
    @if(session('success'))<div class="pl-flash">{{ session('success') }}</div>@endif
    <div class="pl-grid">
        @forelse($lojas as $loja)
            <article class="pl-card">
                <div style="display:flex;justify-content:space-between;gap:12px">
                    <div><h2>{{ $loja->nome }}</h2><p>/restaurantes/{{ $loja->slug }}</p></div>
                    <span class="pl-status {{ $loja->status }}">{{ ucfirst($loja->status) }}</span>
                </div>
                <div class="pl-meta"><span>{{ $loja->users_count }} usuários</span><span>{{ $loja->produtos_count }} produtos</span><span>{{ ucfirst($loja->plano) }}</span></div>
                <div class="pl-actions">
                    <a class="pl-btn secondary" target="_blank" href="{{ route('cliente.cardapio', ['loja' => $loja]) }}">Abrir cardápio</a>
                    <a class="pl-btn secondary" target="_blank" href="{{ route('login', ['loja' => $loja]) }}">Painel da loja</a>
                    <form method="POST" action="{{ route('platform.lojas.status', ['loja' => $loja]) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="{{ $loja->status === 'ativa' ? 'suspensa' : 'ativa' }}">
                        <button class="pl-btn secondary" type="submit">{{ $loja->status === 'ativa' ? 'Suspender' : 'Reativar' }}</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="pl-card"><h2>Nenhuma loja cadastrada</h2><p>Cadastre a primeira operação para começar.</p></div>
        @endforelse
    </div>
</main>
@endsection
