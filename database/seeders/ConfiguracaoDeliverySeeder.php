<?php

namespace Database\Seeders;

use App\Models\ConfiguracaoDelivery;
use Illuminate\Database\Seeder;

class ConfiguracaoDeliverySeeder extends Seeder
{
    public function run(): void
    {
        ConfiguracaoDelivery::create([
            'tipo_taxa' => 'fixa',
            'valor_taxa_fixa' => 5.00,
            'tempo_medio_preparo' => 40,
            'pedido_minimo' => 0.00,
            'ativo' => true,
            'dias_funcionamento' => ['seg', 'ter', 'qua', 'qui', 'sex', 'sab', 'dom'],
        ]);
    }
}
