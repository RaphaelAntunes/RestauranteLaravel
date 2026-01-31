<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\CarrinhoItem;
use App\Models\ConfiguracaoDelivery;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\PedidoItemSabor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PedidoOnlineController extends Controller
{
    public function checkout()
    {
        $cliente = Auth::guard('cliente')->user();
        $itens = $this->getCarrinhoItens()->get();

        if ($itens->isEmpty()) {
            return redirect()->route('cliente.cardapio')->with('error', 'Seu carrinho está vazio!');
        }

        $subtotal = $itens->sum(fn($item) => $item->calcularSubtotal());
        $config = ConfiguracaoDelivery::obter();

        $enderecos = $cliente->enderecos;

        return view('cliente.pedido.checkout', compact('itens', 'subtotal', 'config', 'enderecos'));
    }

    public function finalizarPedido(Request $request)
    {
        $request->validate([
            'tipo_pedido' => 'required|in:delivery,retirada',
            'cliente_endereco_id' => 'required_if:tipo_pedido,delivery|nullable|exists:cliente_enderecos,id',
            'observacoes' => 'nullable|string|max:500',
            'observacoes_entrega' => 'nullable|string|max:500',
        ], [
            'tipo_pedido.required' => 'Selecione o tipo de pedido.',
            'cliente_endereco_id.required_if' => 'Selecione um endereço de entrega.',
        ]);

        $cliente = Auth::guard('cliente')->user();
        $itens = $this->getCarrinhoItens()->get();

        if ($itens->isEmpty()) {
            return redirect()->route('cliente.cardapio')->with('error', 'Seu carrinho está vazio!');
        }

        $config = ConfiguracaoDelivery::obter();

        if (!$config->isDeliveryAberto() && $request->tipo_pedido === 'delivery') {
            return back()->with('error', 'Delivery não está disponível no momento.');
        }

        $subtotal = $itens->sum(fn($item) => $item->calcularSubtotal());

        if ($subtotal < $config->pedido_minimo) {
            return back()->with('error', 'O pedido mínimo é de R$ ' . number_format($config->pedido_minimo, 2, ',', '.'));
        }

        $taxaEntrega = ($request->tipo_pedido === 'delivery') ? $config->calcularTaxaEntrega($subtotal) : 0;

        DB::beginTransaction();

        try {
            $numeroPedido = $this->gerarNumeroPedido();

            $pedido = Pedido::create([
                'cliente_id' => $cliente->id,
                'tipo_pedido' => $request->tipo_pedido,
                'cliente_endereco_id' => $request->cliente_endereco_id,
                'numero_pedido' => $numeroPedido,
                'status' => 'aberto',
                'taxa_entrega' => $taxaEntrega,
                'total' => 0,
                'observacoes' => $request->observacoes,
                'observacoes_entrega' => $request->observacoes_entrega,
                'data_abertura' => now(),
                'previsao_entrega' => now()->addMinutes($config->tempo_medio_preparo),
            ]);

            foreach ($itens as $item) {
                $pedidoItem = PedidoItem::create([
                    'pedido_id' => $pedido->id,
                    'produto_id' => $item->produto_id,
                    'produto_nome' => $item->produto->nome,
                    'produto_tamanho_id' => $item->produto_tamanho_id,
                    'quantidade' => $item->quantidade,
                    'preco_unitario' => $item->preco_unitario,
                    'subtotal' => $item->calcularSubtotal(),
                    'observacoes' => $item->observacoes,
                    'status' => 'pendente',
                ]);

                foreach ($item->sabores as $sabor) {
                    PedidoItemSabor::create([
                        'pedido_item_id' => $pedidoItem->id,
                        'sabor_id' => $sabor->sabor_id,
                    ]);
                }
            }

            $pedido->calcularTotal();

            // Criar mesa virtual para o pedido online
            Mesa::criarParaPedidoOnline($pedido);

            $this->limparCarrinho();

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pedido realizado com sucesso!',
                    'pedido_id' => $pedido->id,
                    'numero_pedido' => $pedido->numero_pedido,
                    'redirect_url' => route('cliente.pedido.acompanhar', $pedido->id),
                ]);
            }

            return redirect()->route('cliente.pedido.acompanhar', $pedido->id)
                ->with('success', 'Pedido realizado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erro ao finalizar pedido: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao finalizar pedido. Tente novamente.',
                ], 500);
            }

            return back()->with('error', 'Erro ao finalizar pedido. Tente novamente.');
        }
    }

    public function getStatusPedido(Pedido $pedido)
    {
        if ($pedido->cliente_id !== Auth::guard('cliente')->id()) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        $pedido->load(['itens.produto', 'itens.sabores.sabor', 'clienteEndereco']);

        $statusLabels = [
            'aberto' => 'Pedido Recebido',
            'em_preparo' => 'Em Preparo',
            'pronto' => 'Pronto para Entrega',
            'saiu_entrega' => 'Saiu para Entrega',
            'entregue' => 'Entregue',
            'fechado' => 'Concluído',
            'cancelado' => 'Cancelado',
        ];

        $statusProgress = [
            'aberto' => 1,
            'em_preparo' => 2,
            'pronto' => 3,
            'saiu_entrega' => 4,
            'entregue' => 5,
            'fechado' => 5,
            'cancelado' => 0,
        ];

        return response()->json([
            'success' => true,
            'pedido' => [
                'id' => $pedido->id,
                'numero_pedido' => $pedido->numero_pedido,
                'status' => $pedido->status,
                'status_label' => $statusLabels[$pedido->status] ?? $pedido->status,
                'status_progress' => $statusProgress[$pedido->status] ?? 0,
                'tipo_pedido' => $pedido->tipo_pedido,
                'total' => $pedido->total,
                'taxa_entrega' => $pedido->taxa_entrega,
                'previsao_entrega' => $pedido->previsao_entrega?->format('H:i'),
                'data_abertura' => $pedido->data_abertura?->format('d/m/Y H:i'),
                'endereco' => $pedido->clienteEndereco ? [
                    'logradouro' => $pedido->clienteEndereco->logradouro,
                    'numero' => $pedido->clienteEndereco->numero,
                    'bairro' => $pedido->clienteEndereco->bairro,
                    'complemento' => $pedido->clienteEndereco->complemento,
                ] : null,
                'itens' => $pedido->itens->map(fn($item) => [
                    'nome' => $item->produto_nome,
                    'quantidade' => $item->quantidade,
                    'tamanho' => $item->produtoTamanho?->nome,
                    'sabores' => $item->sabores->map(fn($s) => $s->sabor->nome)->join(', '),
                    'subtotal' => $item->subtotal,
                ]),
            ],
        ]);
    }

    public function meusPedidos()
    {
        $cliente = Auth::guard('cliente')->user();

        $pedidos = Pedido::where('cliente_id', $cliente->id)
            ->with(['itens.produto', 'clienteEndereco'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('cliente.pedido.meus-pedidos', compact('pedidos'));
    }

    public function getCheckoutData()
    {
        $cliente = Auth::guard('cliente')->user();

        if (!$cliente) {
            return response()->json([
                'success' => false,
                'requires_auth' => true,
                'message' => 'Faça login para continuar',
            ]);
        }

        $enderecos = $cliente->enderecos->map(fn($e) => [
            'id' => $e->id,
            'label' => $e->logradouro . ', ' . $e->numero . ($e->complemento ? ' - ' . $e->complemento : '') . ' - ' . $e->bairro,
            'padrao' => $e->padrao,
        ]);

        $config = ConfiguracaoDelivery::obter();

        return response()->json([
            'success' => true,
            'enderecos' => $enderecos,
            'config' => [
                'delivery_aberto' => $config->isDeliveryAberto(),
                'tempo_medio_preparo' => $config->tempo_medio_preparo,
                'horario_inicio' => $config->horario_inicio,
                'horario_fim' => $config->horario_fim,
            ],
        ]);
    }

    public function acompanhar(Pedido $pedido)
    {
        if ($pedido->cliente_id !== Auth::guard('cliente')->id()) {
            abort(403, 'Você não tem permissão para acessar este pedido.');
        }

        $pedido->load(['itens.produto', 'itens.sabores.sabor', 'clienteEndereco']);

        return view('cliente.pedido.acompanhar', compact('pedido'));
    }

    private function gerarNumeroPedido(): string
    {
        $data = now()->format('Ymd');
        $prefixo = "PED-ONLINE-{$data}-";

        $ultimoPedido = Pedido::where('numero_pedido', 'like', "{$prefixo}%")
            ->orderByRaw('CAST(SUBSTRING(numero_pedido, ?) AS UNSIGNED) DESC', [strlen($prefixo) + 1])
            ->first();

        if ($ultimoPedido) {
            // Extrai o número após o último hífen
            $partes = explode('-', $ultimoPedido->numero_pedido);
            $ultimoNumero = (int) end($partes);
            $novoNumero = $ultimoNumero + 1;
        } else {
            $novoNumero = 1;
        }

        return $prefixo . str_pad($novoNumero, 4, '0', STR_PAD_LEFT);
    }

    private function getCarrinhoItens()
    {
        $query = CarrinhoItem::with(['produto', 'produtoTamanho', 'sabores.sabor']);

        if (Auth::guard('cliente')->check()) {
            return $query->where('cliente_id', Auth::guard('cliente')->id());
        }

        return $query->where('session_id', session('carrinho_session_id'));
    }

    private function limparCarrinho()
    {
        $this->getCarrinhoItens()->delete();
        session()->forget('carrinho_session_id');
    }
}
