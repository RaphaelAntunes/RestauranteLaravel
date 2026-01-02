<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Mesa;
use App\Models\Produto;
use App\Models\User;
use App\Models\Pagamento;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Dashboard principal
     */
    public function index()
    {
        $user = auth()->user();

        // Redirecionar baseado na role do usuário
        if ($user->isCozinha()) {
            return redirect()->route('cozinha.index');
        }

        // Estatísticas gerais
        $stats = [
            'pedidos_abertos' => Pedido::whereIn('status', ['aberto', 'em_preparo'])->count(),
            'mesas_ocupadas' => Mesa::where('status', 'ocupada')->count(),
            'total_hoje' => Pagamento::whereDate('created_at', today())->sum('valor_total'),
            'produtos_ativos' => Produto::where('ativo', true)->count(),
        ];

        // Pedidos recentes (mostra mais com rolagem)
        $pedidos_recentes = Pedido::with(['mesa', 'user', 'itens.produto'])
            ->latest()
            ->limit(20)
            ->get();

        // Mesas ocupadas (mostra todas com rolagem)
        $mesas_ocupadas = Mesa::where('status', 'ocupada')
            ->get()
            ->map(function($mesa) {
                // Carregar pedidos da sessão atual manualmente
                $mesa->pedidos = $mesa->pedidos()
                    ->where(function($query) use ($mesa) {
                        if ($mesa->sessao_atual) {
                            $query->where('sessao_id', $mesa->sessao_atual);
                        } else {
                            $query->whereNull('sessao_id');
                        }
                    })
                    ->whereIn('status', ['aberto', 'em_preparo', 'pronto'])
                    ->with('itens.produto')
                    ->get();

                $mesa->pedidos_count = $mesa->pedidos->count();
                return $mesa;
            });

        return view('home', compact('stats', 'pedidos_recentes', 'mesas_ocupadas', 'user'));
    }
}
