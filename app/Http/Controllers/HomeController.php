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

        // Processar filtros de data
        $dataInicio = request()->get('data_inicio')
            ? \Carbon\Carbon::parse(request()->get('data_inicio'))->startOfDay()
            : today()->startOfDay();
        $dataFim = request()->get('data_fim')
            ? \Carbon\Carbon::parse(request()->get('data_fim'))->endOfDay()
            : today()->endOfDay();

        // Estatísticas gerais filtradas por período
        // Faturamento líquido (sem taxa de serviço do garçom - 10% é repassado)
        $faturamentoBruto = Pagamento::whereBetween('created_at', [$dataInicio, $dataFim])
            ->where('status', 'aprovado')
            ->sum('total');
        $taxaServicoTotal = Pagamento::whereBetween('created_at', [$dataInicio, $dataFim])
            ->where('status', 'aprovado')
            ->sum('valor_taxa_servico');

        $stats = [
            'pedidos_periodo' => Pedido::whereBetween('created_at', [$dataInicio, $dataFim])
                ->count(),
            'mesas_ocupadas' => Mesa::where('status', 'ocupada')->count(),
            'total_hoje' => $faturamentoBruto - $taxaServicoTotal, // Faturamento líquido
            'taxa_servico' => $taxaServicoTotal, // Para exibir separadamente se necessário
            'produtos_ativos' => Produto::where('ativo', true)->count(),
        ];

        // Pedidos recentes (mostra mais com rolagem) - filtrados por período
        $pedidos_recentes = Pedido::with(['mesa', 'user', 'itens.produto'])
            ->whereBetween('created_at', [$dataInicio, $dataFim])
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

        return view('home', [
            'stats' => $stats,
            'pedidos_recentes' => $pedidos_recentes,
            'mesas_ocupadas' => $mesas_ocupadas,
            'user' => $user,
            'dataInicio' => $dataInicio->format('Y-m-d'),
            'dataFim' => $dataFim->format('Y-m-d')
        ]);
    }
}
