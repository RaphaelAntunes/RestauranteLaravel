<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;

class CozinhaController extends Controller
{
    /**
     * Painel da cozinha (KDS - Kitchen Display System)
     */
    public function index()
    {
        // Pedidos em preparo
        $pedidosEmPreparo = Pedido::with(['mesa', 'itens.produto', 'itens.produtoTamanho', 'itens.sabores.sabor'])
            ->where('status', 'em_preparo')
            ->orderBy('data_abertura')
            ->get();

        // Novos pedidos (aguardando preparo)
        $novosPedidos = Pedido::with(['mesa', 'itens.produto', 'itens.produtoTamanho', 'itens.sabores.sabor'])
            ->where('status', 'aberto')
            ->orderBy('data_abertura')
            ->get();

        // Pedidos prontos
        $pedidosProntos = Pedido::with(['mesa', 'itens.produto', 'itens.produtoTamanho', 'itens.sabores.sabor'])
            ->where('status', 'pronto')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        return view('cozinha.index', compact('pedidosEmPreparo', 'novosPedidos', 'pedidosProntos'));
    }

    /**
     * Inicia preparo do pedido
     */
    public function iniciarPreparo(Pedido $pedido)
    {
        if ($pedido->status != 'aberto') {
            return response()->json([
                'success' => false,
                'message' => 'Este pedido não pode ser iniciado.'
            ], 400);
        }

        $pedido->update(['status' => 'em_preparo']);

        return response()->json([
            'success' => true,
            'message' => 'Preparo iniciado!',
            'pedido' => $pedido->load(['mesa', 'itens.produto'])
        ]);
    }

    /**
     * Marca pedido como pronto
     */
    public function marcarPronto(Pedido $pedido)
    {
        if ($pedido->status != 'em_preparo') {
            return response()->json([
                'success' => false,
                'message' => 'Este pedido não está em preparo.'
            ], 400);
        }

        $pedido->update(['status' => 'pronto']);

        return response()->json([
            'success' => true,
            'message' => 'Pedido marcado como pronto!',
            'pedido' => $pedido->load(['mesa', 'itens.produto'])
        ]);
    }

    /**
     * Entrega o pedido (marca como entregue)
     */
    public function entregar(Pedido $pedido)
    {
        if ($pedido->status != 'pronto') {
            return response()->json([
                'success' => false,
                'message' => 'Este pedido não está pronto.'
            ], 400);
        }

        $pedido->update(['status' => 'entregue']);

        return response()->json([
            'success' => true,
            'message' => 'Pedido entregue!',
            'pedido' => $pedido
        ]);
    }

    /**
     * Retorna pedidos atualizados (para polling/AJAX)
     */
    public function atualizar()
    {
        $novosPedidos = Pedido::with(['mesa', 'itens.produto', 'itens.produtoTamanho', 'itens.sabores.sabor'])
            ->where('status', 'aberto')
            ->orderBy('data_abertura')
            ->get();

        $pedidosEmPreparo = Pedido::with(['mesa', 'itens.produto', 'itens.produtoTamanho', 'itens.sabores.sabor'])
            ->where('status', 'em_preparo')
            ->orderBy('data_abertura')
            ->get();

        $pedidosProntos = Pedido::with(['mesa', 'itens.produto', 'itens.produtoTamanho', 'itens.sabores.sabor'])
            ->where('status', 'pronto')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Renderizar HTML dos pedidos
        $htmlNovos = '';
        foreach ($novosPedidos as $pedido) {
            $htmlNovos .= view('cozinha.partials.pedido-card', ['pedido' => $pedido, 'tipo' => 'novo'])->render();
        }

        $htmlPreparo = '';
        foreach ($pedidosEmPreparo as $pedido) {
            $htmlPreparo .= view('cozinha.partials.pedido-card', ['pedido' => $pedido, 'tipo' => 'preparo'])->render();
        }

        $htmlProntos = '';
        foreach ($pedidosProntos as $pedido) {
            $htmlProntos .= view('cozinha.partials.pedido-card', ['pedido' => $pedido, 'tipo' => 'pronto'])->render();
        }

        $data = [
            'novos' => $novosPedidos,
            'em_preparo' => $pedidosEmPreparo,
            'prontos' => $pedidosProntos,
            'html_novos' => $htmlNovos,
            'html_preparo' => $htmlPreparo,
            'html_prontos' => $htmlProntos,
        ];

        return response()->json($data);
    }
}
