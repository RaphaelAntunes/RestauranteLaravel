@extends('layouts.app')

@section('title', 'Novo Pedido')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Novo Pedido</h1>
        <a href="{{ route('pedidos.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
            ← Voltar
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-6">
        <form action="{{ route('pedidos.store') }}" method="POST" id="pedido-form">
            @csrf

            <!-- Seleção de Mesa -->
            <div class="mb-6">
                <label for="mesa_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Mesa *</label>
                <select name="mesa_id" id="mesa_id" required class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-100 focus:ring-2 focus:ring-red-500 focus:border-transparent transition">
                    <option value="">Selecione uma mesa</option>
                    @foreach($mesas as $mesa)
                        <option value="{{ $mesa->id }}">Mesa {{ $mesa->numero }} - {{ $mesa->localizacao }} ({{ $mesa->capacidade }} pessoas)</option>
                    @endforeach
                </select>
            </div>

            <!-- Observações Gerais -->
            <div class="mb-6">
                <label for="observacoes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Observações Gerais</label>
                <textarea name="observacoes" id="observacoes" rows="2" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-red-500"></textarea>
            </div>

            <!-- Seleção de Produtos -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Produtos</h3>

                @foreach($produtos as $categoria => $items)
                    <div class="mb-6">
                        <h4 class="text-md font-medium text-gray-700 dark:text-gray-300 mb-3">{{ $categoria }}</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($items as $produto)
                                <div class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-700 rounded-lg p-3 hover:border-red-500 dark:hover:border-red-500 cursor-pointer transition-colors"
                                     onclick="@if($produto->tamanhos->count() > 0) abrirModalPizza({{ $produto->id }}, '{{ $produto->nome }}', {{ $produto->tamanhos }}, '{{ addslashes($categoria) }}') @else adicionarProduto({{ $produto->id }}, '{{ $produto->nome }}', {{ $produto->preco }}) @endif">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <p class="font-medium text-gray-900 dark:text-white">{{ $produto->nome }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $produto->descricao }}</p>
                                            @if($produto->tamanhos->count() > 0)
                                                <span class="inline-flex mt-2 px-2 py-1 text-xs font-semibold rounded-full bg-orange-900/40 text-orange-300 border border-orange-800">
                                                    {{ $produto->tamanhos->count() }} tamanhos
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-sm font-semibold text-red-600 dark:text-red-400 ml-2">
                                            @if($produto->tamanhos->count() > 0)
                                                A partir de R$ {{ number_format($produto->tamanhos->min('preco'), 2, ',', '.') }}
                                            @else
                                                R$ {{ number_format($produto->preco, 2, ',', '.') }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Itens Adicionados -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Itens do Pedido</h3>
                <div id="itens-pedido" class="space-y-3">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Clique nos produtos acima para adicionar ao pedido</p>
                </div>
            </div>

            <!-- Total -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mb-6">
                <div class="flex justify-between items-center text-lg font-bold text-gray-900 dark:text-white">
                    <span>Total:</span>
                    <span id="total-pedido">R$ 0,00</span>
                </div>
            </div>

            <!-- Botões -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('pedidos.index') }}" class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2 bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white rounded-lg font-semibold shadow-lg transition-all duration-200">
                    Criar Pedido
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Seleção de Pizza -->
<div id="modalPizza" class="fixed inset-0 bg-black/80 hidden z-50 flex items-center justify-center p-4" onclick="fecharModalPizza()">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="sticky top-0 bg-gradient-to-r from-red-500 to-orange-500 p-6 rounded-t-xl">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-2xl font-bold text-white" id="modalPizzaNome">Pizza</h3>
                    <p class="text-white/90 text-sm">Escolha o tamanho e sabores</p>
                </div>
                <button onclick="fecharModalPizza()" class="text-white hover:bg-white/20 rounded-lg p-2 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-6">
            <!-- Tamanhos -->
            <div>
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Escolha o Tamanho *</h4>
                <div id="tamanhos-container" class="grid grid-cols-2 gap-3"></div>
            </div>

            <!-- Sabores -->
            <div id="sabores-section" class="hidden">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Escolha os Sabores *</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                    <span id="sabores-info"></span>
                </p>
                <div id="sabores-container" class="space-y-4"></div>
            </div>

            <!-- Observações -->
            <div>
                <label for="pizza-observacoes" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Observações</label>
                <textarea id="pizza-observacoes" rows="2" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-100 focus:ring-2 focus:ring-red-500" placeholder="Ex: Sem cebola, borda recheada, etc"></textarea>
            </div>
        </div>

        <!-- Footer -->
        <div class="sticky bottom-0 bg-gray-50 dark:bg-gray-900 p-4 rounded-b-xl border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
            <button onclick="fecharModalPizza()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                Cancelar
            </button>
            <button onclick="adicionarPizza()" class="px-4 py-2 bg-gradient-to-r from-red-500 to-orange-500 text-white rounded-lg hover:from-red-600 hover:to-orange-600 transition font-semibold">
                Adicionar ao Pedido
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
let itens = [];
let itemIndex = 0;

