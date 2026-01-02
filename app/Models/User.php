<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nome',
        'email',
        'senha',
        'role_id',
        'ativo',
        'ultimo_acesso',
        'pode_lancar_pedidos',
        'pode_fechar_mesas',
        'pode_cancelar_itens',
        'pode_cancelar_pedidos',
        'face_embedding',
        'facial_obrigatorio',
    ];

    protected $hidden = [
        'senha',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
            'ultimo_acesso' => 'datetime',
            'senha' => 'hashed',
            'pode_lancar_pedidos' => 'boolean',
            'pode_fechar_mesas' => 'boolean',
            'pode_cancelar_itens' => 'boolean',
            'pode_cancelar_pedidos' => 'boolean',
            'face_embedding' => 'array',
            'facial_obrigatorio' => 'boolean',
        ];
    }

    public function getAuthPassword()
    {
        return $this->senha;
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class);
    }

    public function pagamentos(): HasMany
    {
        return $this->hasMany(Pagamento::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(Log::class);
    }

    public function isAdmin(): bool
    {
        return $this->role->nome === 'admin';
    }

    public function isGarcom(): bool
    {
        return $this->role->nome === 'garcom';
    }

    public function isCozinha(): bool
    {
        return $this->role->nome === 'cozinha';
    }

    public function isCaixa(): bool
    {
        return $this->role->nome === 'caixa';
    }

    public function podeLancarPedidos(): bool
    {
        return $this->isAdmin() || $this->pode_lancar_pedidos;
    }

    public function podeFecharMesas(): bool
    {
        return $this->isAdmin() || $this->pode_fechar_mesas;
    }

    public function podeCancelarItens(): bool
    {
        return $this->isAdmin() || $this->pode_cancelar_itens;
    }

    public function podeCancelarPedidos(): bool
    {
        return $this->isAdmin() || $this->pode_cancelar_pedidos;
    }
}
