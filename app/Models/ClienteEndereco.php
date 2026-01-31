<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClienteEndereco extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'nome_endereco',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'referencia',
        'padrao',
    ];

    protected function casts(): array
    {
        return [
            'padrao' => 'boolean',
        ];
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    public function getEnderecoCompleto(): string
    {
        return "{$this->logradouro}, {$this->numero} - {$this->bairro}, {$this->cidade}/{$this->estado}";
    }

    public function marcarComoPadrao(): void
    {
        $this->cliente->enderecos()->update(['padrao' => false]);
        $this->update(['padrao' => true]);
    }
}
