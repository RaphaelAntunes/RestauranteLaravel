<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\ConfiguracaoDelivery;
use App\Models\Produto;
use App\Models\Sabor;

class CardapioController extends Controller
{
    public function index()
    {
        $categorias = Categoria::ativas()
            ->with(['produtosAtivos' => function ($query) {
                $query->with('tamanhos');
            }])
            ->ordenadas()
            ->get();

        $sabores = Sabor::where('ativo', true)
            ->with('categoria')
            ->orderBy('ordem')
            ->orderBy('nome')
            ->get()
            ->groupBy('categoria.nome');

        $config = ConfiguracaoDelivery::obter();

        return view('cliente.cardapio.index', compact('categorias', 'sabores', 'config'));
    }

    public function show(Produto $produto)
    {
        $produto->load(['categoria', 'tamanhos']);

        $sabores = [];
        if ($produto->categoria) {
            $sabores = Sabor::where('categoria_id', $produto->categoria_id)
                ->where('ativo', true)
                ->orderBy('ordem')
                ->orderBy('nome')
                ->get();
        }

        return view('cliente.cardapio.show', compact('produto', 'sabores'));
    }
}
