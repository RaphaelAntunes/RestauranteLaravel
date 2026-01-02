<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'nome' => 'admin',
                'descricao' => 'Administrador do Sistema - Acesso Total',
            ],
            [
                'nome' => 'garcom',
                'descricao' => 'Garçom - Gerencia pedidos e mesas',
            ],
            [
                'nome' => 'cozinha',
                'descricao' => 'Cozinha - Visualiza e prepara pedidos',
            ],
            [
                'nome' => 'caixa',
                'descricao' => 'Caixa/PDV - Realiza fechamentos e pagamentos',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
