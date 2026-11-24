<?php

namespace App\Models;

use App\Models\Concerns\BelongsToLoja;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PedidoItemSabor extends Model
{
    use HasFactory, BelongsToLoja;

    protected $table = 'pedido_item_sabores';

    protected $fillable = [
        'loja_id',
        'pedido_item_id',
        'sabor_id',
    ];

    public function pedidoItem(): BelongsTo
    {
        return $this->belongsTo(PedidoItem::class);
    }

    public function sabor(): BelongsTo
    {
        return $this->belongsTo(Sabor::class);
    }
}
