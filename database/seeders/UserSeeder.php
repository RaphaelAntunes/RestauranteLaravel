<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'nome' => 'Administrador',
            'email' => 'admin@superpizza.com',
            'senha' => Hash::make('admin@superpizza.com'),
            'role_id' => 1,
            'ativo' => true,
        ]);
    }
}
