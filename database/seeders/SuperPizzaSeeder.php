<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Categoria;
use App\Models\Produto;
use App\Models\ProdutoTamanho;
use App\Models\Sabor;

class SuperPizzaSeeder extends Seeder
{
    public function run()
    {
        // Limpar apenas tabelas de cardápio (preservando mesas e pedidos)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('pedido_item_sabores')->truncate();
        DB::table('pedido_itens')->truncate();
        DB::table('pedidos')->truncate();
        DB::table('produto_tamanhos')->truncate();
        DB::table('sabores')->truncate();
        DB::table('produtos')->truncate();
        DB::table('categorias')->truncate();
        // MESAS NÃO É MAIS TRUNCADO - mesas são preservadas

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Criar categorias
        $categoriaPizza = Categoria::create([
            'nome' => 'Pizzas',
            'descricao' => 'Pizzas salgadas tradicionais e especiais',
            'ativo' => true,
        ]);

        $categoriaDoce = Categoria::create([
            'nome' => 'Pizzas Doces',
            'descricao' => 'Pizzas doces',
            'ativo' => true,
        ]);

        $categoriaBebidas = Categoria::create([
            'nome' => 'Bebidas',
            'descricao' => 'Bebidas geladas e quentes',
            'ativo' => true,
        ]);

        $categoriaBordas = Categoria::create([
            'nome' => 'Bordas Recheadas',
            'descricao' => 'Bordas especiais para sua pizza',
            'ativo' => true,
        ]);

        // Criar produto Pizza
        $pizza = Produto::create([
            'categoria_id' => $categoriaPizza->id,
            'nome' => 'Pizza',
            'descricao' => 'Pizza artesanal com ingredientes selecionados',
            'preco' => 27.00, // Preço base (P)
            'ativo' => true,
        ]);

        // Criar tamanhos para Pizza
        ProdutoTamanho::create([
            'produto_id' => $pizza->id,
            'nome' => 'P',
            'descricao' => 'Pequena',
            'preco' => 27.00,
            'max_sabores' => 1,
        ]);

        ProdutoTamanho::create([
            'produto_id' => $pizza->id,
            'nome' => 'M',
            'descricao' => 'Média',
            'preco' => 40.00,
            'max_sabores' => 2,
        ]);

        ProdutoTamanho::create([
            'produto_id' => $pizza->id,
            'nome' => 'G',
            'descricao' => 'Grande',
            'preco' => 50.00,
            'max_sabores' => 2,
        ]);

        ProdutoTamanho::create([
            'produto_id' => $pizza->id,
            'nome' => 'GG',
            'descricao' => 'Gigante',
            'preco' => 90.00,
            'max_sabores' => 3,
        ]);

        // Criar produto Pizza Doce
        $pizzaDoce = Produto::create([
            'categoria_id' => $categoriaDoce->id,
            'nome' => 'Pizza Doce',
            'descricao' => 'Pizza doce irresistível',
            'preco' => 29.00, // Preço base (P)
            'ativo' => true,
        ]);

        // Criar tamanhos para Pizza Doce
        ProdutoTamanho::create([
            'produto_id' => $pizzaDoce->id,
            'nome' => 'P',
            'descricao' => 'Pequena',
            'preco' => 29.00,
            'max_sabores' => 1,
        ]);

        ProdutoTamanho::create([
            'produto_id' => $pizzaDoce->id,
            'nome' => 'M',
            'descricao' => 'Média',
            'preco' => 44.90,
            'max_sabores' => 2,
        ]);

        ProdutoTamanho::create([
            'produto_id' => $pizzaDoce->id,
            'nome' => 'G',
            'descricao' => 'Grande',
            'preco' => 59.90,
            'max_sabores' => 2,
        ]);

        ProdutoTamanho::create([
            'produto_id' => $pizzaDoce->id,
            'nome' => 'GG',
            'descricao' => 'Gigante',
            'preco' => 96.00,
            'max_sabores' => 3,
        ]);

        // Criar sabores - Pizzas Tradicionais
        $saboresTradicionais = [
            [
                'nome' => 'Portuguesa',
                'ingredientes' => 'Molho especial, queijo mussarela, calabresa, cebola, pimentão, azeitona verde, ovos, orégano',
                'preco_p' => 27.00,
                'preco_m' => 40.00,
                'preco_g' => 50.00,
                'preco_gg' => 90.00,
            ],
            [
                'nome' => 'Marguerita',
                'ingredientes' => 'Molho especial, queijo mussarela, tomate, queijo parmesão, manjericão, orégano',
                'preco_p' => 27.00,
                'preco_m' => 40.00,
                'preco_g' => 50.00,
                'preco_gg' => 90.00,
            ],
            [
                'nome' => 'Toscana',
                'ingredientes' => 'Molho especial, queijo mussarela, calabresa, cebola, azeitona, orégano',
                'preco_p' => 27.00,
                'preco_m' => 40.00,
                'preco_g' => 50.00,
                'preco_gg' => 90.00,
            ],
            [
                'nome' => 'Calabresa',
                'ingredientes' => 'Molho especial, queijo mussarela, calabresa, orégano',
                'preco_p' => 27.00,
                'preco_m' => 40.00,
                'preco_g' => 50.00,
                'preco_gg' => 90.00,
            ],
            [
                'nome' => 'Bacon',
                'ingredientes' => 'Molho especial, queijo mussarela, bacon, milho, orégano',
                'preco_p' => 27.00,
                'preco_m' => 40.00,
                'preco_g' => 50.00,
                'preco_gg' => 90.00,
            ],
            [
                'nome' => 'Frango',
                'ingredientes' => 'Molho especial, queijo mussarela, frango desfiado com catupiry',
                'preco_p' => 27.00,
                'preco_m' => 40.00,
                'preco_g' => 50.00,
                'preco_gg' => 90.00,
            ],
            [
                'nome' => 'Mussarela',
                'ingredientes' => 'Molho especial, queijo mussarela, orégano',
                'preco_p' => 27.00,
                'preco_m' => 40.00,
                'preco_g' => 50.00,
                'preco_gg' => 90.00,
            ],
            [
                'nome' => 'Atum',
                'ingredientes' => 'Molho especial, queijo mussarela, atum, requeijão, orégano',
                'preco_p' => 27.00,
                'preco_m' => 40.00,
                'preco_g' => 50.00,
                'preco_gg' => 90.00,
            ],
        ];

        foreach ($saboresTradicionais as $sabor) {
            Sabor::create([
                'categoria_id' => $categoriaPizza->id,
                'nome' => $sabor['nome'],
                'ingredientes' => $sabor['ingredientes'],
                'preco_p' => $sabor['preco_p'],
                'preco_m' => $sabor['preco_m'],
                'preco_g' => $sabor['preco_g'],
                'preco_gg' => $sabor['preco_gg'],
                'ativo' => true,
            ]);
        }

        // Criar sabores - Pizzas Especiais
        $saboresEspeciais = [
            [
                'nome' => 'Carne de Sol',
                'ingredientes' => 'Molho especial, queijo mussarela, carne de sol, queijo coalho, orégano',
                'preco_p' => 29.00,
                'preco_m' => 44.90,
                'preco_g' => 59.90,
                'preco_gg' => 96.00,
            ],
            [
                'nome' => 'Quatro Queijos',
                'ingredientes' => 'Molho especial, queijo mussarela, provolone, gorgonzola, ricota',
                'preco_p' => 29.00,
                'preco_m' => 44.90,
                'preco_g' => 59.90,
                'preco_gg' => 96.00,
            ],
            [
                'nome' => 'Palmito',
                'ingredientes' => 'Molho especial, queijo mussarela, palmito, catupiry, orégano',
                'preco_p' => 29.00,
                'preco_m' => 44.90,
                'preco_g' => 59.90,
                'preco_gg' => 96.00,
            ],
        ];

        foreach ($saboresEspeciais as $sabor) {
            Sabor::create([
                'categoria_id' => $categoriaPizza->id,
                'nome' => $sabor['nome'],
                'ingredientes' => $sabor['ingredientes'],
                'preco_p' => $sabor['preco_p'],
                'preco_m' => $sabor['preco_m'],
                'preco_g' => $sabor['preco_g'],
                'preco_gg' => $sabor['preco_gg'],
                'ativo' => true,
            ]);
        }

        // Criar sabores - Pizzas Doces
        $saboresDoces = [
            [
                'nome' => 'Chocolate',
                'ingredientes' => 'Queijo mussarela, chocolate em pasta',
                'preco_p' => 29.00,
                'preco_m' => 44.90,
                'preco_g' => 59.90,
                'preco_gg' => 96.00,
            ],
            [
                'nome' => 'Dois Amores',
                'ingredientes' => 'Queijo mussarela, chocolate tradicional e chocolate branco em pasta',
                'preco_p' => 29.00,
                'preco_m' => 44.90,
                'preco_g' => 59.90,
                'preco_gg' => 96.00,
            ],
            [
                'nome' => 'Prestígio',
                'ingredientes' => 'Queijo mussarela, chocolate tradicional em pasta e coco ralado',
                'preco_p' => 29.00,
                'preco_m' => 44.90,
                'preco_g' => 59.90,
                'preco_gg' => 96.00,
            ],
            [
                'nome' => 'Banana Nevada',
                'ingredientes' => 'Queijo mussarela, banana e chocolate branco',
                'preco_p' => 29.00,
                'preco_m' => 44.90,
                'preco_g' => 59.90,
                'preco_gg' => 96.00,
            ],
            [
                'nome' => 'Romeu e Julieta',
                'ingredientes' => 'Queijo mussarela, queijo coalho, goiabada',
                'preco_p' => 29.00,
                'preco_m' => 44.90,
                'preco_g' => 59.90,
                'preco_gg' => 96.00,
            ],
        ];

        foreach ($saboresDoces as $sabor) {
            Sabor::create([
                'categoria_id' => $categoriaDoce->id,
                'nome' => $sabor['nome'],
                'ingredientes' => $sabor['ingredientes'],
                'preco_p' => $sabor['preco_p'],
                'preco_m' => $sabor['preco_m'],
                'preco_g' => $sabor['preco_g'],
                'preco_gg' => $sabor['preco_gg'],
                'ativo' => true,
            ]);
        }

        // Criar bebidas
        $bebidas = [
            ['nome' => 'Coca-Cola 2L', 'preco' => 10.00],
            ['nome' => 'Coca-Cola 1L', 'preco' => 7.00],
            ['nome' => 'Coca-Cola Lata 350ml', 'preco' => 5.00],
            ['nome' => 'Guaraná Antarctica 2L', 'preco' => 9.00],
            ['nome' => 'Guaraná Antarctica Lata 350ml', 'preco' => 4.50],
            ['nome' => 'Fanta Laranja 2L', 'preco' => 9.00],
            ['nome' => 'Fanta Laranja Lata 350ml', 'preco' => 4.50],
            ['nome' => 'Sprite 2L', 'preco' => 9.00],
            ['nome' => 'Sprite Lata 350ml', 'preco' => 4.50],
            ['nome' => 'Água Mineral 500ml', 'preco' => 3.00],
            ['nome' => 'Água com Gás 500ml', 'preco' => 3.50],
            ['nome' => 'Suco Natural Laranja 500ml', 'preco' => 8.00],
            ['nome' => 'Suco Natural Limão 500ml', 'preco' => 8.00],
            ['nome' => 'Suco Natural Maracujá 500ml', 'preco' => 8.00],
            ['nome' => 'Cerveja Heineken Long Neck', 'preco' => 8.00],
            ['nome' => 'Cerveja Skol Lata 350ml', 'preco' => 5.00],
            ['nome' => 'Cerveja Brahma Lata 350ml', 'preco' => 5.00],
            ['nome' => 'Cerveja Original Lata 350ml', 'preco' => 5.50],
        ];

        foreach ($bebidas as $bebida) {
            Produto::create([
                'categoria_id' => $categoriaBebidas->id,
                'nome' => $bebida['nome'],
                'descricao' => 'Bebida gelada',
                'preco' => $bebida['preco'],
                'ativo' => true,
            ]);
        }

        // Criar bordas recheadas
        $bordas = [
            ['nome' => 'Borda Catupiry', 'preco' => 8.00],
            ['nome' => 'Borda Cheddar', 'preco' => 8.00],
            ['nome' => 'Borda Chocolate', 'preco' => 10.00],
            ['nome' => 'Borda Cream Cheese', 'preco' => 9.00],
        ];

        foreach ($bordas as $borda) {
            Produto::create([
                'categoria_id' => $categoriaBordas->id,
                'nome' => $borda['nome'],
                'descricao' => 'Adicional de borda recheada',
                'preco' => $borda['preco'],
                'ativo' => true,
            ]);
        }

        $this->command->info('Cardápio da Super Pizza criado com sucesso!');
        $this->command->info('- 4 Categorias criadas (Pizzas, Pizzas Doces, Bebidas, Bordas)');
        $this->command->info('- 2 Produtos de Pizza criados (Pizza, Pizza Doce)');
        $this->command->info('- 8 Tamanhos criados (4 para cada tipo de pizza)');
        $this->command->info('- 16 Sabores criados (11 tradicionais/especiais + 5 doces)');
        $this->command->info('- 18 Bebidas criadas');
        $this->command->info('- 4 Bordas recheadas criadas');
    }
}
