<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarrinhoItemSabor extends Model
{
    use HasFactory;

    protected $table = 'carrinho_item_sabores';

    protected $fillable = [
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
