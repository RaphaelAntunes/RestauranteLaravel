<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            [
                'nome' => 'Entradas',
                'descricao' => 'Porções e aperitivos',
                'ordem' => 1,
            ],
            [
                'nome' => 'Pratos Principais',
                'descricao' => 'Pratos principais',
                'ordem' => 2,
            ],
            [
                'nome' => 'Massas',
                'descricao' => 'Massas e risotos',
                'ordem' => 3,
            ],
            [
                'nome' => 'Pizzas',
                'descricao' => 'Pizzas tradicionais e especiais',
                'ordem' => 4,
            ],
            [
                'nome' => 'Bebidas',
                'descricao' => 'Bebidas em geral',
                'ordem' => 5,
            ],
            [
                'nome' => 'Sobremesas',
                'descricao' => 'Doces e sobremesas',
                'ordem' => 6,
            ],
        ];

        foreach ($categorias as $categoria) {
            Categoria::create($categoria);
        }
    }
}
