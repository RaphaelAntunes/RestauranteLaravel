<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mesa extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero',
        'capacidade',
        'status',
        'localizacao',
        'cliente_nome',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
            'numero' => 'integer',
            'capacidade' => 'integer',
        ];
    }

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class);
    }

    public function pedidoAtivo()
    {
        return $this->hasOne(Pedido::class)
            ->whereIn('status', ['aberto', 'em_preparo', 'pronto', 'entregue'])
            ->latest();
    }

    public function isDisponivel(): bool
    {
        return $this->status === 'disponivel';
    }

    public function isOcupada(): bool
    {
        return $this->status === 'ocupada';
    }

    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeDisponiveis($query)
    {
        return $query->where('status', 'disponivel');
    }
}
