<?php

namespace App\Models;

use App\Models\Concerns\BelongsToLoja;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LojaBanner extends Model
{
    use HasFactory, BelongsToLoja;

    protected $fillable = [
        'loja_id',
        'titulo',
        'subtitulo',
        'imagem_path',
        'link',
        'ordem',
        'ativo',
    ];

    protected function casts(): array
    {
        return ['ativo' => 'boolean', 'ordem' => 'integer'];
    }
}
