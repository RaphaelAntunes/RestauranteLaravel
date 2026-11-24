<?php

namespace App\Models;

use App\Models\Concerns\BelongsToLoja;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PagamentoDetalhe extends Model
{
    use HasFactory, BelongsToLoja;

    public $timestamps = false;

    protected $fillable = [
        'loja_id',
        'pagamento_id',
        'pedido_id',
        'metodo',
        'valor',
    ];

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'created_at' => 'datetime',
        ];
    }

    public function pagamento(): BelongsTo
    {
        return $this->belongsTo(Pagamento::class);
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    public function getValorFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->valor, 2, ',', '.');
    }
}
