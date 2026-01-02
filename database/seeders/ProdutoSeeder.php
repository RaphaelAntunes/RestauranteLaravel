<?php

namespace Database\Seeders;

use App\Models\Produto;
use Illuminate\Database\Seeder;

class ProdutoSeeder extends Seeder
{
    public function run(): void
    {
        $produtos = [
            // Entradas
            [
                'categoria_id' => 1,
                'nome' => 'Porção de Batata Frita',
                'descricao' => 'Batatas fritas crocantes (500g)',
                'preco' => 25.00,
                'tempo_preparo' => 15,
                'ativo' => true,
                'destaque' => false,
            ],
            [
                'categoria_id' => 1,
                'nome' => 'Bruschetta',
                'descricao' => 'Pão italiano com tomate, manjericão e azeite',
                'preco' => 28.00,
                'tempo_preparo' => 10,
                'ativo' => true,
                'destaque' => true,
            ],
            [
                'categoria_id' => 1,
                'nome' => 'Bolinho de Bacalhau',
                'descricao' => '6 unidades com molho especial',
                'preco' => 35.00,
                'tempo_preparo' => 20,
                'ativo' => true,
                'destaque' => false,
            ],

            // Pratos Principais
            [
                'categoria_id' => 2,
                'nome' => 'Filé Mignon ao Molho Madeira',
                'descricao' => 'Acompanha arroz, batata e salada',
                'preco' => 68.00,
                'tempo_preparo' => 30,
                'ativo' => true,
                'destaque' => true,
            ],
            [
                'categoria_id' => 2,
                'nome' => 'Salmão Grelhado',
                'descricao' => 'Com legumes e molho de maracujá',
                'preco' => 75.00,
                'tempo_preparo' => 25,
                'ativo' => true,
                'destaque' => true,
            ],
            [
                'categoria_id' => 2,
                'nome' => 'Picanha na Chapa',
                'descricao' => '400g com acompanhamentos',
                'preco' => 85.00,
                'tempo_preparo' => 35,
                'ativo' => true,
                'destaque' => false,
            ],

            // Massas
            [
                'categoria_id' => 3,
                'nome' => 'Espaguete à Carbonara',
                'descricao' => 'Massa com bacon, ovos e queijo',
                'preco' => 42.00,
                'tempo_preparo' => 20,
                'ativo' => true,
                'destaque' => false,
            ],
            [
                'categoria_id' => 3,
                'nome' => 'Lasanha Bolonhesa',
                'descricao' => 'Camadas de massa, molho e queijo',
                'preco' => 45.00,
                'tempo_preparo' => 25,
                'ativo' => true,
                'destaque' => false,
            ],
            [
                'categoria_id' => 3,
                'nome' => 'Risoto de Funghi',
                'descricao' => 'Arroz arbóreo com cogumelos',
                'preco' => 52.00,
                'tempo_preparo' => 30,
                'ativo' => true,
                'destaque' => true,
            ],

            // Pizzas
            [
                'categoria_id' => 4,
                'nome' => 'Pizza Margherita',
                'descricao' => 'Molho, mussarela e manjericão',
                'preco' => 45.00,
                'tempo_preparo' => 20,
                'ativo' => true,
                'destaque' => false,
            ],
            [
                'categoria_id' => 4,
                'nome' => 'Pizza Calabresa',
                'descricao' => 'Calabresa, cebola e azeitonas',
                'preco' => 48.00,
                'tempo_preparo' => 20,
                'ativo' => true,
                'destaque' => false,
            ],
            [
                'categoria_id' => 4,
                'nome' => 'Pizza Quatro Queijos',
                'descricao' => 'Mussarela, gorgonzola, provolone e parmesão',
                'preco' => 52.00,
                'tempo_preparo' => 20,
                'ativo' => true,
                'destaque' => true,
            ],

            // Bebidas
            [
                'categoria_id' => 5,
                'nome' => 'Refrigerante Lata',
                'descricao' => 'Coca-Cola, Guaraná, Sprite',
                'preco' => 6.00,
                'tempo_preparo' => 2,
                'ativo' => true,
                'destaque' => false,
            ],
            [
                'categoria_id' => 5,
                'nome' => 'Suco Natural',
                'descricao' => 'Laranja, limão ou maracujá',
                'preco' => 12.00,
                'tempo_preparo' => 5,
                'ativo' => true,
                'destaque' => false,
            ],
            [
                'categoria_id' => 5,
                'nome' => 'Água Mineral 500ml',
                'descricao' => 'Com ou sem gás',
                'preco' => 4.00,
                'tempo_preparo' => 1,
                'ativo' => true,
                'destaque' => false,
            ],
            [
                'categoria_id' => 5,
                'nome' => 'Cerveja Heineken Long Neck',
                'descricao' => 'Cerveja importada gelada',
                'preco' => 15.00,
                'tempo_preparo' => 2,
                'ativo' => true,
                'destaque' => false,
            ],

            // Sobremesas
            [
                'categoria_id' => 6,
                'nome' => 'Petit Gateau',
                'descricao' => 'Bolo de chocolate com sorvete',
                'preco' => 28.00,
                'tempo_preparo' => 15,
                'ativo' => true,
                'destaque' => true,
            ],
            [
                'categoria_id' => 6,
                'nome' => 'Pudim de Leite',
                'descricao' => 'Pudim caseiro com calda',
                'preco' => 18.00,
                'tempo_preparo' => 5,
                'ativo' => true,
                'destaque' => false,
            ],
            [
                'categoria_id' => 6,
                'nome' => 'Brownie com Sorvete',
                'descricao' => 'Brownie quente com sorvete de creme',
                'preco' => 22.00,
                'tempo_preparo' => 10,
                'ativo' => true,
                'destaque' => false,
            ],
        ];

        foreach ($produtos as $produto) {
            Produto::create($produto);
        }
    }
}
