<!-- Modal de Pedido -->
<div id="modalPedido" class="fixed inset-0 bg-black/80 hidden z-50 flex items-center justify-center p-4" onclick="fecharModal('modalPedido')">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="sticky top-0 bg-gradient-to-r from-red-500 to-orange-500 p-6 rounded-t-xl">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-2xl font-bold text-white" id="modalPedidoNumero">Carregando...</h3>
                    <p class="text-white/90 text-sm" id="modalPedidoMesa"></p>
                </div>
                <button onclick="fecharModal('modalPedido')" class="text-white hover:bg-white/20 rounded-lg p-2 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Body -->
        <div class="p-6">
            <!-- Info do Pedido -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Garçom</p>
                    <p class="font-semibold text-gray-900 dark:text-white" id="modalPedidoGarcom">-</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Data</p>
                    <p class="font-semibold text-gray-900 dark:text-white" id="modalPedidoData">-</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Status</p>
                    <span id="modalPedidoStatus"></span>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400" id="modalPedidoTotal">-</p>
                </div>
            </div>

            <!-- Observações -->
            <div id="modalPedidoObsDiv" class="mb-6 hidden">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Observações:</p>
                <p class="text-sm text-gray-600 dark:text-gray-400 bg-yellow-50 dark:bg-yellow-900/20 p-3 rounded-lg" id="modalPedidoObs"></p>
            </div>

            <!-- Itens -->
            <div>
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Itens do Pedido</h4>
                <div class="space-y-2" id="modalPedidoItens"></div>
            </div>
        </div>

        <!-- Footer -->
        <div class="sticky bottom-0 bg-gray-50 dark:bg-gray-900 p-4 rounded-b-xl border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
            <button onclick="fecharModal('modalPedido')" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                Fechar
            </button>
            <a id="modalPedidoVerMais" href="#" class="px-4 py-2 bg-gradient-to-r from-red-500 to-orange-500 text-white rounded-lg hover:from-red-600 hover:to-orange-600 transition">
                Ver Detalhes Completos
            </a>
        </div>
    </div>
</div>

<!-- Modal de Mesa -->
<div id="modalMesa" class="fixed inset-0 bg-black/80 hidden z-50 flex items-center justify-center p-4" onclick="fecharModal('modalMesa')">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="sticky top-0 bg-gradient-to-r from-green-500 to-green-600 p-6 rounded-t-xl">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-2xl font-bold text-white" id="modalMesaNumero">Carregando...</h3>
                    <p class="text-white/90 text-sm" id="modalMesaInfo"></p>
                </div>
                <button onclick="fecharModal('modalMesa')" class="text-white hover:bg-white/20 rounded-lg p-2 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Body -->
        <div class="p-6">
            <!-- Total Geral -->
            <div class="bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 p-4 rounded-lg mb-6 border border-green-200 dark:border-green-800">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Geral da Mesa</p>
                <p class="text-3xl font-bold text-green-600 dark:text-green-400" id="modalMesaTotal">-</p>
            </div>

            <!-- Pedidos da Mesa -->
            <div>
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pedidos Ativos</h4>
                <div class="space-y-4" id="modalMesaPedidos"></div>
            </div>
        </div>

        <!-- Footer -->
        <div class="sticky bottom-0 bg-gray-50 dark:bg-gray-900 p-4 rounded-b-xl border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
            <button onclick="fecharModal('modalMesa')" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                Fechar
            </button>
            <a id="modalMesaComanda" href="#" class="px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 transition">
                Ver Comanda Completa
            </a>
        </div>
    </div>
</div>

