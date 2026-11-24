<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Loja extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'slug',
        'descricao',
        'titulo_cardapio',
        'logo_path',
        'banner_path',
        'banner_mobile_path',
        'exibir_textos_banner',
        'cor_primaria',
        'cor_secundaria',
        'telefone',
        'whatsapp',
        'whatsapp_ativo',
        'endereco',
        'status',
        'plano',
        'trial_ends_at',
    ];

    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
            'whatsapp_ativo' => 'boolean',
            'exibir_textos_banner' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Loja $loja): void {
            $loja->slug = Str::slug($loja->slug ?: $loja->nome);
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function categorias(): HasMany
    {
        return $this->hasMany(Categoria::class);
    }

    public function produtos(): HasMany
    {
        return $this->hasMany(Produto::class);
    }

    public function banners(): HasMany
    {
        return $this->hasMany(LojaBanner::class)->orderBy('ordem');
    }

    public function ativa(): bool
    {
        return $this->status === 'ativa';
    }

    public function whatsappLink(): ?string
    {
        if (!$this->whatsapp_ativo || !$this->whatsapp) {
            return null;
        }

        $numero = preg_replace('/\D+/', '', $this->whatsapp);
        if (strlen($numero) === 10 || strlen($numero) === 11) {
            $numero = '55'.$numero;
        }

        if (strlen($numero) < 10) {
            return null;
        }

        $mensagem = rawurlencode("Olá! Vim pelo cardápio da {$this->nome}.");

        return "https://wa.me/{$numero}?text={$mensagem}";
    }
}
