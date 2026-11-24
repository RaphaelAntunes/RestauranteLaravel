<!DOCTYPE html>
<html lang="pt-BR"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}"><link rel="icon" href="{{ isset($lojaAtual) && $lojaAtual->logo_path ? asset('storage/'.$lojaAtual->logo_path) : asset('dobra/mark.svg') }}">
<title>@yield('title', 'Pedidos') | {{ $lojaAtual->nome ?? 'Restaurante' }}</title>
@vite(['resources/css/app.css','resources/js/app.js'])
@stack('styles')
@isset($lojaAtual)<style>:root{--loja-primary:{{ $lojaAtual->cor_primaria }};--loja-secondary:{{ $lojaAtual->cor_secundaria }} }.db-add,.db-status,.db-bag-link{background-color:var(--loja-primary)!important}.db-dot{color:var(--loja-primary)!important}</style>@endisset
</head>
<body class="{{ request()->routeIs('cliente.cardapio') ? 'customer-menu' : 'customer-app' }}">
<a class="db-skip" href="#conteudo">Ir para o conteúdo</a>
@if(!request()->routeIs('cliente.cardapio'))
<nav class="db-bar"><div class="db-wrap db-row"><a href="{{ route('cliente.cardapio') }}" aria-label="{{ $lojaAtual->nome ?? 'Restaurante' }}, início do cardápio"><x-dobra-brand/></a>
<div class="db-actions"><a class="db-nav-link" href="{{ route('cliente.cardapio') }}"><x-dobra-icon name="menu"/>Cardápio</a>
@auth('cliente')
<details class="db-account"><summary><x-dobra-icon name="person"/>Conta</summary><div>
<a href="{{ route('cliente.carrinho.index') }}">Sacola</a><a href="{{ route('cliente.pedidos') }}">Meus pedidos</a>
<a href="{{ route('cliente.perfil.edit') }}">Meu perfil</a><a href="{{ route('cliente.enderecos.index') }}">Endereços</a>
<form method="POST" action="{{ route('cliente.logout') }}">@csrf<button>Sair</button></form></div></details>
@else
@if(!request()->routeIs('cliente.login','cliente.otp.*'))<a class="db-nav-link" href="{{ route('cliente.login') }}">Entrar</a>@endif
@endauth
</div></div></nav>@endif
@foreach(['success','error'] as $tipo)
@if(session($tipo))<p class="db-notice db-notice-{{ $tipo }}" role="status">{{ session($tipo) }}</p>@endif
@endforeach
<main id="conteudo">@yield('content')</main>
<footer class="db-footer"><div class="db-wrap db-row"><x-dobra-brand/><a href="{{ route('cliente.cardapio') }}">Voltar ao cardápio</a></div></footer>
@stack('scripts')
</body></html>
