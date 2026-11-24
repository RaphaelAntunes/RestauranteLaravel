<?php

namespace App\Models;

use App\Models\Concerns\BelongsToLoja;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ClienteEndereco extends Model
{
    use HasFactory, BelongsToLoja;

    protected $fillable = [
        'loja_id',
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
        DB::transaction(function () {
            $this->cliente->enderecos()->whereKeyNot($this->id)->update(['padrao' => false]);
            $this->update(['padrao' => true]);
        });
    }
}
