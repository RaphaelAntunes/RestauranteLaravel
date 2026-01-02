<?php

namespace App\Services;

use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Produto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PedidoService
{
    /**
     * Cria um novo pedido para uma mesa
     */
    public function criarPedido(int $mesaId, int $userId, ?string $observacoes = null): Pedido
    {
        return DB::transaction(function () use ($mesaId, $userId, $observacoes) {
            $mesa = Mesa::findOrFail($mesaId);

            if (!$mesa->isDisponivel()) {
                throw new \Exception('Mesa não está disponível');
            }

            $pedido = Pedido::create([
                'mesa_id' => $mesaId,
                'user_id' => $userId,
                'numero_pedido' => $this->gerarNumeroPedido(),
                'status' => 'aberto',
                'observacoes' => $observacoes,
            ]);

            $mesa->update(['status' => 'ocupada']);

            return $pedido;
        });
    }

    /**
     * Adiciona um item ao pedido
     */
    public function adicionarItem(
        int $pedidoId,
        int $produtoId,
        int $quantidade = 1,
        ?string $observacoes = null
    ): PedidoItem {
        $pedido = Pedido::findOrFail($pedidoId);

        if (!$pedido->isAberto()) {
            throw new \Exception('Pedido não está mais aberto para modificações');
        }

        $produto = Produto::findOrFail($produtoId);

        if (!$produto->ativo) {
            throw new \Exception('Produto não está disponível');
        }

        return PedidoItem::create([
            'pedido_id' => $pedidoId,
            'produto_id' => $produtoId,
            'quantidade' => $quantidade,
            'preco_unitario' => $produto->preco,
            'observacoes' => $observacoes,
        ]);
    }

    /**
     * Remove um item do pedido
     */
    public function removerItem(int $itemId): bool
    {
        $item = PedidoItem::findOrFail($itemId);

        if (!$item->pedido->isAberto()) {
            throw new \Exception('Pedido não está mais aberto para modificações');
        }

        return $item->delete();
    }

    /**
     * Atualiza a quantidade de um item
     */
    public function atualizarQuantidade(int $itemId, int $quantidade): PedidoItem
    {
        $item = PedidoItem::findOrFail($itemId);

        if (!$item->pedido->isAberto()) {
            throw new \Exception('Pedido não está mais aberto para modificações');
        }

        if ($quantidade <= 0) {
            throw new \Exception('Quantidade deve ser maior que zero');
        }

        $item->update(['quantidade' => $quantidade]);

        return $item->fresh();
    }

    /**
     * Envia o pedido para a cozinha
     */
    public function enviarParaCozinha(int $pedidoId): Pedido
    {
        $pedido = Pedido::findOrFail($pedidoId);

        if (!$pedido->isAberto()) {
            throw new \Exception('Apenas pedidos abertos podem ser enviados para a cozinha');
        }

        if ($pedido->itens()->count() === 0) {
            throw new \Exception('Pedido não possui itens');
        }

        $pedido->update(['status' => 'em_preparo']);
        $pedido->itens()->update(['status' => 'em_preparo']);

        return $pedido->fresh();
    }

    /**
     * Marca o pedido como pronto
     */
    public function marcarComoPronto(int $pedidoId): Pedido
    {
        $pedido = Pedido::findOrFail($pedidoId);

        if ($pedido->status !== 'em_preparo') {
            throw new \Exception('Apenas pedidos em preparo podem ser marcados como prontos');
        }

        $pedido->update(['status' => 'pronto']);
        $pedido->itens()->update(['status' => 'pronto']);

        return $pedido->fresh();
    }

    /**
     * Marca o pedido como entregue
     */
    public function marcarComoEntregue(int $pedidoId): Pedido
    {
        $pedido = Pedido::findOrFail($pedidoId);

        if ($pedido->status !== 'pronto') {
            throw new \Exception('Apenas pedidos prontos podem ser marcados como entregues');
        }

        $pedido->update(['status' => 'entregue']);
        $pedido->itens()->update(['status' => 'entregue']);

        return $pedido->fresh();
    }

    /**
     * Cancela um pedido
     */
    public function cancelarPedido(int $pedidoId, string $motivo): Pedido
    {
        return DB::transaction(function () use ($pedidoId, $motivo) {
            $pedido = Pedido::findOrFail($pedidoId);

            if ($pedido->isFinalizado()) {
                throw new \Exception('Pedido já foi finalizado e não pode ser cancelado');
            }

            $pedido->update([
                'status' => 'cancelado',
                'observacoes' => ($pedido->observacoes ?? '') . "\n\nMotivo do cancelamento: {$motivo}",
                'data_finalizacao' => now(),
            ]);

            $pedido->itens()->update(['status' => 'cancelado']);

            $pedido->mesa->update(['status' => 'disponivel']);

            return $pedido->fresh();
        });
    }

    /**
     * Gera um número único para o pedido
     */
    protected function gerarNumeroPedido(): string
    {
        do {
            $numero = 'PED-' . strtoupper(Str::random(8));
        } while (Pedido::where('numero_pedido', $numero)->exists());

        return $numero;
    }
}
