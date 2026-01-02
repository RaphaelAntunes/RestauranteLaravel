<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sabor extends Model
{
    use HasFactory;

    protected $table = 'sabores';

    protected $fillable = [
        'categoria_id',
        'nome',
        'descricao',
        'ingredientes',
        'preco_p',
        'preco_m',
        'preco_g',
        'preco_gg',
        'imagem',
        'ativo',
        'ordem',
    ];

    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
            'ordem' => 'integer',
            'preco_p' => 'decimal:2',
            'preco_m' => 'decimal:2',
            'preco_g' => 'decimal:2',
            'preco_gg' => 'decimal:2',
        ];
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }
}
