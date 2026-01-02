<?php

namespace Database\Seeders;

use App\Models\Configuracao;
use Illuminate\Database\Seeder;

class ConfiguracaoSeeder extends Seeder
{
    public function run(): void
    {
        $configuracoes = [
            [
                'chave' => 'nome_restaurante',
                'valor' => 'Restaurante Delícias',
                'tipo' => 'string',
                'descricao' => 'Nome do restaurante',
            ],
            [
                'chave' => 'taxa_servico',
                'valor' => '10',
                'tipo' => 'number',
                'descricao' => 'Taxa de serviço em porcentagem',
            ],
            [
                'chave' => 'tempo_atualizacao_cozinha',
                'valor' => '30',
                'tipo' => 'number',
                'descricao' => 'Tempo de atualização da tela da cozinha em segundos',
            ],
            [
                'chave' => 'permitir_desconto',
                'valor' => 'true',
                'tipo' => 'boolean',
                'descricao' => 'Permitir desconto nos pedidos',
            ],
        ];

        foreach ($configuracoes as $configuracao) {
            Configuracao::create($configuracao);
        }
    }
}
