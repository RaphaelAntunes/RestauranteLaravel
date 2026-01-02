<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pedido;
use App\Models\Mesa;

class LimparPedidosSemSessao extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pedidos:atualizar-sessoes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atualiza pedidos existentes com sessões únicas por mesa e período';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Atualizando pedidos sem sessão...');

        // Buscar pedidos sem sessão_id
        $pedidosSemSessao = Pedido::whereNull('sessao_id')->get();

        if ($pedidosSemSessao->isEmpty()) {
            $this->info('Nenhum pedido sem sessão encontrado.');
            return 0;
        }

        $this->info("Encontrados {$pedidosSemSessao->count()} pedidos sem sessão.");

        // Agrupar por mesa e criar sessões únicas
        $pedidosSemSessao->groupBy('mesa_id')->each(function ($pedidosMesa, $mesaId) {
            // Para cada mesa, agrupar por "sessão" (pedidos próximos no tempo)
            $sessaoAtual = null;
            $ultimaData = null;

            foreach ($pedidosMesa->sortBy('created_at') as $pedido) {
                // Se é o primeiro pedido ou passou mais de 2 horas do último
                if (!$ultimaData || $pedido->created_at->diffInHours($ultimaData) > 2) {
                    // Criar nova sessão
                    $sessaoAtual = uniqid('sessao_', true);
                }

                // Atualizar pedido com sessão
                $pedido->update(['sessao_id' => $sessaoAtual]);
                $ultimaData = $pedido->created_at;
            }

            $this->info("Mesa {$mesaId}: Atualizada");
        });

        // Atualizar sessao_atual das mesas que estão ocupadas
        Mesa::where('status', 'ocupada')->each(function ($mesa) {
            $ultimoPedidoAtivo = $mesa->pedidos()
                ->whereIn('status', ['aberto', 'em_preparo', 'pronto', 'entregue'])
                ->orderBy('created_at', 'desc')
                ->first();

            if ($ultimoPedidoAtivo && $ultimoPedidoAtivo->sessao_id) {
                $mesa->update(['sessao_atual' => $ultimoPedidoAtivo->sessao_id]);
                $this->info("Mesa {$mesa->numero}: Sessão atual atualizada");
            }
        });

        $this->info('✅ Atualização concluída!');
        return 0;
    }
}
