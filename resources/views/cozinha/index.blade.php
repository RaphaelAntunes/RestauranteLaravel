@extends('layouts.app')

@section('title', 'Painel da Cozinha')

@push('styles')
<style>
    .pedido-card {
        transition: all 0.3s ease;
    }
    .pedido-card:hover {
        transform: translateY(-2px);
    }
    .novo-pedido {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: .7;
        }
    }
</style>
@endpush

@section('content')
<div class="db-kds space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Painel da Cozinha (KDS)</h1>
        <div class="flex items-center space-x-2">
            <span id="ultima-atualizacao" class="text-sm text-gray-600 dark:text-gray-400"></span>
            <span id="status-conexao" class="h-3 w-3 rounded-full bg-green-500"></span>
        </div>
    </div>

    <!-- Grid de Pedidos -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Novos Pedidos (Aguardando) -->
        <div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <span class="bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 px-3 py-1 rounded-full mr-2 border border-yellow-200 dark:border-yellow-800">
                    <span id="count-novos">{{ $novosPedidos->count() }}</span>
                </span>
                Aguardando
            </h2>
            <div id="novos-pedidos" class="space-y-4">
                @foreach($novosPedidos as $pedido)
                    @include('cozinha.partials.pedido-card', ['pedido' => $pedido, 'tipo' => 'novo'])
                @endforeach
            </div>
        </div>

        <!-- Em Preparo -->
        <div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 px-3 py-1 rounded-full mr-2 border border-blue-200 dark:border-blue-800">
                    <span id="count-preparo">{{ $pedidosEmPreparo->count() }}</span>
                </span>
                Em Preparo
            </h2>
            <div id="em-preparo" class="space-y-4">
                @foreach($pedidosEmPreparo as $pedido)
                    @include('cozinha.partials.pedido-card', ['pedido' => $pedido, 'tipo' => 'preparo'])
                @endforeach
            </div>
        </div>

        <!-- Prontos -->
        <div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <span class="bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 px-3 py-1 rounded-full mr-2 border border-green-200 dark:border-green-800">
                    <span id="count-prontos">{{ $pedidosProntos->count() }}</span>
                </span>
                Prontos
            </h2>
            <div id="prontos" class="space-y-4">
                @foreach($pedidosProntos as $pedido)
                    @include('cozinha.partials.pedido-card', ['pedido' => $pedido, 'tipo' => 'pronto'])
                @endforeach
            </div>
        </div>

        <!-- Saiu p/ Entrega -->
        <div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <span class="bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 px-3 py-1 rounded-full mr-2 border border-purple-200 dark:border-purple-800">
                    <span id="count-saiu-entrega">{{ $pedidosSaiuEntrega->count() }}</span>
                </span>
                Saiu p/ Entrega
            </h2>
            <div id="saiu-entrega" class="space-y-4">
                @foreach($pedidosSaiuEntrega as $pedido)
                    @include('cozinha.partials.pedido-card', ['pedido' => $pedido, 'tipo' => 'saiu_entrega'])
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Atualização automática a cada 5 segundos
let atualizando = false;

const cozinhaRoutes = {
    atualizar: @json(route('cozinha.atualizar', ['loja' => $lojaAtual->slug])),
    iniciar: @json(route('cozinha.iniciar', ['loja' => $lojaAtual->slug, 'pedido' => '__ID__'])),
    pronto: @json(route('cozinha.pronto', ['loja' => $lojaAtual->slug, 'pedido' => '__ID__'])),
    entregar: @json(route('cozinha.entregar', ['loja' => $lojaAtual->slug, 'pedido' => '__ID__'])),
    entregue: @json(route('cozinha.entregue', ['loja' => $lojaAtual->slug, 'pedido' => '__ID__'])),
};

function cozinhaRoute(template, pedidoId) {
    return template.replace('__ID__', encodeURIComponent(pedidoId));
}

function cozinhaJson(response) {
    return response.json().then(data => {
        if (!response.ok || data.success === false) {
            throw new Error(data.message || `Falha na operação (${response.status})`);
        }

        return data;
    });
}

