<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardApiController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\SaborController;
use App\Http\Controllers\ProdutoTamanhoController;
use App\Http\Controllers\MesaController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\PDVController;
use App\Http\Controllers\CozinhaController;
use App\Http\Controllers\PreparoController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\GarcomManagementController;
use App\Http\Controllers\GarcomController;
use App\Http\Controllers\FaceAuthController;
use App\Http\Controllers\Cliente\ClienteAuthController;
use App\Http\Controllers\Cliente\CardapioController;
use App\Http\Controllers\Cliente\CarrinhoController;
use App\Http\Controllers\Cliente\PedidoOnlineController;
use App\Http\Controllers\Cliente\ClienteEnderecoController;
use App\Http\Controllers\Admin\PedidoOnlineAdminController;
use App\Http\Controllers\Admin\ConfiguracaoDeliveryController;

/*
|--------------------------------------------------------------------------
| Rotas de Clientes (Sistema de Pedidos Online)
|--------------------------------------------------------------------------
*/
Route::prefix('cliente')->name('cliente.')->group(function () {

    // Rotas para visitantes (guest)
    Route::middleware('cliente.guest')->group(function () {
        Route::get('/login', [ClienteAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login/otp', [ClienteAuthController::class, 'enviarOtp'])->name('otp.enviar');
        Route::get('/login/verificar', [ClienteAuthController::class, 'showVerificarOtpForm'])->name('otp.form');
        Route::post('/login/verificar', [ClienteAuthController::class, 'verificarOtp'])->name('otp.verificar');
    });

    // Cardápio público (sem auth)
    Route::get('/cardapio', [CardapioController::class, 'index'])->name('cardapio');
    Route::get('/cardapio/{produto}', [CardapioController::class, 'show'])->name('cardapio.show');

    // Carrinho público (funciona para visitantes e logados)
    Route::post('/carrinho/adicionar', [CarrinhoController::class, 'adicionar'])->name('carrinho.adicionar.publico');
    Route::get('/carrinho/info', [CarrinhoController::class, 'getCartInfo'])->name('carrinho.info');
    Route::get('/carrinho/data', [CarrinhoController::class, 'getCartData'])->name('carrinho.data');

    // Rotas autenticadas
    Route::middleware('cliente.auth')->group(function () {
        Route::post('/logout', [ClienteAuthController::class, 'logout'])->name('logout');

        // Carrinho
        Route::prefix('carrinho')->name('carrinho.')->group(function () {
            Route::get('/', [CarrinhoController::class, 'index'])->name('index');
            Route::put('/{item}', [CarrinhoController::class, 'atualizar'])->name('atualizar');
            Route::delete('/{item}', [CarrinhoController::class, 'remover'])->name('remover');
            Route::delete('/', [CarrinhoController::class, 'limpar'])->name('limpar');
        });

        // Pedidos
        Route::get('/checkout', [PedidoOnlineController::class, 'checkout'])->name('checkout');
        Route::get('/checkout/data', [PedidoOnlineController::class, 'getCheckoutData'])->name('checkout.data');
        Route::post('/pedido/finalizar', [PedidoOnlineController::class, 'finalizarPedido'])->name('pedido.finalizar');
        Route::get('/pedidos', [PedidoOnlineController::class, 'meusPedidos'])->name('pedidos');
        Route::get('/pedido/{pedido}/acompanhar', [PedidoOnlineController::class, 'acompanhar'])->name('pedido.acompanhar');
        Route::get('/pedido/{pedido}/status', [PedidoOnlineController::class, 'getStatusPedido'])->name('pedido.status');

        // Endereços
        Route::resource('enderecos', ClienteEnderecoController::class);
        Route::post('/enderecos/{endereco}/padrao', [ClienteEnderecoController::class, 'marcarPadrao'])->name('enderecos.padrao');
    });
});

/*
|--------------------------------------------------------------------------
| Rotas de Autenticação (Públicas - Funcionários)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/login', [AuthController::class, 'showLogin']);
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    // Login Facial (público)
    Route::get('/face/login', [FaceAuthController::class, 'showLoginPage'])->name('face.login');
    Route::post('/face/login', [FaceAuthController::class, 'login'])->name('face.login.post');
});

/*
|--------------------------------------------------------------------------
| Rotas Autenticadas
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Cadastro Facial (protegido - usuário logado)
    Route::get('/face/register', [FaceAuthController::class, 'showRegisterPage'])->name('face.register');
    Route::post('/face/register', [FaceAuthController::class, 'register'])->name('face.register.post');
    Route::delete('/face/remove', [FaceAuthController::class, 'remove'])->name('face.remove');

    // Dashboard (Apenas Admin, Caixa e Cozinha)
    Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware('role:admin,caixa,cozinha');

    // Dashboard API (para modais)
    Route::get('/api/pedido/{id}', [DashboardApiController::class, 'getPedido'])->name('api.pedido');
    Route::get('/api/mesa/{id}', [DashboardApiController::class, 'getMesa'])->name('api.mesa');

    // Categorias (CRUD completo) - Admin e Garçom
    Route::resource('categorias', CategoriaController::class);

    // Produtos (CRUD completo) - Admin e Garçom
    Route::resource('produtos', ProdutoController::class);

    // Sabores (CRUD completo) - Admin e Garçom
    Route::resource('sabores', SaborController::class)->parameters(['sabores' => 'sabor']);

    // Tamanhos de Produtos (CRUD completo) - Admin e Garçom
    Route::resource('produto-tamanhos', ProdutoTamanhoController::class);

    // Mesas (CRUD completo) - Admin e Garçom
    Route::resource('mesas', MesaController::class);
    Route::get('/mesas/{mesa}/comanda', [MesaController::class, 'comanda'])->name('mesas.comanda');
    Route::get('/mesas/{mesa}/imprimir', [MesaController::class, 'imprimirComanda'])->name('mesas.imprimir');
    Route::post('/mesas/{mesa}/cliente', [MesaController::class, 'atualizarCliente'])->name('mesas.atualizarCliente');
    Route::post('/mesas/pedido/{pedido}/cancelar', [MesaController::class, 'cancelarPedido'])->name('mesas.cancelarPedido');

    // Pedidos - Admin e Garçom
    Route::resource('pedidos', PedidoController::class);
    Route::post('/pedidos/{pedido}/status', [PedidoController::class, 'updateStatus'])->name('pedidos.status');

    // Ações administrativas de pedidos (apenas Admin)
    Route::post('/pedidos/{pedido}/invalidar', [PedidoController::class, 'invalidar'])->name('pedidos.invalidar')->middleware('role:admin');
    Route::post('/pedidos/{pedido}/alterar-data', [PedidoController::class, 'alterarData'])->name('pedidos.alterarData')->middleware('role:admin');

    /*
    |--------------------------------------------------------------------------
    | Interface do Garçom (Simplificada)
    |--------------------------------------------------------------------------
    */
    Route::get('/garcom', [GarcomController::class, 'index'])->name('garcom.index');
    Route::get('/garcom/mesa/{mesa}/comanda', [GarcomController::class, 'comanda'])->name('garcom.comanda');
    Route::get('/garcom/mesa/{mesa}/imprimir', [GarcomController::class, 'imprimirComanda'])->name('garcom.imprimir');
    Route::post('/garcom/mesa/{mesa}/cliente', [GarcomController::class, 'atualizarCliente'])->name('garcom.atualizarCliente');
    Route::post('/garcom/pedido/{pedido}/cancelar', [GarcomController::class, 'cancelarPedido'])->name('garcom.cancelarPedido');
    Route::post('/garcom/item/{item}/atualizar-quantidade', [GarcomController::class, 'atualizarQuantidadeItem'])->name('garcom.atualizarQuantidadeItem');
    Route::post('/garcom/item/{item}/cancelar', [GarcomController::class, 'cancelarItem'])->name('garcom.cancelarItem');

    // Fechar conta (usa o PDVController mas permite garçom com permissão)
    Route::get('/garcom/mesa/{mesa}/fechar', [PDVController::class, 'fecharMesa'])->name('garcom.fechar');
    Route::post('/garcom/mesa/{mesa}/pagamento', [PDVController::class, 'processarPagamento'])->name('garcom.pagamento');

    /*
    |--------------------------------------------------------------------------
    | PDV - Ponto de Venda (Admin e Caixa)
    |--------------------------------------------------------------------------
    */
    Route::prefix('pdv')->name('pdv.')->middleware('role:admin,caixa')->group(function () {
        Route::get('/', [PDVController::class, 'index'])->name('index');
        Route::get('/mesa/{mesa}', [PDVController::class, 'fecharMesa'])->name('fechar');
        Route::post('/mesa/{mesa}/pagamento', [PDVController::class, 'processarPagamento'])->name('pagamento');
        Route::get('/comprovante/{pagamento}', [PDVController::class, 'comprovante'])->name('comprovante');
        Route::get('/comprovante/{pagamento}/imprimir', [PDVController::class, 'imprimirComprovante'])->name('comprovante.imprimir');
        Route::get('/historico', [PDVController::class, 'historico'])->name('historico');
        Route::get('/gorjetas', [PDVController::class, 'relatorioGorjetas'])->name('gorjetas');
    });

    /*
    |--------------------------------------------------------------------------
    | Painel da Cozinha (Todos podem acessar)
    |--------------------------------------------------------------------------
    */
    Route::prefix('cozinha')->name('cozinha.')->group(function () {
        Route::get('/', [CozinhaController::class, 'index'])->name('index');
        Route::post('/pedido/{pedido}/iniciar', [CozinhaController::class, 'iniciarPreparo'])->name('iniciar');
        Route::post('/pedido/{pedido}/pronto', [CozinhaController::class, 'marcarPronto'])->name('pronto');
        Route::post('/pedido/{pedido}/entregar', [CozinhaController::class, 'entregar'])->name('entregar');
        Route::post('/pedido/{pedido}/entregue', [CozinhaController::class, 'marcarEntregue'])->name('entregue');
        Route::get('/atualizar', [CozinhaController::class, 'atualizar'])->name('atualizar');
    });

    /*
    |--------------------------------------------------------------------------
    | Painel de Preparo (Apenas Comidas - Sem Bebidas)
    |--------------------------------------------------------------------------
    */
    Route::prefix('preparo')->name('preparo.')->group(function () {
        Route::get('/', [PreparoController::class, 'index'])->name('index');
        Route::get('/atualizar', [PreparoController::class, 'atualizar'])->name('atualizar');
    });

    /*
    |--------------------------------------------------------------------------
    | Gerenciamento de Garçons (Apenas Admin)
    |--------------------------------------------------------------------------
    */
    Route::prefix('garcons')->name('garcons.')->middleware('role:admin')->group(function () {
        Route::get('/', [GarcomManagementController::class, 'index'])->name('index');
        Route::get('/create', [GarcomManagementController::class, 'create'])->name('create');
        Route::post('/', [GarcomManagementController::class, 'store'])->name('store');
        Route::get('/{garcon}/edit', [GarcomManagementController::class, 'edit'])->name('edit');
        Route::put('/{garcon}', [GarcomManagementController::class, 'update'])->name('update');
        Route::delete('/{garcon}', [GarcomManagementController::class, 'destroy'])->name('destroy');
        Route::put('/{garcon}/permissions', [GarcomManagementController::class, 'updatePermissions'])->name('permissions.update');
        Route::post('/{garcon}/toggle-status', [GarcomManagementController::class, 'toggleStatus'])->name('toggle-status');
    });

    /*
    |--------------------------------------------------------------------------
    | Relatórios (Apenas Admin)
    |--------------------------------------------------------------------------
    */
    Route::prefix('relatorios')->name('relatorios.')->middleware('role:admin')->group(function () {
        Route::get('/', [RelatorioController::class, 'index'])->name('index');
        Route::get('/vendas', [RelatorioController::class, 'vendas'])->name('vendas');
        Route::get('/produtos', [RelatorioController::class, 'produtosMaisVendidos'])->name('produtos');
        Route::get('/faturamento', [RelatorioController::class, 'faturamentoMensal'])->name('faturamento');
        Route::get('/garcons', [RelatorioController::class, 'desempenhoGarcons'])->name('garcons');
    });

    /*
    |--------------------------------------------------------------------------
    | Gestão de Pedidos Online (Admin e Caixa)
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin/pedidos-online')->name('admin.pedidos-online.')->middleware('role:admin,caixa')->group(function () {
        Route::get('/', [PedidoOnlineAdminController::class, 'index'])->name('index');
        Route::get('/{pedido}', [PedidoOnlineAdminController::class, 'show'])->name('show');
        Route::post('/{pedido}/status', [PedidoOnlineAdminController::class, 'atualizarStatus'])->name('status');
        Route::put('/{pedido}', [PedidoOnlineAdminController::class, 'atualizar'])->name('atualizar');

        // Gerenciar itens
        Route::post('/{pedido}/itens', [PedidoOnlineAdminController::class, 'adicionarItem'])->name('adicionar-item');
        Route::put('/itens/{item}', [PedidoOnlineAdminController::class, 'atualizarItem'])->name('atualizar-item');
        Route::delete('/itens/{item}', [PedidoOnlineAdminController::class, 'removerItem'])->name('remover-item');
    });

    /*
    |--------------------------------------------------------------------------
    | Configurações de Delivery (Apenas Admin)
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin/configuracoes-delivery')->name('admin.configuracoes-delivery.')->middleware('role:admin')->group(function () {
        Route::get('/', [ConfiguracaoDeliveryController::class, 'edit'])->name('edit');
        Route::put('/', [ConfiguracaoDeliveryController::class, 'update'])->name('update');
    });
});
