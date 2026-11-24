<?php

namespace App\Models;

use App\Models\Concerns\BelongsToLoja;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Cliente extends Authenticatable
{
    use HasFactory, BelongsToLoja;

    protected $fillable = [
        'loja_id',
        'nome',
        'celular',
        'email',
        'cpf',
        'ativo',
        'ultimo_acesso',
    ];

    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
            'ultimo_acesso' => 'datetime',
        ];
    }

    public function enderecos()
    {
        return $this->hasMany(ClienteEndereco::class);
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    public function carrinhoItens()
    {
        return $this->hasMany(CarrinhoItem::class);
    }

    public function enderecoPadrao()
    {
        return $this->enderecos()->where('padrao', true)->first();
    }

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }
}
