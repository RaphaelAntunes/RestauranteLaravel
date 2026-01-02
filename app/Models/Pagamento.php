<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pagamento extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'pedido_id',
        'user_id',
        'mesa_id',
        'valor_total',
        'subtotal',
        'total',
        'metodo_pagamento',
        'forma_pagamento',
        'valor_pago',
        'troco',
        'desconto',
        'valor_desconto',
        'acrescimo',
        'valor_acrescimo',
        'status',
        'observacoes',
        'data_pagamento',
    ];

    protected function casts(): array
    {
        return [
            'valor_total' => 'decimal:2',
            'valor_pago' => 'decimal:2',
            'troco' => 'decimal:2',
            'desconto' => 'decimal:2',
            'valor_desconto' => 'decimal:2',
            'acrescimo' => 'decimal:2',
            'valor_acrescimo' => 'decimal:2',
            'data_pagamento' => 'datetime',
        ];
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mesa(): BelongsTo
    {
        return $this->belongsTo(Mesa::class);
    }

    public function detalhes(): HasMany
    {
        return $this->hasMany(PagamentoDetalhe::class);
    }

    public function isMetodoMultiplo(): bool
    {
        return $this->metodo_pagamento === 'multiplo';
    }

    public function getValorTotalFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->valor_total, 2, ',', '.');
    }

    public function getTrocoFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->troco, 2, ',', '.');
    }
}
