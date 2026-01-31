<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produto extends Model
{
    use HasFactory;

    protected $fillable = [
        'categoria_id',
        'nome',
        'descricao',
        'preco',
        'imagem',
        'tempo_preparo',
        'ativo',
        'destaque',
        'ordem',
    ];

    protected function casts(): array
    {
        return [
            'preco' => 'decimal:2',
            'tempo_preparo' => 'integer',
            'ativo' => 'boolean',
            'destaque' => 'boolean',
            'ordem' => 'integer',
        ];
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function pedidoItens(): HasMany
    {
        return $this->hasMany(PedidoItem::class);
    }

    public function tamanhos(): HasMany
    {
        return $this->hasMany(ProdutoTamanho::class)->orderBy('ordem');
    }

    public function temTamanhos(): bool
    {
        return $this->tamanhos()->where('ativo', true)->count() > 0;
    }

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeDestaques($query)
    {
        return $query->where('destaque', true);
    }

    public function getPrecoFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->preco, 2, ',', '.');
    }
}
