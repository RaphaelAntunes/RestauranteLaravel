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
        'sessao_id',
        'user_id',
        'cliente_id',
        'tipo_pedido',
        'cliente_endereco_id',
        'numero_pedido',
        'status',
        'total',
        'taxa_entrega',
        'observacoes',
        'observacoes_entrega',
        'data_abertura',
        'em_preparo_at',
        'pronto_at',
        'saiu_entrega_at',
        'entregue_at',
        'data_finalizacao',
        'previsao_entrega',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'taxa_entrega' => 'decimal:2',
            'data_abertura' => 'datetime',
            'em_preparo_at' => 'datetime',
            'pronto_at' => 'datetime',
            'saiu_entrega_at' => 'datetime',
            'entregue_at' => 'datetime',
            'data_finalizacao' => 'datetime',
            'previsao_entrega' => 'datetime',
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

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function clienteEndereco(): BelongsTo
    {
        return $this->belongsTo(ClienteEndereco::class);
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

    public function isOnline(): bool
    {
        return $this->tipo_pedido !== 'mesa';
    }

    public function isDelivery(): bool
    {
        return $this->tipo_pedido === 'delivery';
    }

    public function isRetirada(): bool
    {
        return $this->tipo_pedido === 'retirada';
    }

    public function getTotalComTaxa(): float
    {
        return $this->total + $this->taxa_entrega;
    }

    public function scopeOnline($query)
    {
        return $query->whereIn('tipo_pedido', ['delivery', 'retirada']);
    }

    public function scopeDelivery($query)
    {
        return $query->where('tipo_pedido', 'delivery');
    }
}
