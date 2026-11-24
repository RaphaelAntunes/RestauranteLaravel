<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LojaBanner;
use App\Support\LojaContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class LojaPerfilController extends Controller
{
    private const IMAGE_TYPES = 'mimetypes:image/jpeg,image/png,image/gif,image/webp,image/avif,image/bmp,image/x-ms-bmp';

    public function edit()
    {
        $loja = app(LojaContext::class)->loja()->load('banners');

        return view('admin.loja.edit', compact('loja'));
    }

    public function update(Request $request)
    {
        $loja = app(LojaContext::class)->loja();
        $data = $request->validate([
            'nome' => ['required', 'string', 'max:120'],
            'slug' => [
                'required',
                'alpha_dash:ascii',
                'max:120',
                Rule::unique('lojas', 'slug')->ignore($loja->id),
            ],
            'descricao' => ['nullable', 'string', 'max:1000'],
            'titulo_cardapio' => ['required', 'string', 'max:120'],
            'telefone' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+().\s-]+$/'],
            'whatsapp_ativo' => ['nullable', 'boolean'],
            'endereco' => ['nullable', 'string', 'max:500'],
            'cor_primaria' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'cor_secundaria' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'logo' => ['nullable', 'file', self::IMAGE_TYPES, 'max:4096'],
            'banner' => ['nullable', 'file', self::IMAGE_TYPES, 'max:8192'],
            'banner_mobile' => ['nullable', 'file', self::IMAGE_TYPES, 'max:8192'],
            'remover_banner' => ['nullable', 'boolean'],
            'remover_banner_mobile' => ['nullable', 'boolean'],
            'exibir_textos_banner' => ['nullable', 'boolean'],
        ], $this->imageValidationMessages());

        $data['whatsapp_ativo'] = $request->boolean('whatsapp_ativo');
        $data['exibir_textos_banner'] = $request->boolean('exibir_textos_banner');

        foreach (['logo', 'banner', 'banner_mobile'] as $field) {
            $column = $field.'_path';
            if ($request->hasFile($field)) {
                if ($loja->{$column}) {
                    Storage::disk('public')->delete($loja->{$column});
                }
                $data[$column] = $request->file($field)->store("lojas/{$loja->id}/identidade", 'public');
            } elseif ($field !== 'logo' && $request->boolean('remover_'.$field)) {
                if ($loja->{$column}) {
                    Storage::disk('public')->delete($loja->{$column});
                }
                $data[$column] = null;
            }
            unset($data[$field]);
            unset($data['remover_'.$field]);
        }

        $loja->update($data);

        return redirect()->route('admin.loja.edit', ['loja' => $loja->fresh()->slug])
            ->with('success', 'Identidade e endereço público da loja atualizados.');
    }

    public function storeBanner(Request $request)
    {
        $loja = app(LojaContext::class)->loja();
        $data = $request->validate([
            'titulo' => ['nullable', 'string', 'max:120'],
            'subtitulo' => ['nullable', 'string', 'max:220'],
            'link' => ['nullable', 'url', 'max:255'],
            'imagem' => ['required', 'file', self::IMAGE_TYPES, 'max:8192'],
        ], $this->imageValidationMessages());
        $data['imagem_path'] = $request->file('imagem')->store("lojas/{$loja->id}/banners", 'public');
        $data['ordem'] = ((int) $loja->banners()->max('ordem')) + 1;
        unset($data['imagem']);
        LojaBanner::create($data);

        return back()->with('success', 'Banner adicionado.');
    }

    public function destroyBanner(LojaBanner $banner)
    {
        Storage::disk('public')->delete($banner->imagem_path);
        $banner->delete();

        return back()->with('success', 'Banner removido.');
    }

    private function imageValidationMessages(): array
    {
        return [
            'logo.mimetypes' => 'O logotipo deve estar em JPEG, PNG, WebP, GIF, BMP ou AVIF.',
            'logo.max' => 'O logotipo deve ter no máximo 4 MB.',
            'banner.mimetypes' => 'A imagem principal deve estar em JPEG, PNG, WebP, GIF, BMP ou AVIF.',
            'banner.max' => 'A imagem principal deve ter no máximo 8 MB.',
            'banner_mobile.mimetypes' => 'O banner para celular deve estar em JPEG, PNG, WebP, GIF, BMP ou AVIF.',
            'banner_mobile.max' => 'O banner para celular deve ter no máximo 8 MB.',
            'imagem.required' => 'Selecione uma imagem para o banner.',
            'imagem.mimetypes' => 'O banner deve estar em JPEG, PNG, WebP, GIF, BMP ou AVIF.',
            'imagem.max' => 'O banner deve ter no máximo 8 MB.',
        ];
    }
}
