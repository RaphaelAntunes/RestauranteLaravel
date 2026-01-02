<!--
    Loading Overlay Global

    PROTEÇÃO AUTOMÁTICA DE FORMULÁRIOS:
    - Todos os formulários recebem automaticamente proteção contra duplo clique
    - Botões de submit são desabilitados e exibem spinner durante o processamento
    - Overlay de loading é exibido automaticamente

    PROTEÇÃO AUTOMÁTICA DE LINKS:
    - Todos os links de navegação interna exibem loading automaticamente
    - Links externos, âncoras (#), target="_blank" e downloads são ignorados
    - Mensagens contextuais baseadas na rota (mesas, pedidos, PDV, etc.)

    CUSTOMIZAÇÃO DE FORMULÁRIOS:
    - Para desativar o loading automático em um formulário específico, adicione a classe 'no-auto-loading'
      Exemplo: <form class="no-auto-loading" ...>

    - Para customizar a mensagem de loading de um formulário, adicione o atributo 'data-loading-text'
      Exemplo: <form data-loading-text="Processando pagamento..." ...>

    CUSTOMIZAÇÃO DE LINKS:
    - Para desativar o loading automático em um link específico, adicione a classe 'no-loading'
      Exemplo: <a href="/pagina" class="no-loading">Link sem loading</a>

    - Para customizar a mensagem de loading de um link, adicione o atributo 'data-loading-text'
      Exemplo: <a href="/mesas" data-loading-text="Carregando mesas...">Mesas</a>
-->
<div id="global-loading-overlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[9999] hidden items-center justify-center transition-all duration-300">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-8 flex flex-col items-center space-y-6 animate-fade-in">
        <!-- Loading GIF -->
        <div class="flex items-center justify-center">
            <img src="https://i.gifer.com/ZKZg.gif" alt="Loading..." class="w-32 h-32 object-contain">
        </div>

        <!-- Texto -->
        <div class="text-center">
            <p id="loading-text" class="text-lg font-semibold text-gray-900 dark:text-white animate-pulse">Carregando...</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Por favor, aguarde</p>
        </div>
    </div>
</div>

<style>
    @keyframes fade-in {
        from {
            opacity: 0;
            transform: scale(0.9);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    @keyframes spin-slow {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    @keyframes spin-fast {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    .animate-fade-in {
        animation: fade-in 0.3s ease-out;
    }

    .animate-spin-slow {
        animation: spin-slow 3s linear infinite;
    }

    .animate-spin-fast {
        animation: spin-fast 0.6s linear infinite;
    }

    #global-loading-overlay.show {
        display: flex !important;
    }
</style>

<script>
    // Funções globais para controlar o loading
    window.showLoading = function(text = 'Carregando...') {
        const overlay = document.getElementById('global-loading-overlay');
        const loadingText = document.getElementById('loading-text');

        if (loadingText) {
            loadingText.textContent = text;
        }

        if (overlay) {
            overlay.classList.remove('hidden');
            overlay.classList.add('show');
            // Previne scroll do body
            document.body.style.overflow = 'hidden';
        }
    };

    window.hideLoading = function() {
        const overlay = document.getElementById('global-loading-overlay');

        if (overlay) {
            overlay.classList.remove('show');
            overlay.classList.add('hidden');
            // Restaura scroll do body
            document.body.style.overflow = '';
        }
    };

    // Helper para requisições fetch com loading automático
    window.fetchWithLoading = async function(url, options = {}, loadingText = 'Carregando...') {
        showLoading(loadingText);
        try {
            const response = await fetch(url, options);
            return response;
        } finally {
            hideLoading();
        }
    };

    // Sistema de proteção automática de formulários contra duplo clique
    document.addEventListener('DOMContentLoaded', function() {
        // Selecionar todos os formulários da página
        const forms = document.querySelectorAll('form');

        forms.forEach(form => {
            // Ignorar formulários com classe 'no-auto-loading' se o dev quiser desativar
            if (form.classList.contains('no-auto-loading')) {
                return;
            }

            form.addEventListener('submit', function(e) {
                // Verificar se o formulário já está sendo processado
                if (form.dataset.submitting === 'true') {
                    e.preventDefault();
                    return false;
                }

                // Marcar formulário como em processamento
                form.dataset.submitting = 'true';

                // Encontrar o botão de submit
                const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');

                submitButtons.forEach(button => {
                    // Salvar o texto original do botão
                    if (!button.dataset.originalHtml) {
                        button.dataset.originalHtml = button.innerHTML || button.value;
                    }

                    // Desabilitar o botão
                    button.disabled = true;
                    button.classList.add('opacity-75', 'cursor-not-allowed');

                    // Adicionar spinner e texto de loading
                    if (button.tagName === 'BUTTON') {
                        button.innerHTML = `
                            <svg class="animate-spin h-5 w-5 inline-block mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Processando...</span>
                        `;
                    } else {
                        button.value = 'Processando...';
                    }
                });

                // Determinar mensagem de loading baseada no formulário
                let loadingMessage = 'Processando...';

                // Verificar se há atributo data-loading-text no formulário
                if (form.dataset.loadingText) {
                    loadingMessage = form.dataset.loadingText;
                } else {
                    // Mensagens baseadas na rota/action
                    const action = form.action || '';

                    if (action.includes('/login')) {
                        loadingMessage = 'Autenticando...';
                    } else if (action.includes('/logout')) {
                        loadingMessage = 'Saindo...';
                    } else if (action.includes('/store') || action.includes('/create')) {
                        loadingMessage = 'Salvando...';
                    } else if (action.includes('/update') || action.includes('/edit')) {
                        loadingMessage = 'Atualizando...';
                    } else if (action.includes('/destroy') || action.includes('/delete')) {
                        loadingMessage = 'Excluindo...';
                    } else if (action.includes('/pagamento') || action.includes('/fechar')) {
                        loadingMessage = 'Processando pagamento...';
                    } else if (action.includes('/pedido')) {
                        loadingMessage = 'Enviando pedido...';
                    }
                }

                // Mostrar loading overlay
                showLoading(loadingMessage);

                // Nota: O loading será escondido automaticamente quando a página recarregar
                // ou se houver erro de validação que impeça o submit
            });

            // Resetar estado se houver erro de validação (página não recarrega)
            window.addEventListener('pageshow', function(event) {
                if (event.persisted || performance.getEntriesByType("navigation")[0]?.type === "back_forward") {
                    // Página foi restaurada do cache (botão voltar)
                    resetFormState(form);
                }
            });
        });

        // Função para resetar estado do formulário
        function resetFormState(form) {
            form.dataset.submitting = 'false';
            hideLoading();

            const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
            submitButtons.forEach(button => {
                button.disabled = false;
                button.classList.remove('opacity-75', 'cursor-not-allowed');

                if (button.dataset.originalHtml) {
                    if (button.tagName === 'BUTTON') {
                        button.innerHTML = button.dataset.originalHtml;
                    } else {
                        button.value = button.dataset.originalHtml;
                    }
                }
            });
        }

        // Sistema de proteção automática de links de navegação
        document.addEventListener('click', function(e) {
            // Encontrar o link clicado (pode ser o elemento ou um pai)
            let target = e.target;
            let link = null;

            // Buscar até 3 níveis acima para encontrar um link
            for (let i = 0; i < 3 && target; i++) {
                if (target.tagName === 'A' && target.href) {
                    link = target;
                    break;
                }
                target = target.parentElement;
            }

            if (!link) return;

            // Verificar se deve mostrar loading
            const href = link.getAttribute('href');

            // Não mostrar loading se:
            // - Link tem classe 'no-loading'
            // - Link é âncora (#)
            // - Link abre em nova aba
            // - Link tem download
            // - Link é para logout (já tem loading do formulário)
            // - Link é JavaScript (javascript:)
            // - Link é mailto ou tel
            if (
                link.classList.contains('no-loading') ||
                !href ||
                href === '#' ||
                href.startsWith('#') ||
                href.startsWith('javascript:') ||
                href.startsWith('mailto:') ||
                href.startsWith('tel:') ||
                link.target === '_blank' ||
                link.hasAttribute('download') ||
                href.includes('/logout')
            ) {
                return;
            }

            // Verificar se é link externo (domínio diferente)
            try {
                const linkUrl = new URL(link.href);
                const currentUrl = new URL(window.location.href);

                // Se for domínio diferente, não mostrar loading
                if (linkUrl.hostname !== currentUrl.hostname) {
                    return;
                }
            } catch (error) {
                // Se houver erro ao parsear URL, continuar normalmente
            }

            // Verificar se tem atributo data-loading-text personalizado
            let loadingMessage = 'Carregando...';
            if (link.dataset.loadingText) {
                loadingMessage = link.dataset.loadingText;
            } else {
                // Mensagens baseadas na rota
                if (href.includes('/mesas/') && href.includes('/comanda')) {
                    loadingMessage = 'Abrindo comanda...';
                } else if (href.includes('/mesas')) {
                    loadingMessage = 'Carregando mesas...';
                } else if (href.includes('/pedidos')) {
                    loadingMessage = 'Carregando pedidos...';
                } else if (href.includes('/pdv')) {
                    loadingMessage = 'Abrindo PDV...';
                } else if (href.includes('/cozinha')) {
                    loadingMessage = 'Carregando cozinha...';
                } else if (href.includes('/produtos')) {
                    loadingMessage = 'Carregando produtos...';
                } else if (href.includes('/categorias')) {
                    loadingMessage = 'Carregando categorias...';
                } else if (href.includes('/relatorios')) {
                    loadingMessage = 'Carregando relatórios...';
                } else if (href.includes('/create')) {
                    loadingMessage = 'Abrindo formulário...';
                } else if (href.includes('/edit')) {
                    loadingMessage = 'Carregando dados...';
                }
            }

            // Mostrar loading
            showLoading(loadingMessage);

            // O loading será escondido automaticamente quando a nova página carregar
            // Ou se o usuário voltar (evento pageshow já configurado)
        });

        // Esconder loading quando a página terminar de carregar
        // (caso o usuário clique no botão voltar)
        window.addEventListener('pageshow', function() {
            hideLoading();
        });
    });
</script>
