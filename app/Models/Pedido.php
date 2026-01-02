<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'mesa_id',
        'user_id',
        'numero_pedido',
        'status',
        'total',
        'observacoes',
        'data_abertura',
        'data_finalizacao',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'data_abertura' => 'datetime',
            'data_finalizacao' => 'datetime',
        ];
    }

    public function mesa(): BelongsTo
    {
        return $this->belongsTo(Mesa::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function itens(): HasMany
    {
        return $this->hasMany(PedidoItem::class);
    }

    public function pagamento(): HasOne
    {
        return $this->hasOne(Pagamento::class);
    }

    public function scopeAbertos($query)
    {
        return $query->where('status', 'aberto');
    }

    public function scopeEmPreparo($query)
    {
        return $query->where('status', 'em_preparo');
    }

    public function scopeProntos($query)
    {
        return $query->where('status', 'pronto');
    }

    public function scopeFinalizados($query)
    {
        return $query->where('status', 'finalizado');
    }

    public function calcularTotal(): void
    {
        $this->total = $this->itens()->sum('subtotal');
        $this->save();
    }

    public function isAberto(): bool
    {
        return $this->status === 'aberto';
    }

    public function isFinalizado(): bool
    {
        return $this->status === 'finalizado';
    }

    public function isCancelado(): bool
    {
        return $this->status === 'cancelado';
    }

    public function getTotalFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->total, 2, ',', '.');
    }
}
