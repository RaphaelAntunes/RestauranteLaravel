<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'tipo',
        'descricao',
        'ordem',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
            'ordem' => 'integer',
        ];
    }

    public function produtos(): HasMany
    {
        return $this->hasMany(Produto::class);
    }

    public function produtosAtivos(): HasMany
    {
        return $this->hasMany(Produto::class)->where('ativo', true);
    }

    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeOrdenadas($query)
    {
        return $query->orderBy('ordem');
    }

    public function scopeComidas($query)
    {
        return $query->where('tipo', 'comida');
    }

    public function scopeBebidas($query)
    {
        return $query->where('tipo', 'bebida');
    }

    public function isComida(): bool
    {
        return $this->tipo === 'comida';
    }

    public function isBebida(): bool
    {
        return $this->tipo === 'bebida';
    }
}
