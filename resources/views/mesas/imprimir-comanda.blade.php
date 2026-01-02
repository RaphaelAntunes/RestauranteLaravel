<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comanda - Mesa {{ $mesa->numero }}</title>
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
            border-bottom: 1px dashed #000;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 3px;
            font-weight: 900;
        }

        .header p {
            font-size: 16px;
        }

        .mesa-info {
            margin: 10px 0;
            padding: 8px;
            border: 1px solid #000;
            text-align: center;
        }

        .mesa-numero {
            font-size: 32px;
            font-weight: 900;
        }

        .divider {
            border-top: 1px dashed #000;
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
            margin-left: 20px;
            margin-top: 2px;
        }

        .sabores {
            font-style: italic;
        }

        .obs {
            font-size: 15px;
            margin-top: 2px;
        }

        .totals {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid #000;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .total-final {
            font-size: 26px;
            font-weight: 900;
            padding-top: 5px;
            border-top: 1px solid #000;
            margin-top: 5px;
        }

        .footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px dashed #000;
            text-align: center;
            font-size: 16px;
        }

        .pedido-section {
            margin-top: 10px;
            padding: 5px;
            background: #f5f5f5;
        }

        .pedido-header {
            font-size: 16px;
            margin-bottom: 5px;
            padding-bottom: 3px;
            border-bottom: 1px solid #999;
            font-weight: 900;
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
        <p>COMANDA</p>
        <p>{{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Informações da Mesa -->
    <div class="mesa-info">
        <div class="mesa-numero">MESA {{ $mesa->numero }}</div>
        @if($mesa->localizacao)
        <div>{{ $mesa->localizacao }}</div>
        @endif
        @if($mesa->cliente_nome)
        <div style="margin-top: 8px; padding-top: 8px; border-top: 1px solid #999;">
            <strong>Cliente:</strong> {{ $mesa->cliente_nome }}
        </div>
        @endif
    </div>

    <div class="divider"></div>

    <!-- Itens dos Pedidos -->
    @foreach($pedidos as $pedido)
        @if($pedidos->count() > 1)
        <div class="pedido-section">
            <div class="pedido-header">
                <strong>{{ $pedido->numero_pedido }}</strong> - {{ $pedido->created_at->format('d/m/Y H:i') }}
            </div>
        @endif

        @foreach($pedido->itens as $item)
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
            <div class="item-details sabores">
                Sabores: {{ $item->sabores->pluck('sabor.nome')->join(', ') }}
            </div>
            @endif

            @if($item->observacoes)
            <div class="item-details obs">
                Obs: {{ $item->observacoes }}
            </div>
            @endif
        </div>
        @endforeach

        @if($pedidos->count() > 1)
        </div>
        @endif
    @endforeach

    <!-- Totais -->
    <div class="totals">
        <div class="total-row">
            <span>Total de Pedidos:</span>
            <span class="bold">{{ $pedidos->count() }}</span>
        </div>
        <div class="total-row">
            <span>Total de Itens:</span>
            <span class="bold">{{ $quantidadeItens }}</span>
        </div>
        <div class="total-row total-final">
            <span>TOTAL:</span>
            <span>R$ {{ number_format($totalGeral, 2, ',', '.') }}</span>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Obrigado pela preferência!</p>
    </div>

    <!-- Botão de Impressão (não aparece na impressão) -->
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
            // Aguarda um breve momento para garantir que tudo foi carregado
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
