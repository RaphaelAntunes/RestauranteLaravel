@extends('layouts.app')
@section('title', 'Minha loja')
@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <section class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6">
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div><h2 class="text-2xl font-bold">Identidade da loja</h2><p class="text-gray-500">Nome, cores e imagens exibidos no cardápio.</p></div>
            <a class="px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-600" target="_blank" href="{{ route('cliente.cardapio') }}">Ver cardápio</a>
        </header>
        @if(session('success'))<div class="mb-5 p-4 rounded-xl bg-green-100 text-green-800">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="mb-5 p-4 rounded-xl bg-red-100 text-red-800"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <form method="POST" enctype="multipart/form-data" action="{{ route('admin.loja.update') }}" class="grid md:grid-cols-2 gap-5">
            @csrf @method('PUT')
            <label class="grid gap-2"><span class="font-semibold">Nome</span><input class="rounded-xl border-gray-300" name="nome" value="{{ old('nome', $loja->nome) }}" required></label>
            <label class="grid gap-2"><span class="font-semibold">Identificador da URL</span><input class="rounded-xl border-gray-300" name="slug" value="{{ old('slug', $loja->slug) }}" pattern="[a-z0-9-]+" required><small class="text-gray-500">antuneslab.tech/restaurantes/<strong>{{ $loja->slug }}</strong></small></label>
            <div class="md:col-span-2 grid md:grid-cols-[minmax(0,1fr)_auto] gap-4 items-end rounded-2xl border border-green-200 bg-green-50/70 p-4">
                <label class="grid gap-2"><span class="font-semibold">WhatsApp do cardápio</span><input class="rounded-xl border-gray-300" name="whatsapp" inputmode="tel" value="{{ old('whatsapp', $loja->whatsapp) }}" placeholder="(11) 99999-9999"><small class="text-gray-500">Use DDD e número. O código do Brasil é adicionado automaticamente.</small></label>
                <label class="flex items-center gap-3 min-h-12 px-4 rounded-xl bg-white border border-green-200 font-semibold"><input type="checkbox" name="whatsapp_ativo" value="1" @checked(old('whatsapp_ativo', $loja->whatsapp_ativo))><span>Exibir no cardápio</span></label>
            </div>
            <label class="grid gap-2"><span class="font-semibold">Telefone</span><input class="rounded-xl border-gray-300" name="telefone" value="{{ old('telefone', $loja->telefone) }}"></label>
            <label class="grid gap-2"><span class="font-semibold">Endereço</span><input class="rounded-xl border-gray-300" name="endereco" value="{{ old('endereco', $loja->endereco) }}"></label>
            <label class="grid gap-2"><span class="font-semibold">Título do banner</span><input class="rounded-xl border-gray-300" name="titulo_cardapio" maxlength="120" value="{{ old('titulo_cardapio', $loja->titulo_cardapio ?: 'Cardápio da casa') }}" required><small class="text-gray-500">Exibido em destaque abaixo do nome da loja.</small></label>
            <label class="grid gap-2"><span class="font-semibold">Texto de apoio do banner</span><textarea class="rounded-xl border-gray-300" name="descricao" rows="3" placeholder="Escolha seus favoritos e faça seu pedido.">{{ old('descricao', $loja->descricao) }}</textarea></label>
            <label class="grid gap-2"><span class="font-semibold">Cor principal</span><input type="color" class="h-12 w-full rounded-xl border-gray-300" name="cor_primaria" value="{{ old('cor_primaria', $loja->cor_primaria) }}"></label>
            <label class="grid gap-2"><span class="font-semibold">Cor de apoio</span><input type="color" class="h-12 w-full rounded-xl border-gray-300" name="cor_secundaria" value="{{ old('cor_secundaria', $loja->cor_secundaria) }}"></label>
            <label class="grid gap-2"><span class="font-semibold">Logotipo</span><input type="file" name="logo" accept=".jpg,.jpeg,.png,.webp,.gif,.bmp,.avif"><small class="text-gray-500">JPEG, PNG, WebP, GIF, BMP ou AVIF — até 4 MB.</small></label>
            <div class="grid gap-2">
                <span class="font-semibold">Banner para computador</span>
                @if($loja->banner_path)<img class="w-full h-28 rounded-xl object-cover border border-gray-300" src="{{ asset('storage/'.$loja->banner_path) }}" alt="Banner atual para computador">@endif
                <input type="file" name="banner" accept=".jpg,.jpeg,.png,.webp,.gif,.bmp,.avif">
                <small class="text-gray-500">Recomendado: 1920 × 450 px. Se não enviar, será usado o banner padrão.</small>
                @if($loja->banner_path)<label class="flex items-center gap-2 text-sm text-red-700"><input type="checkbox" name="remover_banner" value="1"><span>Remover e voltar ao banner padrão</span></label>@endif
            </div>
            <div class="grid gap-2">
                <span class="font-semibold">Banner para celular</span>
                @if($loja->banner_mobile_path)<img class="w-full h-28 rounded-xl object-cover border border-gray-300" src="{{ asset('storage/'.$loja->banner_mobile_path) }}" alt="Banner atual para celular">@endif
                <input type="file" name="banner_mobile" accept=".jpg,.jpeg,.png,.webp,.gif,.bmp,.avif">
                <small class="text-gray-500">Recomendado: 1080 × 608 px. Se não enviar, o banner do computador será adaptado.</small>
                @if($loja->banner_mobile_path)<label class="flex items-center gap-2 text-sm text-red-700"><input type="checkbox" name="remover_banner_mobile" value="1"><span>Remover e usar o banner do computador</span></label>@endif
            </div>
            <label class="md:col-span-2 flex items-start gap-3 rounded-xl border border-gray-300 p-4 bg-gray-50 dark:bg-gray-900"><input class="mt-1" type="checkbox" name="exibir_textos_banner" value="1" @checked(old('exibir_textos_banner', $loja->exibir_textos_banner))><span><strong class="block">Exibir nome e chamada sobre o banner</strong><small class="text-gray-500">Desative quando sua arte já tiver esses textos. Status da loja e tempo de preparo continuam visíveis para o cliente.</small></span></label>
            <div class="md:col-span-2"><button class="px-5 py-3 rounded-xl text-white font-bold" style="background:{{ $loja->cor_primaria }}">Salvar identidade</button></div>
        </form>
    </section>
</div>
@endsection
