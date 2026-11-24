<?php

namespace Tests\Feature;

use App\Models\CarrinhoItem;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\ClienteEndereco;
use App\Models\Mesa;
use App\Models\OtpCode;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Produto;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerAndDeliveryTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_sign_in_with_a_valid_one_time_code(): void
    {
        $response = $this->post(route('cliente.otp.enviar'), [
            'celular' => '(84) 99999-0000',
        ]);

        $response->assertRedirect(route('cliente.otp.form'));
        $response->assertSessionHas('otp_celular', '84999990000');

        $otp = OtpCode::where('celular', '84999990000')->firstOrFail();
        $this->post(route('cliente.otp.verificar'), ['codigo' => $otp->codigo])
            ->assertRedirect(route('cliente.cardapio', ['loja' => 'loja-teste']));

        $this->assertAuthenticatedAs(Cliente::where('celular', '84999990000')->firstOrFail(), 'cliente');
        $this->assertDatabaseHas('clientes', ['celular' => '84999990000']);
    }

    public function test_guest_opening_my_orders_returns_there_after_login(): void
    {
        $ordersUrl = route('cliente.pedidos');

        $this->get(route('cliente.cardapio'))
            ->assertOk()
            ->assertSee('Meus pedidos');

        $this->get($ordersUrl)
            ->assertRedirect(route('cliente.login', ['loja' => 'loja-teste']));
        $this->assertSame($ordersUrl, session('url.intended'));

        $this->post(route('cliente.otp.enviar'), ['celular' => '84999990000']);
        $otp = OtpCode::where('celular', '84999990000')->firstOrFail();

        $this->post(route('cliente.otp.verificar'), ['codigo' => $otp->codigo])
            ->assertRedirect($ordersUrl);
    }

    public function test_checkout_login_overrides_an_old_orders_destination_and_returns_to_checkout(): void
    {
        $this->get(route('cliente.pedidos'));
        $this->assertSame(route('cliente.pedidos'), session('url.intended'));

        $checkoutReturn = route('cliente.cardapio', ['checkout' => 1]);
        $this->get(route('cliente.login', ['origem' => 'checkout']))->assertOk();
        $this->assertSame($checkoutReturn, session('url.intended'));

        $this->post(route('cliente.otp.enviar'), ['celular' => '84999990000']);
        $otp = OtpCode::where('celular', '84999990000')->firstOrFail();

        $this->post(route('cliente.otp.verificar'), ['codigo' => $otp->codigo])
            ->assertRedirect($checkoutReturn);
    }

    public function test_customer_cannot_sign_in_with_an_invalid_code(): void
    {
        $this->post(route('cliente.otp.enviar'), ['celular' => '84999990000']);
        $otp = OtpCode::where('celular', '84999990000')->firstOrFail();
        $invalidCode = str_pad((string) (((int) $otp->codigo + 1) % 1000000), 6, '0', STR_PAD_LEFT);

        $this->from(route('cliente.otp.form'))
            ->post(route('cliente.otp.verificar'), ['codigo' => $invalidCode])
            ->assertRedirect(route('cliente.otp.form'));

        $this->assertGuest('cliente');
        $this->assertDatabaseHas('otp_codes', [
            'celular' => '84999990000',
            'tentativas' => 1,
        ]);
    }

    public function test_guest_cart_is_preserved_after_customer_sign_in(): void
    {
        $sessionId = 'guest-cart-session';
        $this->withSession(['carrinho_session_id' => $sessionId]);
        $item = $this->createCartItem(null, sessionId: $sessionId);

        $this->post(route('cliente.otp.enviar'), ['celular' => '8499990000']);
        $otp = OtpCode::where('celular', '8499990000')->firstOrFail();
        $this->post(route('cliente.otp.verificar'), ['codigo' => $otp->codigo]);

        $cliente = Cliente::where('celular', '8499990000')->firstOrFail();
        $this->assertDatabaseHas('carrinho_itens', [
            'id' => $item->id,
            'cliente_id' => $cliente->id,
            'session_id' => $sessionId,
        ]);
    }

    public function test_cart_remains_in_the_browser_after_logout_and_can_be_managed_as_guest(): void
    {
        $cliente = $this->createCliente();
        $sessionId = 'persistent-browser-cart';
        $item = $this->createCartItem($cliente, sessionId: $sessionId);

        $this->actingAs($cliente, 'cliente')
            ->withSession(['carrinho_session_id' => $sessionId])
            ->post(route('cliente.logout'))
            ->assertRedirect(route('cliente.cardapio'));

        $this->assertGuest('cliente');
        $this->assertSame($sessionId, session('carrinho_session_id'));
        $this->assertDatabaseHas('carrinho_itens', [
            'id' => $item->id,
            'cliente_id' => null,
            'session_id' => $sessionId,
        ]);

        $this->getJson(route('cliente.carrinho.data'))
            ->assertOk()
            ->assertJsonPath('cart_count', 1);
        $this->putJson(route('cliente.carrinho.atualizar', ['item' => $item]), ['quantidade' => 2])
            ->assertOk()
            ->assertJsonPath('cart_count', 2);
    }

    public function test_employee_login_rejects_inactive_users_and_accepts_valid_credentials(): void
    {
        $role = Role::create(['nome' => 'admin']);
        $user = User::create([
            'nome' => 'Operador de teste',
            'email' => 'operador@example.test',
            'senha' => 'senha-segura',
            'role_id' => $role->id,
            'ativo' => true,
        ]);

        $this->post(route('login.post'), [
            'email' => $user->email,
            'password' => 'senha-segura',
        ])->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->ultimo_acesso);
    }

    public function test_employee_login_rejects_inactive_accounts(): void
    {
        $user = $this->createOperador('cozinha');
        $user->update(['ativo' => false]);

        $this->from(route('login'))
            ->post(route('login.post'), [
                'email' => $user->email,
                'password' => 'senha-segura',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_customer_can_update_profile_without_changing_login_phone(): void
    {
        $cliente = $this->createCliente();

        $this->actingAs($cliente, 'cliente')
            ->put(route('cliente.perfil.update'), [
                'nome' => 'Raphael Cliente',
                'email' => 'raphael@example.test',
                'cpf' => '123.456.789-01',
            ])
            ->assertRedirect(route('cliente.perfil.edit'));

        $this->assertDatabaseHas('clientes', [
            'id' => $cliente->id,
            'nome' => 'Raphael Cliente',
            'celular' => '84999990000',
            'email' => 'raphael@example.test',
            'cpf' => '12345678901',
        ]);
    }

    public function test_first_saved_address_becomes_default_and_other_customers_cannot_edit_it(): void
    {
        $cliente = $this->createCliente();
        $outroCliente = $this->createCliente('84999990001');
        $dadosSemApelido = $this->addressData();
        unset($dadosSemApelido['nome_endereco']);

        $this->actingAs($cliente, 'cliente')
            ->get(route('cliente.enderecos.create'))
            ->assertOk()
            ->assertDontSee('Apelido do endereço')
            ->assertSee('https://viacep.com.br/ws/', false);

        $this->actingAs($cliente, 'cliente')
            ->post(route('cliente.enderecos.store'), $dadosSemApelido)
            ->assertRedirect(route('cliente.enderecos.index'));

        $endereco = ClienteEndereco::firstOrFail();
        $this->assertTrue($endereco->padrao);
        $this->assertSame('Endereço principal', $endereco->nome_endereco);

        $dadosAtualizados = $dadosSemApelido;
        $dadosAtualizados['logradouro'] = 'Rua Atualizada';
        $this->actingAs($cliente, 'cliente')
            ->put(route('cliente.enderecos.update', ['endereco' => $endereco]), $dadosAtualizados)
            ->assertRedirect(route('cliente.enderecos.index'));

        $this->assertSame('Rua Atualizada', $endereco->fresh()->logradouro);
        $this->assertSame('Endereço principal', $endereco->fresh()->nome_endereco);

        $this->actingAs($outroCliente, 'cliente')
            ->get(route('cliente.enderecos.edit', ['endereco' => $endereco]))
            ->assertForbidden();
    }

    public function test_customer_cannot_submit_an_address_owned_by_someone_else(): void
    {
        $cliente = $this->createCliente();
        $donoDoEndereco = $this->createCliente('84999990002');
        $endereco = $donoDoEndereco->enderecos()->create($this->addressData());
        $this->createCartItem($cliente);

        $this->actingAs($cliente, 'cliente')
            ->from(route('cliente.checkout'))
            ->post(route('cliente.pedido.finalizar'), [
                'tipo_pedido' => 'delivery',
                'cliente_endereco_id' => $endereco->id,
            ])
            ->assertRedirect(route('cliente.checkout'))
            ->assertSessionHasErrors('cliente_endereco_id');

        $this->assertDatabaseCount('pedidos', 0);
    }

    public function test_customer_can_place_delivery_order_and_clears_the_cart(): void
    {
        $cliente = $this->createCliente();
        $endereco = $cliente->enderecos()->create($this->addressData());
        $this->createCartItem($cliente, quantidade: 2);

        $this->actingAs($cliente, 'cliente')
            ->post(route('cliente.pedido.finalizar'), [
                'tipo_pedido' => 'delivery',
                'cliente_endereco_id' => $endereco->id,
                'observacoes' => 'Sem cebola',
            ])
            ->assertRedirect();

        $pedido = Pedido::with('itens')->sole();
        $this->assertSame('delivery', $pedido->tipo_pedido);
        $this->assertSame($cliente->id, $pedido->cliente_id);
        $this->assertSame($endereco->id, $pedido->cliente_endereco_id);
        $this->assertSame('aberto', $pedido->status);
        $this->assertSame(50.0, (float) $pedido->total);
        $this->assertSame(5.0, (float) $pedido->taxa_entrega);
        $this->assertSame('Sem cebola', $pedido->observacoes);
        $this->assertDatabaseCount('pedido_itens', 1);
        $this->assertDatabaseCount('carrinho_itens', 0);
        $this->assertDatabaseHas('mesas', ['pedido_online_id' => $pedido->id, 'tipo' => 'delivery']);
    }

    public function test_customer_with_generated_name_must_provide_name_when_finishing_first_order(): void
    {
        $cliente = $this->createCliente();
        $cliente->update(['nome' => 'Cliente 0000']);
        $this->createCartItem($cliente);

        $this->actingAs($cliente, 'cliente')
            ->postJson(route('cliente.pedido.finalizar'), ['tipo_pedido' => 'retirada'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('nome_cliente');

        $this->postJson(route('cliente.pedido.finalizar'), [
            'tipo_pedido' => 'retirada',
            'nome_cliente' => 'Ana Cliente',
        ])->assertOk()->assertJson(['success' => true]);

        $this->assertSame('Ana Cliente', $cliente->fresh()->nome);
        $this->assertDatabaseCount('pedidos', 1);
    }

    public function test_customer_cannot_change_another_customers_cart_item(): void
    {
        $cliente = $this->createCliente();
        $donoDoItem = $this->createCliente('84999990003');
        $item = $this->createCartItem($donoDoItem);

        $this->actingAs($cliente, 'cliente')
            ->put(route('cliente.carrinho.atualizar', ['item' => $item]), ['quantidade' => 5])
            ->assertNotFound();

        $this->assertDatabaseHas('carrinho_itens', ['id' => $item->id, 'quantidade' => 1]);
    }

    public function test_kitchen_can_move_an_online_order_through_delivery_statuses(): void
    {
        $cliente = $this->createCliente();
        $endereco = $cliente->enderecos()->create($this->addressData());
        $this->createCartItem($cliente);

        $this->actingAs($cliente, 'cliente')->post(route('cliente.pedido.finalizar'), [
            'tipo_pedido' => 'delivery',
            'cliente_endereco_id' => $endereco->id,
        ]);
        $pedido = Pedido::sole();

        $operador = $this->createOperador('cozinha');
        $this->actingAs($operador)
            ->postJson(route('cozinha.iniciar', ['pedido' => $pedido]))->assertOk()->assertJsonPath('success', true);
        $this->postJson(route('cozinha.pronto', ['pedido' => $pedido]))->assertOk()->assertJsonPath('success', true);
        $this->postJson(route('cozinha.entregar', ['pedido' => $pedido]))->assertOk()->assertJsonPath('success', true);
        $this->postJson(route('cozinha.entregue', ['pedido' => $pedido]))->assertOk()->assertJsonPath('success', true);

        $this->assertSame('entregue', $pedido->fresh()->status);
        $this->assertNotNull($pedido->fresh()->entregue_at);
        $this->assertNotNull($pedido->fresh()->data_finalizacao);
    }

    public function test_admin_can_cancel_an_online_order_and_archive_its_virtual_table(): void
    {
        [$pedido, $mesa] = $this->createOnlineOrderWithItem();
        $admin = $this->createOperador('admin');

        $this->actingAs($admin)
            ->postJson(route('garcom.cancelarPedido', ['pedido' => $pedido]))
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertSame('cancelado', $pedido->fresh()->status);
        $this->assertDatabaseHas('mesas', [
            'id' => $mesa->id,
            'status' => 'disponivel',
            'ativo' => false,
        ]);
    }

    public function test_admin_can_cancel_the_last_online_item_and_archive_its_virtual_table(): void
    {
        [$pedido, $mesa, $item] = $this->createOnlineOrderWithItem();
        $admin = $this->createOperador('admin');

        $this->actingAs($admin)
            ->postJson(route('garcom.cancelarItem', ['item' => $item]))
            ->assertOk()
            ->assertJson([
                'success' => true,
                'pedido_cancelado' => true,
            ]);

        $this->assertSame('cancelado', $pedido->fresh()->status);
        $this->assertDatabaseHas('mesas', [
            'id' => $mesa->id,
            'status' => 'disponivel',
            'ativo' => false,
        ]);
    }

    public function test_delivered_online_table_remains_visible_until_the_order_is_finalized(): void
    {
        [$pedido, $mesa] = $this->createOnlineOrderWithItem();

        $pedido->update(['status' => 'entregue']);
        $this->assertDatabaseHas('mesas', [
            'id' => $mesa->id,
            'status' => 'ocupada',
            'ativo' => true,
        ]);

        $pedido->update(['status' => 'finalizado', 'data_finalizacao' => now()]);
        $this->assertDatabaseHas('mesas', [
            'id' => $mesa->id,
            'status' => 'disponivel',
            'ativo' => false,
        ]);
        $this->assertDatabaseCount('mesas', 1);
    }

    private function createCliente(string $celular = '84999990000'): Cliente
    {
        return Cliente::create([
            'nome' => 'Cliente de teste',
            'celular' => $celular,
            'ativo' => true,
        ]);
    }

    private function createCartItem(?Cliente $cliente, int $quantidade = 1, ?string $sessionId = null): CarrinhoItem
    {
        $categoria = Categoria::firstOrCreate(
            ['nome' => 'Lanches'],
            ['tipo' => 'comida', 'ativo' => true]
        );
        $produto = Produto::create([
            'categoria_id' => $categoria->id,
            'nome' => 'Sanduíche de teste',
            'descricao' => 'Produto para teste automatizado',
            'preco' => 25,
            'tempo_preparo' => 15,
            'ativo' => true,
        ]);

        return CarrinhoItem::create([
            'cliente_id' => $cliente?->id,
            'session_id' => $sessionId ?? ('sessao-cliente-' . $cliente?->id),
            'produto_id' => $produto->id,
            'quantidade' => $quantidade,
            'preco_unitario' => 25,
        ]);
    }

    private function createOnlineOrderWithItem(): array
    {
        $cliente = $this->createCliente();
        $categoria = Categoria::create([
            'nome' => 'Cancelamento',
            'tipo' => 'comida',
            'ativo' => true,
        ]);
        $produto = Produto::create([
            'categoria_id' => $categoria->id,
            'nome' => 'Produto cancelável',
            'descricao' => 'Produto para testar cancelamentos',
            'preco' => 25,
            'tempo_preparo' => 15,
            'ativo' => true,
        ]);
        $pedido = Pedido::create([
            'cliente_id' => $cliente->id,
            'tipo_pedido' => 'retirada',
            'numero_pedido' => 'PED-CANCELAMENTO',
            'status' => 'aberto',
            'total' => 0,
            'data_abertura' => now(),
        ]);
        $mesa = Mesa::criarParaPedidoOnline($pedido);
        $item = PedidoItem::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $produto->id,
            'produto_nome' => $produto->nome,
            'quantidade' => 1,
            'preco_unitario' => 25,
            'subtotal' => 25,
            'status' => 'pendente',
        ]);

        return [$pedido->fresh(), $mesa->fresh(), $item];
    }

    private function createOperador(string $roleName): User
    {
        $role = Role::firstOrCreate(['nome' => $roleName]);

        return User::create([
            'nome' => 'Operador de teste',
            'email' => $roleName . '@example.test',
            'senha' => 'senha-segura',
            'role_id' => $role->id,
            'ativo' => true,
        ]);
    }

    private function addressData(): array
    {
        return [
            'nome_endereco' => 'Casa',
            'cep' => '59000-000',
            'logradouro' => 'Rua de Teste',
            'numero' => '123',
            'complemento' => 'Apto 4',
            'bairro' => 'Centro',
            'cidade' => 'Natal',
            'estado' => 'rn',
            'referencia' => 'Próximo à praça',
        ];
    }
}
