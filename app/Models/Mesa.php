<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mesa extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero',
        'tipo',
        'pedido_online_id',
        'capacidade',
        'status',
        'localizacao',
        'cliente_nome',
        'sessao_atual',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
            'numero' => 'integer',
            'capacidade' => 'integer',
        ];
    }

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class);
    }

    public function pedidoAtivo()
    {
        return $this->hasOne(Pedido::class)
            ->whereIn('status', ['aberto', 'em_preparo', 'pronto', 'entregue'])
            ->latest();
    }

    public function isDisponivel(): bool
    {
        return $this->status === 'disponivel';
    }

    public function isOcupada(): bool
    {
        return $this->status === 'ocupada';
    }

    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeDisponiveis($query)
    {
        return $query->where('status', 'disponivel');
    }

    public function scopeNormais($query)
    {
        return $query->where('tipo', 'normal');
    }

    public function scopeDelivery($query)
    {
        return $query->where('tipo', 'delivery');
    }

    public function scopeRetirada($query)
    {
        return $query->where('tipo', 'retirada');
    }

    public function scopeOnline($query)
    {
        return $query->whereIn('tipo', ['delivery', 'retirada']);
    }

    public function isDelivery(): bool
    {
        return $this->tipo === 'delivery';
    }

    public function isRetirada(): bool
    {
        return $this->tipo === 'retirada';
    }

    public function isOnline(): bool
    {
        return in_array($this->tipo, ['delivery', 'retirada']);
    }

    public function pedidoOnline()
    {
        return $this->belongsTo(Pedido::class, 'pedido_online_id');
    }

    /**
     * Cria uma mesa virtual para pedido online
     */
    public static function criarParaPedidoOnline(Pedido $pedido): self
    {
        // Busca o próximo número disponível para mesas virtuais (começando em 900)
        $ultimaMesaVirtual = self::where('tipo', '!=', 'normal')
            ->where('numero', '>=', 900)
            ->max('numero');

        $proximoNumero = $ultimaMesaVirtual ? $ultimaMesaVirtual + 1 : 900;

        $clienteNome = $pedido->cliente ? $pedido->cliente->nome : 'Cliente Online';

        $mesa = self::create([
            'numero' => $proximoNumero,
            'tipo' => $pedido->tipo_pedido, // 'delivery' ou 'retirada'
            'pedido_online_id' => $pedido->id,
            'capacidade' => 1,
            'status' => 'ocupada',
            'localizacao' => $pedido->tipo_pedido === 'delivery' ? 'Delivery' : 'Retirada',
            'cliente_nome' => $clienteNome,
            'ativo' => true,
        ]);

        // Vincula o pedido à mesa
        $pedido->update(['mesa_id' => $mesa->id]);

        return $mesa;
    }

    /**
     * Libera a mesa virtual após fechamento
     */
    public function liberarMesaVirtual(): void
    {
        if ($this->isOnline()) {
            $this->delete();
        }
    }
}