function atualizarPedidos() {
    if (atualizando) return;

    atualizando = true;
    document.getElementById('status-conexao').classList.remove('bg-green-500');
    document.getElementById('status-conexao').classList.add('bg-yellow-500');

    fetch(cozinhaRoutes.atualizar)
        .then(response => response.json())
        .then(data => {
            // Atualizar contadores
            document.getElementById('count-novos').textContent = data.novos.length;
            document.getElementById('count-preparo').textContent = data.em_preparo.length;
            document.getElementById('count-prontos').textContent = data.prontos.length;
            document.getElementById('count-saiu-entrega').textContent = data.saiu_entrega.length;

            // Atualizar HTML dos pedidos
            if (data.html_novos !== undefined) {
                document.getElementById('novos-pedidos').innerHTML = data.html_novos;
            }
            if (data.html_preparo !== undefined) {
                document.getElementById('em-preparo').innerHTML = data.html_preparo;
            }
            if (data.html_prontos !== undefined) {
                document.getElementById('prontos').innerHTML = data.html_prontos;
            }
            if (data.html_saiu_entrega !== undefined) {
                document.getElementById('saiu-entrega').innerHTML = data.html_saiu_entrega;
            }

            // Atualizar timestamp
            const agora = new Date();
            document.getElementById('ultima-atualizacao').textContent =
                'Atualizado às ' + agora.toLocaleTimeString('pt-BR');

            // Status de conexão
            document.getElementById('status-conexao').classList.remove('bg-yellow-500', 'bg-red-500');
            document.getElementById('status-conexao').classList.add('bg-green-500');

            atualizando = false;
        })
        .catch(error => {
            console.error('Erro ao atualizar:', error);
            document.getElementById('status-conexao').classList.remove('bg-green-500', 'bg-yellow-500');
            document.getElementById('status-conexao').classList.add('bg-red-500');
            atualizando = false;
        });
}

// Atualizar a cada 5 segundos
setInterval(atualizarPedidos, 5000);

// Atualizar imediatamente ao carregar
atualizarPedidos();

// Funções para gerenciar pedidos
function iniciarPreparo(pedidoId) {
    // Mostrar loading
    showLoading('Iniciando preparo...');

    fetch(cozinhaRoute(cozinhaRoutes.iniciar, pedidoId), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(cozinhaJson)
    .then(data => {
        if (data.success) {
            atualizarPedidos();
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert(error.message || 'Não foi possível iniciar o preparo.');
    })
    .finally(() => {
        // Esconder loading
        hideLoading();
    });
}

function marcarPronto(pedidoId) {
    // Mostrar loading
    showLoading('Marcando pedido como pronto...');

    fetch(cozinhaRoute(cozinhaRoutes.pronto, pedidoId), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(cozinhaJson)
    .then(data => {
        if (data.success) {
            atualizarPedidos();
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert(error.message || 'Não foi possível marcar o pedido como pronto.');
    })
    .finally(() => {
        // Esconder loading
        hideLoading();
    });
}

function entregar(pedidoId) {
    // Mostrar loading
    showLoading('Enviando para entrega...');

    fetch(cozinhaRoute(cozinhaRoutes.entregar, pedidoId), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(cozinhaJson)
    .then(data => {
        if (data.success) {
            atualizarPedidos();
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert(error.message || 'Não foi possível enviar o pedido para entrega.');
    })
    .finally(() => {
        // Esconder loading
        hideLoading();
    });
}

function marcarEntregue(pedidoId) {
    // Mostrar loading
    showLoading('Finalizando entrega...');

    fetch(cozinhaRoute(cozinhaRoutes.entregue, pedidoId), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(cozinhaJson)
    .then(data => {
        if (data.success) {
            atualizarPedidos();
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert(error.message || 'Não foi possível finalizar a entrega.');
    })
    .finally(() => {
        // Esconder loading
        hideLoading();
    });
}
</script>
@endpush