<script>
function abrirModalPedido(id) {
    document.getElementById('modalPedido').classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // Mostrar loading
    showLoading('Carregando detalhes do pedido...');

    fetch(`/api/pedido/${id}`)
        .then(response => response.json())
        .then(data => {
            const p = data.pedido;

            document.getElementById('modalPedidoNumero').textContent = p.numero_pedido;
            document.getElementById('modalPedidoMesa').textContent = `Mesa ${p.mesa.numero} - ${p.mesa.localizacao}`;
            document.getElementById('modalPedidoGarcom').textContent = p.garcom;
            document.getElementById('modalPedidoData').textContent = p.data;
            document.getElementById('modalPedidoTotal').textContent = p.total_formatado;
            document.getElementById('modalPedidoVerMais').href = `/pedidos/${p.id}`;

            // Status
            let statusClass = 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-300 border-gray-200 dark:border-gray-700';
            if (p.status === 'aberto') statusClass = 'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-300 border-yellow-200 dark:border-yellow-800';
            if (p.status === 'em_preparo') statusClass = 'bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-300 border-blue-200 dark:border-blue-800';
            if (p.status === 'pronto') statusClass = 'bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-300 border-green-200 dark:border-green-800';

            document.getElementById('modalPedidoStatus').innerHTML = `<span class="inline-flex px-3 py-1.5 text-xs font-bold rounded-full border ${statusClass}">${p.status_formatado}</span>`;

            // Observações
            if (p.observacoes) {
                document.getElementById('modalPedidoObsDiv').classList.remove('hidden');
                document.getElementById('modalPedidoObs').textContent = p.observacoes;
            } else {
                document.getElementById('modalPedidoObsDiv').classList.add('hidden');
            }

            // Itens
            let itensHtml = '';
            p.itens.forEach(item => {
                itensHtml += `
                    <div class="flex justify-between items-start p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 dark:text-white">
                                <span class="text-red-600 dark:text-red-400 font-bold">${item.quantidade}x</span> ${item.produto}
                            </p>
                            ${item.observacoes ? `<p class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">Obs: ${item.observacoes}</p>` : ''}
                        </div>
                        <div class="text-right ml-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">${item.preco_unitario}</p>
                            <p class="font-bold text-gray-900 dark:text-white">${item.subtotal}</p>
                        </div>
                    </div>
                `;
            });
            document.getElementById('modalPedidoItens').innerHTML = itensHtml;
        })
        .catch(error => console.error('Erro:', error))
        .finally(() => {
            // Esconder loading
            hideLoading();
        });
}

function abrirModalMesa(id) {
    document.getElementById('modalMesa').classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // Mostrar loading
    showLoading('Carregando detalhes da mesa...');

    fetch(`/api/mesa/${id}`)
        .then(response => response.json())
        .then(data => {
            const m = data.mesa;

            document.getElementById('modalMesaNumero').textContent = `Mesa ${m.numero}`;
            document.getElementById('modalMesaInfo').textContent = `${m.localizacao} - Capacidade: ${m.capacidade} pessoas`;
            document.getElementById('modalMesaTotal').textContent = m.total_geral;
            document.getElementById('modalMesaComanda').href = `/mesas/${m.id}/comanda`;

            // Pedidos
            let pedidosHtml = '';
            if (m.pedidos.length === 0) {
                pedidosHtml = '<p class="text-gray-500 dark:text-gray-400 text-center py-4">Nenhum pedido ativo</p>';
            } else {
                m.pedidos.forEach(pedido => {
                    let statusClass = 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-300';
                    if (pedido.status === 'aberto') statusClass = 'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-300';
                    if (pedido.status === 'em_preparo') statusClass = 'bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-300';
                    if (pedido.status === 'pronto') statusClass = 'bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-300';

                    let itensHtml = '';
                    pedido.itens.forEach(item => {
                        itensHtml += `<li class="text-sm text-gray-600 dark:text-gray-400">${item.quantidade}x ${item.produto} - ${item.subtotal}</li>`;
                    });

                    pedidosHtml += `
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 p-4">
                            <div class="flex justify-between items-center mb-3">
                                <div>
                                    <p class="font-bold text-gray-900 dark:text-white">${pedido.numero_pedido}</p>
                                    <span class="inline-flex px-2 py-1 text-xs font-bold rounded-full mt-1 ${statusClass}">${pedido.status_formatado}</span>
                                </div>
                                <p class="text-xl font-bold text-red-600 dark:text-red-400">${pedido.total}</p>
                            </div>
                            <ul class="space-y-1">${itensHtml}</ul>
                        </div>
                    `;
                });
            }
            document.getElementById('modalMesaPedidos').innerHTML = pedidosHtml;
        })
        .catch(error => console.error('Erro:', error))
        .finally(() => {
            // Esconder loading
            hideLoading();
        });
}

function fecharModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Fechar modal com ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        fecharModal('modalPedido');
        fecharModal('modalMesa');
    }
});
</script>
