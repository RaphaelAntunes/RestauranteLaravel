<?php

namespace Tests\Feature;

use App\Models\{CarrinhoItem, Categoria, Cliente, OtpCode, Produto};
use App\Support\EntradaDemonstracao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EntradaDemonstracaoTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitor_enters_without_otp_and_keeps_only_their_cart(): void
    {
        config(['demonstracao.entrada_cliente' => true]);
        $real = Cliente::create(['nome' => 'Conta anterior', 'celular' => '84999990000', 'ativo' => true]);
        $categoria = Categoria::create(['nome' => 'Entradas', 'tipo' => 'comida', 'ativo' => true]);
        $produto = Produto::create(['categoria_id' => $categoria->id, 'nome' => 'Pão', 'preco' => 15, 'ativo' => true]);
        foreach (['esta-sacola', 'outra-sacola'] as $chave) {
            CarrinhoItem::create(['session_id' => $chave, 'produto_id' => $produto->id, 'quantidade' => 1, 'preco_unitario' => 15]);
        }
        $this->withSession(['carrinho_session_id' => 'esta-sacola'])
            ->post(route('cliente.otp.enviar'), ['celular' => '(84) 99999-0000'])
            ->assertRedirect(route('cliente.cardapio', ['loja' => 'loja-teste']))->assertSessionMissing('otp_celular');
        $visitante = auth('cliente')->user();
        $this->assertNotNull($visitante);
        $this->assertNotEquals($real->id, $visitante->id);
        $this->assertStringStartsWith('~', $visitante->celular);
        $this->assertGuest('web');
        $this->assertSame(0, OtpCode::count());
        $this->assertDatabaseHas('carrinho_itens', ['session_id' => 'esta-sacola', 'cliente_id' => $visitante->id]);
        $this->assertDatabaseHas('carrinho_itens', ['session_id' => 'outra-sacola', 'cliente_id' => null]);
        $this->post(route('cliente.logout'));
        $this->post(route('cliente.otp.enviar'), ['celular' => '84999990000'])
            ->assertRedirect(route('cliente.cardapio', ['loja' => 'loja-teste']));
        $this->assertNotEquals($visitante->id, auth('cliente')->id());
    }

    public function test_disabling_demo_removes_access_on_next_request(): void
    {
        config(['demonstracao.entrada_cliente' => true]);
        $this->post(route('cliente.otp.enviar'), ['celular' => '84999990000']);
        config(['demonstracao.entrada_cliente' => false]);
        $this->get(route('cliente.perfil.edit'))->assertRedirect(route('cliente.login', ['loja' => 'loja-teste']));
        $this->assertGuest('cliente');
    }

    public function test_demo_rejects_invalid_phone_without_creating_customer(): void
    {
        config(['demonstracao.entrada_cliente' => true]);
        $this->post(route('cliente.otp.enviar'), ['celular' => '123'])->assertSessionHasErrors('celular');
        $this->assertGuest('cliente');
        $this->assertSame(0, Cliente::count());
    }

    public function test_demo_is_not_available_in_production_even_if_flag_is_true(): void
    {
        config(['demonstracao.entrada_cliente' => true]);
        $this->app->instance('env', 'production');
        try {
            $this->assertFalse(EntradaDemonstracao::ativa());
            $token = bin2hex(random_bytes(20));
            $this->withSession(['_token' => $token])->post(route('cliente.otp.enviar'), [
                '_token' => $token, 'celular' => '84999990000',
            ])->assertRedirect(route('cliente.otp.form'));
            $this->assertGuest('cliente');
        } finally {
            $this->app->instance('env', 'testing');
        }
    }
}
