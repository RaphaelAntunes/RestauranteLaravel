<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprovante - Pagamento #{{ $pagamento->id }}</title>
    <style>
        @media print {
            @page {
                margin: 0;
                size: 80mm auto;
            }
            body {
                margin: 0;
                padding: 0;
            }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 20px;
            line-height: 1.4;
            width: 80mm;
            margin: 0 auto;
            padding: 5mm;
            background: white;
            color: #000;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .center {
            text-align: center;
        }

        .bold {
            font-weight: 900;
        }

        .header {
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 30px;
            margin-bottom: 3px;
            font-weight: 900;
        }

        .header .status {
            font-size: 22px;
            font-weight: 900;
            margin-top: 5px;
            padding: 5px;
            border: 2px solid #000;
        }

        .info-section {
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px dashed #000;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }

        .divider-solid {
            border-top: 2px solid #000;
            margin: 10px 0;
        }

        .item {
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 1px dotted #ccc;
        }

        .item:last-child {
            border-bottom: none;
        }

        .item-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }

        .item-qty {
            font-weight: 900;
        }

        .item-details {
            font-size: 16px;
            margin-left: 15px;
            margin-top: 2px;
        }

        .pedido-section {
            margin-top: 8px;
            padding: 5px;
            background: #f5f5f5;
        }

        .pedido-header {
            font-size: 16px;
            font-weight: 900;
            margin-bottom: 5px;
            padding-bottom: 3px;
            border-bottom: 1px solid #999;
        }

        .totals {
            margin-top: 10px;
            padding-top: 10px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .total-row.desconto {
            font-size: 16px;
        }

        .total-final {
            font-size: 26px;
            font-weight: 900;
            padding: 8px 0;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            margin: 8px 0;
        }

        .payment-info {
            margin: 10px 0;
            padding: 8px;
            border: 1px solid #000;
        }

        .payment-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }

        .obs-section {
            margin: 10px 0;
            padding: 8px;
            background: #f5f5f5;
            border: 1px dashed #000;
        }

        .footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px dashed #000;
            text-align: center;
            font-size: 16px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header center">
        <h1>{{ config('app.name', 'RESTAURANTE') }}</h1>
        <p>COMPROVANTE DE PAGAMENTO</p>
        <p>{{ $pagamento->created_at->format('d/m/Y H:i:s') }}</p>
        <div class="status">✓ PAGAMENTO APROVADO</div>
    </div>

    <!-- Informações Básicas -->
    <div class="info-section">
        <div class="info-row">
            <span>Comprovante:</span>
            <span class="bold">#{{ $pagamento->id }}</span>
        </div>
        <div class="info-row">
            <span>Mesa:</span>
            <span class="bold">{{ $pagamento->mesa->numero }}</span>
        </div>
        <div class="info-row">
            <span>Atendente:</span>
            <span class="bold">{{ $pagamento->user->nome }}</span>
        </div>
    </div>

    <!-- Itens Consumidos -->
    <div class="divider-solid"></div>
    <div class="center bold" style="margin-bottom: 10px;">ITENS CONSUMIDOS</div>

    @foreach($pagamento->detalhes as $detalhe)
        @if($pagamento->detalhes->count() > 1)
        <div class="pedido-section">
            <div class="pedido-header">{{ $detalhe->pedido->numero_pedido }}</div>
        @endif

        @foreach($detalhe->pedido->itens as $item)
        <div class="item">
            <div class="item-header">
                <div>
                    <span class="item-qty">{{ $item->quantidade }}x</span>
                    {{ $item->produto->nome }}
                </div>
                <div class="bold">
                    {{ number_format($item->subtotal, 2, ',', '.') }}
                </div>
            </div>

            @if($item->produtoTamanho)
            <div class="item-details">
                Tamanho: <strong>{{ $item->produtoTamanho->nome }}</strong>
            </div>
            @endif

            @if($item->sabores->count() > 0)
            <div class="item-details">
                Sabores: {{ $item->sabores->pluck('sabor.nome')->join(', ') }}
            </div>
            @endif

            @if($item->observacoes)
            <div class="item-details">
                Obs: {{ $item->observacoes }}
            </div>
            @endif
        </div>
        @endforeach

        @if($pagamento->detalhes->count() > 1)
        </div>
        @endif
    @endforeach

    <!-- Totais -->
    <div class="totals">
        <div class="total-row">
            <span>Subtotal:</span>
            <span class="bold">R$ {{ number_format($pagamento->subtotal, 2, ',', '.') }}</span>
        </div>
        @if($pagamento->desconto > 0)
        <div class="total-row desconto">
            <span>Desconto:</span>
            <span>- R$ {{ number_format($pagamento->valor_desconto, 2, ',', '.') }}</span>
        </div>
        @endif
        @if($pagamento->acrescimo > 0)
        <div class="total-row desconto">
            <span>Acrescimo:</span>
            <span>+ R$ {{ number_format($pagamento->valor_acrescimo, 2, ',', '.') }}</span>
        </div>
        @endif
        <div class="total-row total-final">
            <span>TOTAL:</span>
            <span>R$ {{ number_format($pagamento->total, 2, ',', '.') }}</span>
        </div>
    </div>

    <!-- Forma de Pagamento -->
    <div class="payment-info">
        <div class="payment-row">
            <span>Forma de Pagamento:</span>
            <span class="bold">{{ strtoupper(str_replace('_', ' ', $pagamento->forma_pagamento)) }}</span>
        </div>
        <div class="payment-row">
            <span>Valor Pago:</span>
            <span class="bold">R$ {{ number_format($pagamento->valor_pago, 2, ',', '.') }}</span>
        </div>
        @if($pagamento->troco > 0)
        <div class="payment-row" style="border-top: 1px solid #999; padding-top: 4px; margin-top: 4px;">
            <span>Troco:</span>
            <span class="bold">R$ {{ number_format($pagamento->troco, 2, ',', '.') }}</span>
        </div>
        @endif
    </div>

    @if($pagamento->observacoes)
    <div class="obs-section">
        <div class="bold" style="margin-bottom: 3px;">Observações:</div>
        <div>{{ $pagamento->observacoes }}</div>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p class="bold" style="font-size: 18px; margin-bottom: 5px;">Obrigado pela preferência!</p>
        <p>Volte sempre!</p>
        <div style="margin-top: 8px; padding-top: 8px; border-top: 1px solid #999;">
            <p>{{ config('app.name', 'Sistema Restaurante') }}</p>
        </div>
    </div>

    <!-- Botões (não aparece na impressão) -->
    <div class="no-print" style="margin-top: 20px; text-align: center; padding: 10px; border-top: 2px solid #000;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px; cursor: pointer; background: #4CAF50; color: white; border: none; border-radius: 5px;">
            🖨️ Imprimir
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 14px; cursor: pointer; background: #f44336; color: white; border: none; border-radius: 5px; margin-left: 10px;">
            ✖️ Fechar
        </button>
    </div>

    <script>
        // Auto-imprimir ao carregar a página
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
