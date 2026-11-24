<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\CarrinhoItem;
use App\Models\CarrinhoItemSabor;
use App\Models\ConfiguracaoDelivery;
use App\Models\Produto;
use App\Models\Sabor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Support\LojaValidation;

class CarrinhoController extends Controller
{
    public function index()
    {
        $itens = $this->getItens()->get();
        $subtotal = $itens->sum(fn($item) => $item->calcularSubtotal());

        $config = ConfiguracaoDelivery::obter();
        $taxaEntrega = $config->calcularTaxaEntrega($subtotal);
        $total = $subtotal + $taxaEntrega;

        return view('cliente.carrinho.index', compact('itens', 'subtotal', 'taxaEntrega', 'total', 'config'));
    }

    public function adicionar(Request $request)
    {
        $request->validate([
            'produto_id' => ['required', LojaValidation::exists('produtos')],
            'produto_tamanho_id' => ['nullable', LojaValidation::exists('produto_tamanhos')],
            'quantidade' => 'required|integer|min:1|max:10',
            'sabores' => 'nullable|array|max:4',
            'sabores.*' => LojaValidation::exists('sabores'),
            'observacoes' => 'nullable|string|max:255',
        ]);

        $produto = Produto::findOrFail($request->produto_id);

        if (!$produto->ativo || !$produto->categoria?->ativo) {
            return back()->with('error', 'Este produto não está disponível no momento.');
        }

        $tamanho = null;
        if ($request->filled('produto_tamanho_id')) {
            $tamanho = $produto->tamanhos()
                ->where('ativo', true)
                ->whereKey($request->produto_tamanho_id)
                ->first();

            if (!$tamanho) {
                return back()->with('error', 'O tamanho selecionado não pertence a este produto ou não está disponível.');
            }
        } elseif ($produto->temTamanhos()) {
            return back()->with('error', 'Selecione um tamanho para este produto.');
        }

        $sabores = collect();
        if ($request->filled('sabores')) {
            if (!$tamanho || count($request->sabores) > $tamanho->max_sabores) {
                return back()->with('error', 'A quantidade de sabores não é válida para o tamanho selecionado.');
            }

            $sabores = Sabor::whereIn('id', $request->sabores)
                ->where('categoria_id', $produto->categoria_id)
                ->where('ativo', true)
                ->get();

            if ($sabores->count() !== count(array_unique($request->sabores))) {
                return back()->with('error', 'Um ou mais sabores não estão disponíveis para este produto.');
            }
        }

        $precoUnitario = $produto->preco;

        if ($tamanho) {
            $precoUnitario = $tamanho->preco;

            if ($sabores->isNotEmpty()) {
                $precoMaiorSabor = $tamanho->preco;

                foreach ($sabores as $sabor) {
                    $precoSabor = match($tamanho->nome) {
                        'P' => $sabor->preco_p,
                        'M' => $sabor->preco_m,
                        'G' => $sabor->preco_g,
                        'GG' => $sabor->preco_gg,
                        default => null,
                    };
                    if ($precoSabor !== null && $precoSabor > 0) {
                        $precoMaiorSabor = max($precoMaiorSabor, $precoSabor);
                    }
                }

                $precoUnitario = $precoMaiorSabor;
            }
        }

        DB::beginTransaction();

        try {
            $carrinhoItem = CarrinhoItem::create([
                'cliente_id' => Auth::guard('cliente')->id(),
                'session_id' => $this->getSessionId(),
                'produto_id' => $produto->id,
                'produto_tamanho_id' => $request->produto_tamanho_id,
                'quantidade' => $request->quantidade,
                'preco_unitario' => $precoUnitario,
                'observacoes' => $request->observacoes,
            ]);

            if ($request->sabores) {
                foreach ($request->sabores as $saborId) {
                    CarrinhoItemSabor::create([
                        'carrinho_item_id' => $carrinhoItem->id,
                        'sabor_id' => $saborId,
                    ]);
                }
            }

            DB::commit();

            // Se for requisição AJAX, retornar JSON
            if ($request->ajax() || $request->wantsJson()) {
                $cartCount = $this->getItens()->sum('quantidade');
                $cartTotal = $this->getItens()->get()->sum(fn($item) => $item->preco_unitario * $item->quantidade);

                return response()->json([
                    'success' => true,
                    'message' => 'Produto adicionado ao carrinho!',
                    'cart_count' => $cartCount,
                    'cart_total' => $cartTotal,
                ]);
            }

            return redirect()->route('cliente.carrinho.index')->with('success', 'Produto adicionado ao carrinho!');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao adicionar produto ao carrinho.',
                ], 500);
            }

            return back()->with('error', 'Erro ao adicionar produto ao carrinho.');
        }
    }

    public function getCartInfo()
    {
        $cartCount = $this->getItens()->sum('quantidade');
        $cartTotal = $this->getItens()->get()->sum(fn($item) => $item->preco_unitario * $item->quantidade);

        return response()->json([
            'cart_count' => $cartCount,
            'cart_total' => $cartTotal,
        ]);
    }

    public function getCartData()
    {
        $itens = $this->getItens()->get();
        $subtotal = $itens->sum(fn($item) => $item->calcularSubtotal());

        $config = ConfiguracaoDelivery::obter();
        $taxaEntrega = $config->calcularTaxaEntrega($subtotal);
        $total = $subtotal + $taxaEntrega;

        $itensData = $itens->map(function($item) {
            $saboresNomes = $item->sabores->map(fn($s) => $s->sabor->nome)->join(', ');
            return [
                'id' => $item->id,
                'produto_nome' => $item->produto->nome,
                'produto_imagem' => $item->produto->imagem ? asset('storage/' . $item->produto->imagem) : null,
                'tamanho_nome' => $item->produtoTamanho?->nome,
                'sabores' => $saboresNomes,
                'quantidade' => $item->quantidade,
                'preco_unitario' => $item->preco_unitario,
                'subtotal' => $item->calcularSubtotal(),
                'observacoes' => $item->observacoes,
            ];
        });

        return response()->json([
            'success' => true,
            'itens' => $itensData,
            'subtotal' => $subtotal,
            'taxa_entrega' => $taxaEntrega,
            'total' => $total,
            'cart_count' => $itens->sum('quantidade'),
            'config' => [
                'pedido_minimo' => $config->pedido_minimo,
                'taxa_entrega' => $config->taxa_entrega,
                'entrega_gratis_acima' => $config->entrega_gratis_acima,
                'delivery_aberto' => $config->isDeliveryAberto(),
                'tempo_medio_preparo' => $config->tempo_medio_preparo,
            ],
        ]);
    }

    public function atualizar(CarrinhoItem $item, Request $request)
    {
        $this->authorizeCartItem($item);

        $request->validate([
            'quantidade' => 'required|integer|min:1|max:10',
        ]);

        $item->update([
            'quantidade' => $request->quantidade,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return $this->getCartData();
        }

        return back()->with('success', 'Quantidade atualizada!');
    }

    public function remover(CarrinhoItem $item, Request $request)
    {
        $this->authorizeCartItem($item);
        $item->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return $this->getCartData();
        }

        return back()->with('success', 'Item removido do carrinho!');
    }

    public function limpar(Request $request)
    {
        $this->getItens()->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Carrinho limpo!',
                'cart_count' => 0,
                'cart_total' => 0,
            ]);
        }

        return back()->with('success', 'Carrinho limpo!');
    }

    private function getSessionId(): string
    {
        if (!session()->has('carrinho_session_id')) {
            session(['carrinho_session_id' => uniqid('carrinho_', true)]);
        }
        return session('carrinho_session_id');
    }

    private function getItens()
    {
        $query = CarrinhoItem::with(['produto', 'produtoTamanho', 'sabores.sabor']);

        if (Auth::guard('cliente')->check()) {
            return $query->where('cliente_id', Auth::guard('cliente')->id());
        }

        return $query->where('session_id', $this->getSessionId());
    }

    private function authorizeCartItem(CarrinhoItem $item): void
    {
        if (Auth::guard('cliente')->check()) {
            abort_unless((int) $item->cliente_id === (int) Auth::guard('cliente')->id(), 404);
            return;
        }

        abort_unless(
            $item->cliente_id === null
                && hash_equals((string) $item->session_id, $this->getSessionId()),
            404
        );
    }
}
