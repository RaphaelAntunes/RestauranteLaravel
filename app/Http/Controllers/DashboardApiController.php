<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Mesa;
use Illuminate\Http\Request;

class DashboardApiController extends Controller
{
    /**
     * Retorna detalhes de um pedido para o modal
     */
    public function getPedido($id)
    {
        $pedido = Pedido::with(['mesa', 'user', 'itens.produto', 'itens.produtoTamanho', 'itens.sabores.sabor'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'pedido' => [
                'id' => $pedido->id,
                'numero_pedido' => $pedido->numero_pedido,
                'mesa' => [
                    'numero' => $pedido->mesa->numero ?? '-',
                    'localizacao' => $pedido->mesa->localizacao ?? 'Delivery',
                ],
                'garcom' => $pedido->user ? $pedido->user->nome : 'Garçom removido',
                'status' => $pedido->status,
                'status_formatado' => ucfirst(str_replace('_', ' ', $pedido->status)),
                'total' => $pedido->total,
                'total_formatado' => 'R$ ' . number_format($pedido->total, 2, ',', '.'),
                'data' => $pedido->created_at->format('d/m/Y H:i'),
                'observacoes' => $pedido->observacoes,
                'itens' => $pedido->itens->map(function($item) {
                    $sabores = $item->sabores->pluck('sabor.nome')->filter()->join(', ');
                    return [
                        'produto' => $item->produto->nome,
                        'tamanho' => $item->produtoTamanho ? $item->produtoTamanho->nome : null,
                        'sabores' => $sabores ?: null,
                        'quantidade' => $item->quantidade,
                        'preco_unitario' => 'R$ ' . number_format($item->preco_unitario, 2, ',', '.'),
                        'subtotal' => 'R$ ' . number_format($item->subtotal, 2, ',', '.'),
                        'observacoes' => $item->observacoes,
                    ];
                }),
            ]
        ]);
    }

    /**
     * Retorna detalhes de uma mesa para o modal
     */
    public function getMesa($id)
    {
        $mesa = Mesa::with(['pedidos' => function($query) {
                $query->whereIn('status', ['aberto', 'em_preparo', 'pronto'])
                      ->with(['itens.produto', 'itens.produtoTamanho', 'itens.sabores.sabor']);
            }])
            ->findOrFail($id);

        $totalGeral = $mesa->pedidos->sum('total');

        return response()->json([
            'success' => true,
            'mesa' => [
                'id' => $mesa->id,
                'numero' => $mesa->numero,
                'localizacao' => $mesa->localizacao,
                'capacidade' => $mesa->capacidade,
                'status' => $mesa->status,
                'total_geral' => 'R$ ' . number_format($totalGeral, 2, ',', '.'),
                'pedidos' => $mesa->pedidos->map(function($pedido) {
                    return [
                        'id' => $pedido->id,
                        'numero_pedido' => $pedido->numero_pedido,
                        'status' => $pedido->status,
                        'status_formatado' => ucfirst(str_replace('_', ' ', $pedido->status)),
                        'total' => 'R$ ' . number_format($pedido->total, 2, ',', '.'),
                        'itens' => $pedido->itens->map(function($item) {
                            $sabores = $item->sabores->pluck('sabor.nome')->filter()->join(', ');
                            return [
                                'produto' => $item->produto->nome,
                                'tamanho' => $item->produtoTamanho ? $item->produtoTamanho->nome : null,
                                'sabores' => $sabores ?: null,
                                'quantidade' => $item->quantidade,
                                'subtotal' => 'R$ ' . number_format($item->subtotal, 2, ',', '.'),
                                'observacoes' => $item->observacoes,
                            ];
                        }),
                    ];
                }),
            ]
        ]);
    }
}
