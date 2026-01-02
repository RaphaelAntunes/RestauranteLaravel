<?php

namespace Database\Seeders;

use App\Models\Mesa;
use Illuminate\Database\Seeder;

class MesaSeeder extends Seeder
{
    public function run(): void
    {
        $mesas = [
            ['numero' => 1, 'capacidade' => 4, 'localizacao' => 'Salão Principal'],
            ['numero' => 2, 'capacidade' => 4, 'localizacao' => 'Salão Principal'],
            ['numero' => 3, 'capacidade' => 2, 'localizacao' => 'Salão Principal'],
            ['numero' => 4, 'capacidade' => 6, 'localizacao' => 'Salão Principal'],
            ['numero' => 5, 'capacidade' => 4, 'localizacao' => 'Varanda'],
            ['numero' => 6, 'capacidade' => 2, 'localizacao' => 'Varanda'],
            ['numero' => 7, 'capacidade' => 4, 'localizacao' => 'Área Externa'],
            ['numero' => 8, 'capacidade' => 8, 'localizacao' => 'Salão VIP'],
            ['numero' => 9, 'capacidade' => 4, 'localizacao' => 'Salão Principal'],
            ['numero' => 10, 'capacidade' => 2, 'localizacao' => 'Salão Principal'],
        ];

        foreach ($mesas as $mesa) {
            Mesa::create($mesa);
        }
    }
}
