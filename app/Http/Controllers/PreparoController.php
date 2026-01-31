<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Mesa;
use App\Models\Pedido;
use Illuminate\Http\Request;

class PreparoController extends Controller
{
    /**
     * Painel de Preparo (apenas comidas, sem bebidas)
     */
    public function index()
    {
        // IDs das categorias de comida (não bebida)
        $categoriasComidaIds = Categoria::where('tipo', 'comida')->pluck('id');

        // Pedidos em preparo que têm pelo menos um item de comida
        $pedidosEmPreparo = Pedido::with(['mesa', 'itens.produto.categoria', 'itens.produtoTamanho', 'itens.sabores.sabor'])
            ->where('status', 'em_preparo')
            ->whereHas('itens.produto', function($q) use ($categoriasComidaIds) {
                $q->whereIn('categoria_id', $categoriasComidaIds);
            })
            ->orderBy('data_abertura')
            ->get();

        // Novos pedidos (aguardando preparo) que têm pelo menos um item de comida
        $novosPedidos = Pedido::with(['mesa', 'itens.produto.categoria', 'itens.produtoTamanho', 'itens.sabores.sabor'])
            ->where('status', 'aberto')
            ->whereHas('itens.produto', function($q) use ($categoriasComidaIds) {
                $q->whereIn('categoria_id', $categoriasComidaIds);
            })
            ->orderBy('data_abertura')
            ->get();

        // Pedidos prontos que têm pelo menos um item de comida
        $pedidosProntos = Pedido::with(['mesa', 'itens.produto.categoria', 'itens.produtoTamanho', 'itens.sabores.sabor'])
            ->where('status', 'pronto')
            ->whereHas('itens.produto', function($q) use ($categoriasComidaIds) {
                $q->whereIn('categoria_id', $categoriasComidaIds);
            })
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Pedidos saiu para entrega que têm pelo menos um item de comida
        $pedidosSaiuEntrega = Pedido::with(['mesa', 'itens.produto.categoria', 'itens.produtoTamanho', 'itens.sabores.sabor'])
            ->where('status', 'saiu_entrega')
            ->whereHas('itens.produto', function($q) use ($categoriasComidaIds) {
                $q->whereIn('categoria_id', $categoriasComidaIds);
            })
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Filtrar itens de cada pedido para mostrar apenas comidas
        $pedidosEmPreparo = $this->filtrarItensComida($pedidosEmPreparo, $categoriasComidaIds);
        $novosPedidos = $this->filtrarItensComida($novosPedidos, $categoriasComidaIds);
        $pedidosProntos = $this->filtrarItensComida($pedidosProntos, $categoriasComidaIds);
        $pedidosSaiuEntrega = $this->filtrarItensComida($pedidosSaiuEntrega, $categoriasComidaIds);

        return view('preparo.index', compact('pedidosEmPreparo', 'novosPedidos', 'pedidosProntos', 'pedidosSaiuEntrega'));
    }

    /**
     * Filtra os itens dos pedidos para mostrar apenas comidas
     */
    private function filtrarItensComida($pedidos, $categoriasComidaIds)
    {
        foreach ($pedidos as $pedido) {
            $pedido->setRelation('itens', $pedido->itens->filter(function($item) use ($categoriasComidaIds) {
                return $item->produto && $categoriasComidaIds->contains($item->produto->categoria_id);
            }));
        }
        return $pedidos;
    }

    /**
     * Retorna pedidos atualizados (para polling/AJAX)
     */
    public function atualizar()
    {
        $categoriasComidaIds = Categoria::where('tipo', 'comida')->pluck('id');

        $novosPedidos = Pedido::with(['mesa', 'itens.produto.categoria', 'itens.produtoTamanho', 'itens.sabores.sabor'])
            ->where('status', 'aberto')
            ->whereHas('itens.produto', function($q) use ($categoriasComidaIds) {
                $q->whereIn('categoria_id', $categoriasComidaIds);
            })
            ->orderBy('data_abertura')
            ->get();

        $pedidosEmPreparo = Pedido::with(['mesa', 'itens.produto.categoria', 'itens.produtoTamanho', 'itens.sabores.sabor'])
            ->where('status', 'em_preparo')
            ->whereHas('itens.produto', function($q) use ($categoriasComidaIds) {
                $q->whereIn('categoria_id', $categoriasComidaIds);
            })
            ->orderBy('data_abertura')
            ->get();

        $pedidosProntos = Pedido::with(['mesa', 'itens.produto.categoria', 'itens.produtoTamanho', 'itens.sabores.sabor'])
            ->where('status', 'pronto')
            ->whereHas('itens.produto', function($q) use ($categoriasComidaIds) {
                $q->whereIn('categoria_id', $categoriasComidaIds);
            })
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        $pedidosSaiuEntrega = Pedido::with(['mesa', 'itens.produto.categoria', 'itens.produtoTamanho', 'itens.sabores.sabor'])
            ->where('status', 'saiu_entrega')
            ->whereHas('itens.produto', function($q) use ($categoriasComidaIds) {
                $q->whereIn('categoria_id', $categoriasComidaIds);
            })
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Filtrar itens
        $novosPedidos = $this->filtrarItensComida($novosPedidos, $categoriasComidaIds);
        $pedidosEmPreparo = $this->filtrarItensComida($pedidosEmPreparo, $categoriasComidaIds);
        $pedidosProntos = $this->filtrarItensComida($pedidosProntos, $categoriasComidaIds);
        $pedidosSaiuEntrega = $this->filtrarItensComida($pedidosSaiuEntrega, $categoriasComidaIds);

        // Renderizar HTML dos pedidos
        $htmlNovos = '';
        foreach ($novosPedidos as $pedido) {
            $htmlNovos .= view('preparo.partials.pedido-card', ['pedido' => $pedido, 'tipo' => 'novo'])->render();
        }

        $htmlPreparo = '';
        foreach ($pedidosEmPreparo as $pedido) {
            $htmlPreparo .= view('preparo.partials.pedido-card', ['pedido' => $pedido, 'tipo' => 'preparo'])->render();
        }

        $htmlProntos = '';
        foreach ($pedidosProntos as $pedido) {
            $htmlProntos .= view('preparo.partials.pedido-card', ['pedido' => $pedido, 'tipo' => 'pronto'])->render();
        }

        $htmlSaiuEntrega = '';
        foreach ($pedidosSaiuEntrega as $pedido) {
            $htmlSaiuEntrega .= view('preparo.partials.pedido-card', ['pedido' => $pedido, 'tipo' => 'saiu_entrega'])->render();
        }

        $data = [
            'novos' => $novosPedidos,
            'em_preparo' => $pedidosEmPreparo,
            'prontos' => $pedidosProntos,
            'saiu_entrega' => $pedidosSaiuEntrega,
            'html_novos' => $htmlNovos,
            'html_preparo' => $htmlPreparo,
            'html_prontos' => $htmlProntos,
            'html_saiu_entrega' => $htmlSaiuEntrega,
        ];

        return response()->json($data);
    }
}
