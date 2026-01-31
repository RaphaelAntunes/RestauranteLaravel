<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'celular',
        'codigo',
        'tentativas',
        'expirado_em',
        'usado_em',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'expirado_em' => 'datetime',
            'usado_em' => 'datetime',
            'tentativas' => 'integer',
        ];
    }

    public static function gerarCodigo(string $celular, ?string $ip = null): self
    {
        return self::create([
            'celular' => $celular,
            'codigo' => str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT),
            'expirado_em' => now()->addMinutes(5),
            'ip_address' => $ip,
        ]);
    }

    public function validar(string $codigo): bool
    {
        if ($this->isExpirado() || $this->usado_em || $this->tentativas >= 3) {
            return false;
        }

        if ($this->codigo === $codigo) {
            $this->marcarComoUsado();
            return true;
        }

        $this->increment('tentativas');
        return false;
    }

    public function isExpirado(): bool
    {
        return $this->expirado_em->isPast();
    }

    public function marcarComoUsado(): void
    {
        $this->update(['usado_em' => now()]);
    }

    public function scopeValidos($query)
    {
        return $query->where('expirado_em', '>', now())->whereNull('usado_em');
    }
}
