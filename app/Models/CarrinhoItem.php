<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarrinhoItem extends Model
{
    use HasFactory;

    protected $table = 'carrinho_itens';

    protected $fillable = [
        'cliente_id',
        'session_id',
        'produto_id',
        'produto_tamanho_id',
        'quantidade',
        'preco_unitario',
        'observacoes',
    ];

    protected function casts(): array
    {
        return [
            'quantidade' => 'integer',
            'preco_unitario' => 'decimal:2',
        ];
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }

    public function produtoTamanho()
    {
        return $this->belongsTo(ProdutoTamanho::class);
    }

    public function sabores()
    {
        return $this->hasMany(CarrinhoItemSabor::class);
    }

    public function calcularSubtotal(): float
    {
        return $this->quantidade * $this->preco_unitario;
    }
}