// Dados da pizza sendo configurada
let pizzaAtual = {
    produtoId: null,
    produtoNome: null,
    categoria: null,
    tamanhos: [],
    tamanhoSelecionado: null,
    saboresSelecionados: []
};

// Sabores disponíveis
const saboresDisponiveis = @json($sabores);

function adicionarProduto(id, nome, preco) {
    const item = {
        index: itemIndex++,
        produto_id: id,
        nome: nome,
        preco: preco,
        quantidade: 1,
        isPizza: false
    };

    itens.push(item);
    renderizarItens();
    atualizarTotal();
}

function abrirModalPizza(id, nome, tamanhos, categoria) {
    pizzaAtual = {
        produtoId: id,
        produtoNome: nome,
        categoria: categoria,
        tamanhos: tamanhos,
        tamanhoSelecionado: null,
        saboresSelecionados: []
    };

    document.getElementById('modalPizzaNome').textContent = nome;
    document.getElementById('modalPizza').classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    renderizarTamanhos();
    document.getElementById('sabores-section').classList.add('hidden');
}

function fecharModalPizza() {
    document.getElementById('modalPizza').classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('pizza-observacoes').value = '';
}

function renderizarTamanhos() {
    const container = document.getElementById('tamanhos-container');
    container.innerHTML = pizzaAtual.tamanhos.map(tamanho => `
        <div class="border-2 ${pizzaAtual.tamanhoSelecionado?.id === tamanho.id ? 'border-red-500 bg-red-50 dark:bg-red-900/20' : 'border-gray-300 dark:border-gray-600'} rounded-lg p-4 cursor-pointer hover:border-red-400 transition" onclick="selecionarTamanho(${tamanho.id})">
            <div class="text-center">
                <p class="text-2xl font-bold text-gray-900 dark:text-white">${tamanho.nome}</p>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">${tamanho.descricao || ''}</p>
                <p class="text-lg font-semibold text-red-600 dark:text-red-400 mt-2">R$ ${parseFloat(tamanho.preco).toFixed(2).replace('.', ',')}</p>
                <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">Até ${tamanho.max_sabores} ${tamanho.max_sabores > 1 ? 'sabores' : 'sabor'}</p>
            </div>
        </div>
    `).join('');
}

function selecionarTamanho(tamanhoId) {
    pizzaAtual.tamanhoSelecionado = pizzaAtual.tamanhos.find(t => t.id === tamanhoId);
    pizzaAtual.saboresSelecionados = [];

    renderizarTamanhos();
    document.getElementById('sabores-section').classList.remove('hidden');
    document.getElementById('sabores-info').textContent = `Selecione até ${pizzaAtual.tamanhoSelecionado.max_sabores} ${pizzaAtual.tamanhoSelecionado.max_sabores > 1 ? 'sabores' : 'sabor'}`;

    renderizarSabores();
}

