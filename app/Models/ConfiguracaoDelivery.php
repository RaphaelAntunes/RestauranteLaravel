<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracaoDelivery extends Model
{
    use HasFactory;

    protected $table = 'configuracoes_delivery';

    protected $fillable = [
        'tipo_taxa',
        'valor_taxa_fixa',
        'valor_minimo_gratis',
        'tempo_medio_preparo',
        'pedido_minimo',
        'ativo',
        'horario_inicio',
        'horario_fim',
        'dias_funcionamento',
    ];

    protected function casts(): array
    {
        return [
            'valor_taxa_fixa' => 'decimal:2',
            'valor_minimo_gratis' => 'decimal:2',
            'pedido_minimo' => 'decimal:2',
            'tempo_medio_preparo' => 'integer',
            'ativo' => 'boolean',
            'dias_funcionamento' => 'array',
        ];
    }

    public static function obter(): self
    {
        return self::firstOrCreate([], [
            'tipo_taxa' => 'fixa',
            'valor_taxa_fixa' => 5.00,
            'tempo_medio_preparo' => 40,
        ]);
    }

    public function calcularTaxaEntrega(float $subtotal): float
    {
        if ($this->tipo_taxa === 'gratis_acima' && $subtotal >= $this->valor_minimo_gratis) {
            return 0.00;
        }
        return $this->valor_taxa_fixa;
    }

    public function isDeliveryAberto(): bool
    {
        if (!$this->ativo) {
            return false;
        }

        if ($this->dias_funcionamento) {
            $diasMap = [
                'Sunday' => 'dom',
                'Monday' => 'seg',
                'Tuesday' => 'ter',
                'Wednesday' => 'qua',
                'Thursday' => 'qui',
                'Friday' => 'sex',
                'Saturday' => 'sab',
            ];

            $diaHoje = $diasMap[now()->format('l')];

            if (!in_array($diaHoje, $this->dias_funcionamento)) {
                return false;
            }
        }

        if ($this->horario_inicio && $this->horario_fim) {
            $agora = now()->format('H:i:s');
            if ($agora < $this->horario_inicio || $agora > $this->horario_fim) {
                return false;
            }
        }

        return true;
    }
}
