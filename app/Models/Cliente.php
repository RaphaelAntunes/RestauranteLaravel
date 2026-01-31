<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Cliente extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
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