function renderizarSabores() {
    const container = document.getElementById('sabores-container');
    const tamanhoNome = pizzaAtual.tamanhoSelecionado.nome.toLowerCase();
    const campoPreco = `preco_${tamanhoNome}`;
    const categoriaProduto = pizzaAtual.categoria;

    // Filtrar categorias de sabores baseado no tipo de pizza
    // Pizza Doce -> só mostra sabores de "Pizzas Doces"
    // Pizza Salgada -> mostra sabores de "Pizzas" e "Pizzas Doces"
    const isPizzaDoce = categoriaProduto && categoriaProduto.toLowerCase().includes('doce');

    let html = '';
    for (const [categoriaSabor, sabores] of Object.entries(saboresDisponiveis)) {
        // Se for pizza doce, só mostrar sabores doces
        if (isPizzaDoce) {
            if (!categoriaSabor.toLowerCase().includes('doce')) {
                continue; // Pular categorias que não são doces
            }
        }

        html += `
            <div>
                <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">${categoriaSabor}</h5>
                <div class="grid grid-cols-1 gap-2">
        `;

        sabores.forEach(sabor => {
            const isSelected = pizzaAtual.saboresSelecionados.includes(sabor.id);
            const isDisabled = !isSelected && pizzaAtual.saboresSelecionados.length >= pizzaAtual.tamanhoSelecionado.max_sabores;
            const precoSabor = sabor[campoPreco] ? parseFloat(sabor[campoPreco]) : 0;
            const isEspecial = precoSabor > parseFloat(pizzaAtual.tamanhoSelecionado.preco);

            html += `
                <div class="flex items-center p-3 border ${isSelected ? 'border-red-500 bg-red-50 dark:bg-red-900/20' : 'border-gray-300 dark:border-gray-600'} rounded-lg ${isDisabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer hover:border-red-400'} transition" onclick="${!isDisabled ? `toggleSabor(${sabor.id})` : ''}">
                    <input type="checkbox" ${isSelected ? 'checked' : ''} ${isDisabled ? 'disabled' : ''} class="h-4 w-4 text-red-500 bg-gray-700 border-gray-600 rounded focus:ring-red-500 mr-3" onclick="event.stopPropagation()">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <p class="font-medium text-gray-900 dark:text-white">${sabor.nome}</p>
                            ${isEspecial ? '<span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-orange-900/40 text-orange-300 border border-orange-800">Especial</span>' : ''}
                        </div>
                        ${sabor.ingredientes ? `<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">${sabor.ingredientes}</p>` : ''}
                        ${precoSabor > 0 ? `<p class="text-xs text-green-400 mt-1 font-semibold">R$ ${precoSabor.toFixed(2).replace('.', ',')}</p>` : ''}
                    </div>
                </div>
            `;
        });

        html += `
                </div>
            </div>
        `;
    }

    container.innerHTML = html;
}

function toggleSabor(saborId) {
    const index = pizzaAtual.saboresSelecionados.indexOf(saborId);

    if (index > -1) {
        pizzaAtual.saboresSelecionados.splice(index, 1);
    } else if (pizzaAtual.saboresSelecionados.length < pizzaAtual.tamanhoSelecionado.max_sabores) {
        pizzaAtual.saboresSelecionados.push(saborId);
    }

    renderizarSabores();
}

function adicionarPizza() {
    if (!pizzaAtual.tamanhoSelecionado) {
        alert('Selecione um tamanho');
        return;
    }

    if (pizzaAtual.saboresSelecionados.length === 0) {
        alert('Selecione pelo menos um sabor');
        return;
    }

    // Calcular preço baseado nos sabores escolhidos (sempre o maior preço)
    const tamanhoNome = pizzaAtual.tamanhoSelecionado.nome.toLowerCase();
    const campoPreco = `preco_${tamanhoNome}`;
    let maiorPreco = parseFloat(pizzaAtual.tamanhoSelecionado.preco);

    const saboresNomes = pizzaAtual.saboresSelecionados.map(id => {
        for (const sabores of Object.values(saboresDisponiveis)) {
            const sabor = sabores.find(s => s.id === id);
            if (sabor) {
                // Verificar o preço do sabor para o tamanho selecionado
                const precoSabor = sabor[campoPreco] ? parseFloat(sabor[campoPreco]) : 0;
                if (precoSabor > maiorPreco) {
                    maiorPreco = precoSabor;
                }
                return sabor.nome;
            }
        }
        return '';
    }).join(', ');

    const item = {
        index: itemIndex++,
        produto_id: pizzaAtual.produtoId,
        nome: `${pizzaAtual.produtoNome} - ${pizzaAtual.tamanhoSelecionado.nome}`,
        detalhes: `Sabores: ${saboresNomes}`,
        preco: maiorPreco,
        quantidade: 1,
        isPizza: true,
        tamanho_id: pizzaAtual.tamanhoSelecionado.id,
        sabores: pizzaAtual.saboresSelecionados,
        observacoes: document.getElementById('pizza-observacoes').value
    };

    itens.push(item);
    renderizarItens();
    atualizarTotal();
    fecharModalPizza();
}

function removerItem(index) {
    itens = itens.filter(item => item.index !== index);
    renderizarItens();
    atualizarTotal();
}

function atualizarQuantidade(index, quantidade) {
    const item = itens.find(i => i.index === index);
    if (item) {
        item.quantidade = parseInt(quantidade);
        atualizarTotal();
    }
}

function renderizarItens() {
    const container = document.getElementById('itens-pedido');

    if (itens.length === 0) {
        container.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-sm">Clique nos produtos acima para adicionar ao pedido</p>';
        return;
    }

    container.innerHTML = itens.map(item => `
        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg">
            <div class="flex-1">
                <p class="font-medium text-gray-900 dark:text-white">${item.nome}</p>
                ${item.detalhes ? `<p class="text-xs text-gray-600 dark:text-gray-400 mt-1">${item.detalhes}</p>` : ''}
                ${item.observacoes ? `<p class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">Obs: ${item.observacoes}</p>` : ''}
                <p class="text-sm text-gray-600 dark:text-gray-400">R$ ${item.preco.toFixed(2).replace('.', ',')}</p>
                <input type="hidden" name="itens[${item.index}][produto_id]" value="${item.produto_id}">
                ${item.isPizza ? `
                    <input type="hidden" name="itens[${item.index}][tamanho_id]" value="${item.tamanho_id}">
                    ${item.sabores.map((saborId, idx) => `<input type="hidden" name="itens[${item.index}][sabores][${idx}]" value="${saborId}">`).join('')}
                ` : ''}
                <input type="hidden" name="itens[${item.index}][observacoes]" value="${item.observacoes || ''}">
            </div>
            <div class="flex items-center space-x-3">
                <input
                    type="number"
                    name="itens[${item.index}][quantidade]"
                    value="${item.quantidade}"
                    min="1"
                    class="w-20 px-2 py-1 bg-gray-700 border border-gray-600 rounded text-gray-100"
                    onchange="atualizarQuantidade(${item.index}, this.value)"
                >
                <span class="font-medium text-gray-900 dark:text-white w-24 text-right">R$ ${(item.preco * item.quantidade).toFixed(2).replace('.', ',')}</span>
                <button type="button" onclick="removerItem(${item.index})" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    `).join('');
}

function atualizarTotal() {
    const total = itens.reduce((sum, item) => sum + (item.preco * item.quantidade), 0);
    document.getElementById('total-pedido').textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
}

document.getElementById('pedido-form').addEventListener('submit', function(e) {
    if (itens.length === 0) {
        e.preventDefault();
        alert('Adicione pelo menos um item ao pedido');
    }
});

// Fechar modal com ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        fecharModalPizza();
    }
});
</script>
@endpush
@endsection
