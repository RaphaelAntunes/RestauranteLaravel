<?php

namespace Tests\Feature;

use App\Models\{CarrinhoItem, Categoria, Cliente, ConfiguracaoDelivery, Mesa, OtpCode, Pedido, Produto, ProdutoTamanho, Role, Sabor, User};
use App\Services\OtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeploymentRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_expired_otp_is_rejected(): void
    {
        $otp = OtpCode::gerarCodigo('84999990000');
        $otp->update(['expirado_em' => now()->subMinute()]);
        $this->assertFalse(app(OtpService::class)->validarCodigo($otp->celular, $otp->codigo));
    }

    public function test_used_otp_cannot_be_replayed(): void
    {
        $otp = OtpCode::gerarCodigo('84999990000');
        $service = app(OtpService::class);
        $this->assertTrue($service->validarCodigo($otp->celular, $otp->codigo));
        $this->assertFalse($service->validarCodigo($otp->celular, $otp->codigo));
    }

    public function test_three_invalid_attempts_exhaust_an_otp(): void
    {
        $otp = OtpCode::gerarCodigo('84999990000');
        $invalid = $otp->codigo === '000000' ? '000001' : '000000';
        for ($i = 0; $i < 3; $i++) {
            $this->assertFalse(app(OtpService::class)->validarCodigo($otp->celular, $invalid));
        }
        $this->assertFalse(app(OtpService::class)->validarCodigo($otp->celular, $otp->codigo));
    }

    public function test_inactive_customer_cannot_login_even_with_valid_otp(): void
    {
        $cliente = $this->customer();
        $cliente->update(['ativo' => false]);
        $otp = OtpCode::gerarCodigo($cliente->celular);
        $this->withSession(['otp_celular' => $cliente->celular])
            ->post(route('cliente.otp.verificar'), ['codigo' => $otp->codigo]);
        $this->assertGuest('cliente');
    }

    public function test_invalid_phone_is_rejected_without_creating_otp(): void
    {
        $this->post(route('cliente.otp.enviar'), ['celular' => '123'])
            ->assertRedirect()->assertSessionHasErrors('celular');
        $this->assertDatabaseCount('otp_codes', 0);
    }

    public function test_wrong_employee_password_cannot_login(): void
    {
        $user = $this->employee('admin');
        $this->post(route('login.post'), ['email' => $user->email, 'password' => 'incorrect'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_cart_uses_server_price_not_the_submitted_price(): void
    {
        $product = $this->product();
        $this->postJson(route('cliente.carrinho.adicionar.publico'), [
            'produto_id' => $product->id, 'quantidade' => 2, 'preco_unitario' => 0.01,
        ])->assertOk()->assertJsonPath('success', true);
        $this->assertSame(25.0, (float) CarrinhoItem::sole()->preco_unitario);
    }

    public function test_inactive_product_cannot_be_added(): void
    {
        $product = $this->product();
        $product->update(['ativo' => false]);
        $this->post(route('cliente.carrinho.adicionar.publico'), ['produto_id' => $product->id, 'quantidade' => 1]);
        $this->assertDatabaseCount('carrinho_itens', 0);
    }

    public function test_product_in_inactive_category_cannot_be_added(): void
    {
        $product = $this->product();
        $product->categoria->update(['ativo' => false]);
        $this->post(route('cliente.carrinho.adicionar.publico'), ['produto_id' => $product->id, 'quantidade' => 1]);
        $this->assertDatabaseCount('carrinho_itens', 0);
    }

    public function test_a_size_from_a_different_product_is_rejected(): void
    {
        $product = $this->product();
        $other = $this->product();
        $size = ProdutoTamanho::create(['produto_id' => $other->id, 'nome' => 'M', 'preco' => 35, 'max_sabores' => 2, 'ativo' => true]);
        $this->post(route('cliente.carrinho.adicionar.publico'), ['produto_id' => $product->id, 'produto_tamanho_id' => $size->id, 'quantidade' => 1]);
        $this->assertDatabaseCount('carrinho_itens', 0);
    }

    public function test_flavor_price_is_calculated_from_the_largest_valid_flavor(): void
    {
        $product = $this->product();
        $size = ProdutoTamanho::create(['produto_id' => $product->id, 'nome' => 'M', 'preco' => 35, 'max_sabores' => 2, 'ativo' => true]);
        $flavor = Sabor::create(['categoria_id' => $product->categoria_id, 'nome' => 'Especial', 'preco_m' => 45, 'ativo' => true]);
        $this->postJson(route('cliente.carrinho.adicionar.publico'), ['produto_id' => $product->id, 'produto_tamanho_id' => $size->id, 'quantidade' => 1, 'sabores' => [$flavor->id], 'preco_unitario' => 1])
            ->assertOk()->assertJsonPath('success', true);
        $this->assertSame(45.0, (float) CarrinhoItem::sole()->preco_unitario);
    }

    public function test_flavor_from_another_category_is_rejected(): void
    {
        $product = $this->product();
        $other = $this->product();
        $size = ProdutoTamanho::create(['produto_id' => $product->id, 'nome' => 'M', 'preco' => 35, 'max_sabores' => 2, 'ativo' => true]);
        $flavor = Sabor::create(['categoria_id' => $other->categoria_id, 'nome' => 'Outra categoria', 'preco_m' => 45, 'ativo' => true]);
        $this->post(route('cliente.carrinho.adicionar.publico'), ['produto_id' => $product->id, 'produto_tamanho_id' => $size->id, 'quantidade' => 1, 'sabores' => [$flavor->id]]);
        $this->assertDatabaseCount('carrinho_itens', 0);
    }

    public function test_pickup_order_does_not_charge_delivery(): void
    {
        $customer = $this->customer();
        $this->cart($customer);
        $this->actingAs($customer, 'cliente')->post(route('cliente.pedido.finalizar'), ['tipo_pedido' => 'retirada'])->assertRedirect();
        $order = Pedido::sole();
        $this->assertSame(0.0, (float) $order->taxa_entrega);
        $this->assertSame(25.0, (float) $order->total);
    }

    public function test_empty_cart_cannot_create_an_order(): void
    {
        $this->actingAs($this->customer(), 'cliente')->post(route('cliente.pedido.finalizar'), ['tipo_pedido' => 'retirada'])
            ->assertRedirect(route('cliente.cardapio'));
        $this->assertDatabaseCount('pedidos', 0);
    }

    public function test_minimum_order_is_enforced(): void
    {
        $customer = $this->customer();
        $this->cart($customer);
        ConfiguracaoDelivery::obter()->update(['pedido_minimo' => 50]);
        $this->actingAs($customer, 'cliente')->post(route('cliente.pedido.finalizar'), ['tipo_pedido' => 'retirada'])->assertSessionHas('error');
        $this->assertDatabaseCount('pedidos', 0);
    }

    public function test_delivery_outside_opening_hours_is_rejected(): void
    {
        $this->travelTo(now()->setTime(10, 0));
        $customer = $this->customer();
        $address = $this->address($customer);
        $this->cart($customer);
        ConfiguracaoDelivery::obter()->update(['ativo' => true, 'horario_inicio' => '18:00:00', 'horario_fim' => '23:00:00']);
        $this->actingAs($customer, 'cliente')->post(route('cliente.pedido.finalizar'), ['tipo_pedido' => 'delivery', 'cliente_endereco_id' => $address->id])->assertSessionHas('error');
        $this->assertDatabaseCount('pedidos', 0);
    }

    public function test_customer_cannot_track_another_customers_order(): void
    {
        $owner = $this->customer();
        $this->cart($owner);
        $this->actingAs($owner, 'cliente')->post(route('cliente.pedido.finalizar'), ['tipo_pedido' => 'retirada']);
        $order = Pedido::sole();
        $other = $this->customer('84999990001');
        $this->actingAs($other, 'cliente')->get(route('cliente.pedido.acompanhar', ['pedido' => $order]))->assertForbidden();
        $this->getJson(route('cliente.pedido.status', ['pedido' => $order]))->assertForbidden();
    }

    public function test_cashier_cannot_access_admin_only_settings_or_users(): void
    {
        $this->actingAs($this->employee('caixa'));
        $this->get(route('admin.configuracoes-delivery.edit'))->assertRedirect(route('pdv.index'));
        $this->get(route('garcons.index'))->assertRedirect(route('pdv.index'));
        $this->get(route('relatorios.index'))->assertRedirect(route('pdv.index'));
    }

    public function test_kitchen_cannot_access_the_cash_register(): void
    {
        $this->actingAs($this->employee('cozinha'))->get(route('pdv.index'))->assertRedirect(route('cozinha.index'));
    }

    public function test_order_cannot_skip_kitchen_statuses(): void
    {
        $owner = $this->customer();
        $this->cart($owner);
        $this->actingAs($owner, 'cliente')->post(route('cliente.pedido.finalizar'), ['tipo_pedido' => 'retirada']);
        $order = Pedido::sole();
        $this->actingAs($this->employee('cozinha'))->postJson(route('cozinha.pronto', ['pedido' => $order]))->assertStatus(400);
        $this->assertSame('aberto', $order->fresh()->status);
    }

    public function test_cashier_can_settle_a_table_without_optional_observations(): void
    {
        [$mesa, $order] = $this->tableOrder();
        $this->actingAs($this->employee('caixa'))->post(route('pdv.pagamento', ['mesa' => $mesa]), ['forma_pagamento' => 'dinheiro', 'valor_pago' => 30])->assertRedirect();
        $this->assertDatabaseCount('pagamentos', 1);
        $this->assertSame('finalizado', $order->fresh()->status);
        $this->assertSame('disponivel', $mesa->fresh()->status);
        $this->assertDatabaseHas('pagamentos', ['valor_total' => 25, 'troco' => 5]);
    }

    public function test_cashier_can_settle_an_online_order_without_deleting_its_virtual_table(): void
    {
        $customer = $this->customer();
        $this->cart($customer);
        $this->actingAs($customer, 'cliente')->post(route('cliente.pedido.finalizar'), [
            'tipo_pedido' => 'retirada',
        ]);

        $order = Pedido::sole();
        $mesa = Mesa::where('pedido_online_id', $order->id)->sole();

        $this->actingAs($this->employee('caixa'))->post(route('pdv.pagamento', ['mesa' => $mesa]), [
            'forma_pagamento' => 'pix',
            'valor_pago' => 30,
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertSame('finalizado', $order->fresh()->status);
        $this->assertDatabaseHas('mesas', [
            'id' => $mesa->id,
            'status' => 'disponivel',
            'ativo' => false,
        ]);
        $this->assertDatabaseHas('pagamentos', ['mesa_id' => $mesa->id]);
    }

    public function test_insufficient_payment_does_not_close_the_table(): void
    {
        [$mesa, $order] = $this->tableOrder();
        $transactionLevel = \Illuminate\Support\Facades\DB::transactionLevel();
        $this->actingAs($this->employee('caixa'))->post(route('pdv.pagamento', ['mesa' => $mesa]), ['forma_pagamento' => 'dinheiro', 'valor_pago' => 1])->assertSessionHas('error');
        $this->assertDatabaseCount('pagamentos', 0);
        $this->assertSame('aberto', $order->fresh()->status);
        $this->assertSame('ocupada', $mesa->fresh()->status);
        $this->assertSame($transactionLevel, \Illuminate\Support\Facades\DB::transactionLevel());
    }

    public function test_waiter_cannot_bypass_payment_permission_by_omitting_source(): void
    {
        [$mesa, $order] = $this->tableOrder();
        $this->actingAs($this->employee('garcom'))->post(route('garcom.pagamento', ['mesa' => $mesa]), ['forma_pagamento' => 'dinheiro', 'valor_pago' => 25, 'observacoes' => 'Teste de permissao'])->assertForbidden();
        $this->assertDatabaseCount('pagamentos', 0);
    }

    public function test_authorized_waiter_can_settle_a_table(): void
    {
        [$mesa, $order] = $this->tableOrder();
        $waiter = $this->employee('garcom');
        $waiter->update(['pode_fechar_mesas' => true]);
        $this->actingAs($waiter)->post(route('garcom.pagamento', ['mesa' => $mesa]), ['forma_pagamento' => 'pix', 'valor_pago' => 25])->assertRedirect();
        $this->assertDatabaseCount('pagamentos', 1);
        $this->assertSame('finalizado', $order->fresh()->status);
    }

    public function test_free_delivery_threshold_is_calculated_on_server(): void
    {
        $config = ConfiguracaoDelivery::obter();
        $config->update(['tipo_taxa' => 'gratis_acima', 'valor_minimo_gratis' => 50]);
        $this->assertSame(0.0, $config->calcularTaxaEntrega(50));
        $this->assertSame(5.0, (float) $config->calcularTaxaEntrega(49));
    }

    public function test_settling_an_empty_table_does_not_leave_a_transaction_open(): void
    {
        $mesa = Mesa::create(['numero' => 1, 'status' => 'disponivel', 'ativo' => true]);
        $transactionLevel = \Illuminate\Support\Facades\DB::transactionLevel();
        $this->actingAs($this->employee('caixa'))->post(route('pdv.pagamento', ['mesa' => $mesa]), ['forma_pagamento' => 'pix', 'valor_pago' => 25])->assertSessionHas('error');
        $this->assertDatabaseCount('pagamentos', 0);
        $this->assertSame($transactionLevel, \Illuminate\Support\Facades\DB::transactionLevel());
    }

    public function test_admin_can_open_delivery_settings(): void
    {
        $this->actingAs($this->employee('admin'))->get(route('admin.configuracoes-delivery.edit'))
            ->assertOk()->assertSee('Salvar configurações');
    }

    public function test_admin_can_save_delivery_settings(): void
    {
        $this->actingAs($this->employee('admin'))->put(route('admin.configuracoes-delivery.update'), [
            'tipo_taxa' => 'gratis_acima', 'valor_minimo_gratis' => 80,
            'tempo_medio_preparo' => 35, 'pedido_minimo' => 20, 'ativo' => 1,
            'horario_inicio' => '18:00', 'horario_fim' => '23:00',
            'dias_funcionamento' => ['sex', 'sab'],
        ])->assertRedirect()->assertSessionHasNoErrors()->assertSessionHas('success');
        $this->assertDatabaseHas('configuracoes_delivery', ['tipo_taxa' => 'gratis_acima', 'valor_minimo_gratis' => 80, 'pedido_minimo' => 20]);
    }

    private function customer(string $phone = '84999990000'): Cliente
    {
        return Cliente::create(['nome' => 'Cliente sintetico', 'celular' => $phone, 'ativo' => true]);
    }

    private function employee(string $name): User
    {
        return User::create(['nome' => 'Operador sintetico', 'email' => $name.'@example.test', 'senha' => 'test-password', 'role_id' => Role::firstOrCreate(['nome' => $name])->id, 'ativo' => true, 'pode_fechar_mesas' => false]);
    }

    private function product(): Produto
    {
        $category = Categoria::create(['nome' => 'Categoria '.Categoria::count(), 'tipo' => 'comida', 'ativo' => true]);
        return Produto::create(['categoria_id' => $category->id, 'nome' => 'Produto sintetico', 'preco' => 25, 'ativo' => true]);
    }

    private function cart(Cliente $customer): CarrinhoItem
    {
        return CarrinhoItem::create(['cliente_id' => $customer->id, 'session_id' => 'test-cart-'.$customer->id, 'produto_id' => $this->product()->id, 'quantidade' => 1, 'preco_unitario' => 25]);
    }

    private function address(Cliente $customer)
    {
        return $customer->enderecos()->create(['nome_endereco' => 'Teste', 'cep' => '59000000', 'logradouro' => 'Rua de Teste', 'numero' => '1', 'bairro' => 'Centro', 'cidade' => 'Natal', 'estado' => 'RN']);
    }

    private function tableOrder(): array
    {
        $user = $this->employee('admin');
        $mesa = Mesa::create(['numero' => 1, 'status' => 'ocupada', 'ativo' => true]);
        $order = Pedido::create(['numero_pedido' => 'TEST-001', 'mesa_id' => $mesa->id, 'user_id' => $user->id, 'status' => 'aberto', 'total' => 25, 'data_abertura' => now()]);
        return [$mesa, $order];
    }
}
