<?php

namespace App\Models;

use App\Models\Concerns\BelongsToLoja;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarrinhoItemSabor extends Model
{
    use HasFactory, BelongsToLoja;

    protected $table = 'carrinho_item_sabores';

    protected $fillable = [
        'loja_id',
        'carrinho_item_id',
        'sabor_id',
    ];

    public function carrinhoItem()
    {
        return $this->belongsTo(CarrinhoItem::class);
    }

    public function sabor()
    {
        return $this->belongsTo(Sabor::class);
    }
}
