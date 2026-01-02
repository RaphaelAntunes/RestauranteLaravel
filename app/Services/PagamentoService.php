<?php

namespace App\Services;

use App\Models\Configuracao;
use App\Models\Pagamento;
use App\Models\PagamentoDetalhe;
use App\Models\Pedido;
use Illuminate\Support\Facades\DB;

class PagamentoService
{
    /**
     * Processa um pagamento simples
     */
    public function processarPagamentoSimples(
        int $pedidoId,
        int $userId,
        string $metodoPagamento,
        float $valorPago,
        float $desconto = 0.00
    ): Pagamento {
        return DB::transaction(function () use ($pedidoId, $userId, $metodoPagamento, $valorPago, $desconto) {
            $pedido = Pedido::findOrFail($pedidoId);

            if ($pedido->isFinalizado()) {
                throw new \Exception('Pedido já foi finalizado');
            }

            if ($pedido->isCancelado()) {
                throw new \Exception('Pedido foi cancelado');
            }

            $valorTotal = $this->calcularValorTotal($pedido, $desconto);
            $troco = max(0, $valorPago - $valorTotal);

            $pagamento = Pagamento::create([
                'pedido_id' => $pedidoId,
                'user_id' => $userId,
                'valor_total' => $valorTotal,
                'metodo_pagamento' => $metodoPagamento,
                'valor_pago' => $valorPago,
                'troco' => $troco,
                'desconto' => $desconto,
            ]);

            $this->finalizarPedido($pedido);

            return $pagamento;
        });
    }

    /**
     * Processa um pagamento múltiplo (dividido em várias formas)
     */
    public function processarPagamentoMultiplo(
        int $pedidoId,
        int $userId,
        array $metodos,
        float $desconto = 0.00
    ): Pagamento {
        return DB::transaction(function () use ($pedidoId, $userId, $metodos, $desconto) {
            $pedido = Pedido::findOrFail($pedidoId);

            if ($pedido->isFinalizado()) {
                throw new \Exception('Pedido já foi finalizado');
            }

            if ($pedido->isCancelado()) {
                throw new \Exception('Pedido foi cancelado');
            }

            $valorTotal = $this->calcularValorTotal($pedido, $desconto);
            $valorPago = array_sum(array_column($metodos, 'valor'));

            if (abs($valorPago - $valorTotal) > 0.01) {
                throw new \Exception('A soma dos valores não corresponde ao total do pedido');
            }

            $pagamento = Pagamento::create([
                'pedido_id' => $pedidoId,
                'user_id' => $userId,
                'valor_total' => $valorTotal,
                'metodo_pagamento' => 'multiplo',
                'valor_pago' => $valorPago,
                'troco' => 0.00,
                'desconto' => $desconto,
            ]);

            foreach ($metodos as $metodo) {
                PagamentoDetalhe::create([
                    'pagamento_id' => $pagamento->id,
                    'metodo' => $metodo['metodo'],
                    'valor' => $metodo['valor'],
                ]);
            }

            $this->finalizarPedido($pedido);

            return $pagamento->load('detalhes');
        });
    }

    /**
     * Calcula o valor total do pedido com taxa de serviço e desconto
     */
    protected function calcularValorTotal(Pedido $pedido, float $desconto = 0.00): float
    {
        $subtotal = $pedido->total;
        $taxaServico = Configuracao::obter('taxa_servico', 0);

        $valorTaxaServico = ($subtotal * $taxaServico) / 100;
        $totalComTaxa = $subtotal + $valorTaxaServico;
        $totalFinal = max(0, $totalComTaxa - $desconto);

        return round($totalFinal, 2);
    }

    /**
     * Finaliza o pedido e libera a mesa
     */
    protected function finalizarPedido(Pedido $pedido): void
    {
        $pedido->update([
            'status' => 'finalizado',
            'data_finalizacao' => now(),
        ]);

        $pedido->mesa->update(['status' => 'disponivel']);
    }

    /**
     * Calcula o resumo do pagamento (para exibição antes de confirmar)
     */
    public function calcularResumo(int $pedidoId, float $desconto = 0.00): array
    {
        $pedido = Pedido::with('itens.produto')->findOrFail($pedidoId);
        $taxaServico = Configuracao::obter('taxa_servico', 0);

        $subtotal = $pedido->total;
        $valorTaxaServico = ($subtotal * $taxaServico) / 100;
        $totalComTaxa = $subtotal + $valorTaxaServico;
        $totalFinal = max(0, $totalComTaxa - $desconto);

        return [
            'subtotal' => $subtotal,
            'taxa_servico_percentual' => $taxaServico,
            'taxa_servico_valor' => $valorTaxaServico,
            'desconto' => $desconto,
            'total' => $totalFinal,
            'itens' => $pedido->itens->map(function ($item) {
                return [
                    'produto' => $item->produto->nome,
                    'quantidade' => $item->quantidade,
                    'preco_unitario' => $item->preco_unitario,
                    'subtotal' => $item->subtotal,
                ];
            }),
        ];
    }
}
