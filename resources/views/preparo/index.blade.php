@extends('layouts.app')

@section('title', 'Painel de Preparo')

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

    /* Layout Mobile - Scroll Horizontal */
    .preparo-container {
        display: flex;
        overflow-x: auto;
        gap: 1rem;
        padding-bottom: 1rem;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
    }

    .preparo-column {
        flex: 0 0 85vw;
        max-width: 85vw;
        scroll-snap-align: start;
    }

    @media (min-width: 768px) {
        .preparo-column {
            flex: 0 0 45vw;
            max-width: 45vw;
        }
    }

    @media (min-width: 1024px) {
        .preparo-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            overflow-x: visible;
        }
        .preparo-column {
            flex: none;
            max-width: none;
        }
    }

    /* Indicadores de scroll (mobile) */
    .scroll-indicator {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.5rem 0;
    }

    .scroll-indicator .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #cbd5e1;
        transition: background 0.3s;
    }

    .scroll-indicator .dot.active {
        background: #3b82f6;
    }

    @media (min-width: 1024px) {
        .scroll-indicator {
            display: none;
        }
    }

    /* Header fixo em mobile */
    .preparo-header {
        position: sticky;
        top: 0;
        z-index: 10;
        background: inherit;
        padding-bottom: 0.5rem;
    }
</style>
@endpush

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="preparo-header flex justify-between items-center bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">Painel de Preparo</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Apenas comidas (sem bebidas)</p>
        </div>
        <div class="flex items-center space-x-2">
            <span id="ultima-atualizacao" class="text-xs text-gray-600 dark:text-gray-400 hidden md:inline"></span>
            <span id="status-conexao" class="h-3 w-3 rounded-full bg-green-500"></span>
        </div>
    </div>

    <!-- Indicadores de scroll (mobile) -->
    <div class="scroll-indicator lg:hidden">
        <span class="dot active" data-column="0"></span>
        <span class="dot" data-column="1"></span>
        <span class="dot" data-column="2"></span>
        <span class="dot" data-column="3"></span>
    </div>

    <!-- Grid de Pedidos -->
    <div class="preparo-container" id="preparo-container">
        <!-- Novos Pedidos (Aguardando) -->
        <div class="preparo-column" data-column="0">
            <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-3 mb-3">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <span class="bg-yellow-500 text-white px-3 py-1 rounded-full mr-2 text-sm font-bold">
                        <span id="count-novos">{{ $novosPedidos->count() }}</span>
                    </span>
                    Aguardando
                </h2>
            </div>
            <div id="novos-pedidos" class="space-y-3">
                @foreach($novosPedidos as $pedido)
                    @include('preparo.partials.pedido-card', ['pedido' => $pedido, 'tipo' => 'novo'])
                @endforeach
            </div>
        </div>

        <!-- Em Preparo -->
        <div class="preparo-column" data-column="1">
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 mb-3">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <span class="bg-blue-500 text-white px-3 py-1 rounded-full mr-2 text-sm font-bold">
                        <span id="count-preparo">{{ $pedidosEmPreparo->count() }}</span>
                    </span>
                    Em Preparo
                </h2>
            </div>
            <div id="em-preparo" class="space-y-3">
                @foreach($pedidosEmPreparo as $pedido)
                    @include('preparo.partials.pedido-card', ['pedido' => $pedido, 'tipo' => 'preparo'])
                @endforeach
            </div>
        </div>

        <!-- Prontos -->
        <div class="preparo-column" data-column="2">
            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3 mb-3">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <span class="bg-green-500 text-white px-3 py-1 rounded-full mr-2 text-sm font-bold">
                        <span id="count-prontos">{{ $pedidosProntos->count() }}</span>
                    </span>
                    Prontos
                </h2>
            </div>
            <div id="prontos" class="space-y-3">
                @foreach($pedidosProntos as $pedido)
                    @include('preparo.partials.pedido-card', ['pedido' => $pedido, 'tipo' => 'pronto'])
                @endforeach
            </div>
        </div>

        <!-- Saiu p/ Entrega -->
        <div class="preparo-column" data-column="3">
            <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-3 mb-3">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <span class="bg-purple-500 text-white px-3 py-1 rounded-full mr-2 text-sm font-bold">
                        <span id="count-saiu-entrega">{{ $pedidosSaiuEntrega->count() }}</span>
                    </span>
                    Saiu p/ Entrega
                </h2>
            </div>
            <div id="saiu-entrega" class="space-y-3">
                @foreach($pedidosSaiuEntrega as $pedido)
                    @include('preparo.partials.pedido-card', ['pedido' => $pedido, 'tipo' => 'saiu_entrega'])
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

function atualizarPedidos() {
    if (atualizando) return;

    atualizando = true;
    document.getElementById('status-conexao').classList.remove('bg-green-500');
    document.getElementById('status-conexao').classList.add('bg-yellow-500');

    fetch('{{ route("preparo.atualizar") }}')
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

// Scroll indicator para mobile
const container = document.getElementById('preparo-container');
const dots = document.querySelectorAll('.scroll-indicator .dot');

if (container && dots.length > 0) {
    container.addEventListener('scroll', () => {
        const scrollLeft = container.scrollLeft;
        const columnWidth = container.querySelector('.preparo-column').offsetWidth;
        const activeColumn = Math.round(scrollLeft / columnWidth);

        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === activeColumn);
        });
    });

    // Clicar nos dots para navegar
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            const columnWidth = container.querySelector('.preparo-column').offsetWidth;
            container.scrollTo({
                left: columnWidth * index,
                behavior: 'smooth'
            });
        });
    });
}

// Funções para gerenciar pedidos (usa as mesmas rotas da cozinha)
function iniciarPreparo(pedidoId) {
    showLoading('Iniciando preparo...');

    fetch(`/cozinha/pedido/${pedidoId}/iniciar`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            atualizarPedidos();
        }
    })
    .catch(error => console.error('Erro:', error))
    .finally(() => {
        hideLoading();
    });
}

function marcarPronto(pedidoId) {
    showLoading('Marcando pedido como pronto...');

    fetch(`/cozinha/pedido/${pedidoId}/pronto`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            atualizarPedidos();
        }
    })
    .catch(error => console.error('Erro:', error))
    .finally(() => {
        hideLoading();
    });
}

function entregar(pedidoId) {
    showLoading('Enviando para entrega...');

    fetch(`/cozinha/pedido/${pedidoId}/entregar`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            atualizarPedidos();
        }
    })
    .catch(error => console.error('Erro:', error))
    .finally(() => {
        hideLoading();
    });
}

function marcarEntregue(pedidoId) {
    showLoading('Finalizando entrega...');

    fetch(`/cozinha/pedido/${pedidoId}/entregue`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            atualizarPedidos();
        }
    })
    .catch(error => console.error('Erro:', error))
    .finally(() => {
        hideLoading();
    });
}
</script>
@endpush
