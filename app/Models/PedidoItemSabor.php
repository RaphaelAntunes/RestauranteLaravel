<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PedidoItemSabor extends Model
{
    use HasFactory;

    protected $table = 'pedido_item_sabores';

    protected $fillable = [
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
