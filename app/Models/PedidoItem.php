<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PedidoItem extends Model
{
    use HasFactory;

    protected $table = 'pedido_itens';

    protected $fillable = [
        'pedido_id',
        'produto_id',
        'produto_nome',
        'produto_tamanho_id',
        'quantidade',
        'preco_unitario',
        'subtotal',
        'observacoes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'quantidade' => 'integer',
            'preco_unitario' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->subtotal = $item->quantidade * $item->preco_unitario;
        });

        static::saved(function ($item) {
            $item->pedido->calcularTotal();
        });

        static::deleted(function ($item) {
            $item->pedido->calcularTotal();
        });
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class);
    }

    public function produtoTamanho(): BelongsTo
    {
        return $this->belongsTo(ProdutoTamanho::class);
    }

    public function sabores(): HasMany
    {
        return $this->hasMany(PedidoItemSabor::class);
    }

    public function scopePendentes($query)
    {
        return $query->where('status', 'pendente');
    }

    public function scopeEmPreparo($query)
    {
        return $query->where('status', 'em_preparo');
    }

    public function getSubtotalFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->subtotal, 2, ',', '.');
    }

    public function getNomeProduto(): string
    {
        // Se o produto ainda existe, usa o nome atual
        if ($this->produto) {
            return $this->produto->nome;
        }

        // Se o produto foi deletado, usa o nome salvo
        return $this->produto_nome ?? 'Produto Removido';
    }
}
