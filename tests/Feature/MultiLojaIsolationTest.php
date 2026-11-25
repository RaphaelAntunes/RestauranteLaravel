<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Loja;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Produto;
use App\Models\Role;
use App\Models\User;
use App\Support\LojaContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class MultiLojaIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_menu_only_displays_products_from_its_store(): void
    {
        $produtoA = $this->productFor($this->loja, 'Prato da Loja A');
        $lojaB = $this->newStore('loja-b');
        $produtoB = $this->productFor($lojaB, 'Prato da Loja B');

        $this->get(route('cliente.cardapio', ['loja' => $this->loja->slug]))
            ->assertOk()
            ->assertSee($produtoA->nome)
            ->assertDontSee($produtoB->nome);

        $this->get(route('cliente.cardapio', ['loja' => $lojaB->slug]))
            ->assertOk()
            ->assertSee($produtoB->nome)
            ->assertDontSee($produtoA->nome);
    }

    public function test_order_tracking_request_uses_the_current_store_url(): void
    {
        $this->get(route('cliente.cardapio', ['loja' => $this->loja->slug]))
            ->assertOk()
            ->assertSee(
                'restaurantes\\/loja-teste\\/pedido\\/__PEDIDO_ID__\\/status',
                false
            )
            ->assertSee('window.location.assign(data.redirect_url)', false)
            ->assertDontSee('/cliente/pedido/', false);
    }

    public function test_product_from_another_store_cannot_be_added_to_cart(): void
    {
        $produtoB = $this->productFor($this->newStore('loja-b'), 'Produto externo');

        $this->postJson(route('cliente.carrinho.adicionar.publico', ['loja' => $this->loja->slug]), [
            'produto_id' => $produtoB->id,
            'quantidade' => 1,
        ])->assertUnprocessable();

        $this->assertDatabaseCount('carrinho_itens', 0);
    }

    public function test_same_customer_phone_and_employee_email_are_allowed_in_distinct_stores(): void
    {
        $role = Role::firstOrCreate(['nome' => 'admin']);
        $this->inStore($this->loja, function () use ($role): void {
            Cliente::create(['nome' => 'Cliente A', 'celular' => '84999990000', 'ativo' => true]);
            User::create(['nome' => 'Dono A', 'email' => 'dono@example.test', 'senha' => 'segredo-123', 'role_id' => $role->id, 'ativo' => true]);
        });

        $lojaB = $this->newStore('loja-b');
        $this->inStore($lojaB, function () use ($role): void {
            Cliente::create(['nome' => 'Cliente B', 'celular' => '84999990000', 'ativo' => true]);
            User::create(['nome' => 'Dono B', 'email' => 'dono@example.test', 'senha' => 'segredo-123', 'role_id' => $role->id, 'ativo' => true]);
        });

        $this->assertDatabaseCount('clientes', 2);
        $this->assertDatabaseCount('users', 2);
    }

    public function test_store_forms_validate_unique_values_only_inside_the_current_store(): void
    {
        $adminRole = Role::firstOrCreate(['nome' => 'admin']);
        Role::firstOrCreate(['nome' => 'garcom']);

        $this->inStore($this->loja, function (): void {
            Categoria::create(['nome' => 'Mais pedidos', 'tipo' => 'comida', 'ordem' => 1, 'ativo' => true]);
            Mesa::create(['numero' => 12, 'capacidade' => 4, 'status' => 'disponivel', 'ativo' => true]);
            Cliente::create([
                'nome' => 'Cliente da loja A',
                'celular' => '84999990001',
                'email' => 'cliente@compartilhado.test',
                'cpf' => '12345678901',
                'ativo' => true,
            ]);
        });

        $garcomRole = Role::where('nome', 'garcom')->firstOrFail();
        $this->inStore($this->loja, fn () => User::create([
            'nome' => 'Garcom da loja A',
            'email' => 'garcom@compartilhado.test',
            'senha' => 'segredo-123',
            'role_id' => $garcomRole->id,
            'ativo' => true,
        ]));

        $lojaB = $this->newStore('loja-b');
        $adminB = $this->inStore($lojaB, fn () => User::create([
            'nome' => 'Administrador B',
            'email' => 'admin-b@example.test',
            'senha' => 'segredo-123',
            'role_id' => $adminRole->id,
            'ativo' => true,
        ]));
        $clienteB = $this->inStore($lojaB, fn () => Cliente::create([
            'nome' => 'Cliente da loja B',
            'celular' => '84999990002',
            'ativo' => true,
        ]));

        $this->actingAs($adminB)
            ->post(route('categorias.store', ['loja' => $lojaB->slug]), [
                'nome' => 'Mais pedidos',
                'descricao' => 'Categoria própria da loja B',
                'ordem' => 1,
            ])->assertSessionHasNoErrors()->assertRedirectContains('/restaurantes/loja-b/painel/categorias');

        $this->actingAs($adminB)
            ->post(route('mesas.store', ['loja' => $lojaB->slug]), [
                'numero' => 12,
                'capacidade' => 4,
                'status' => 'disponivel',
            ])->assertSessionHasNoErrors()->assertRedirectContains('/restaurantes/loja-b/painel/mesas');

        $this->actingAs($adminB)
            ->post(route('garcons.store', ['loja' => $lojaB->slug]), [
                'nome' => 'Garcom da loja B',
                'email' => 'garcom@compartilhado.test',
                'senha' => 'segredo-123',
            ])->assertSessionHasNoErrors()->assertRedirectContains('/restaurantes/loja-b/painel/garcons');

        $this->actingAs($clienteB, 'cliente')
            ->put(route('cliente.perfil.update', ['loja' => $lojaB->slug]), [
                'nome' => 'Cliente da loja B',
                'email' => 'cliente@compartilhado.test',
                'cpf' => '123.456.789-01',
            ])->assertSessionHasNoErrors()->assertRedirectContains('/restaurantes/loja-b/perfil');

        $this->assertDatabaseHas('categorias', ['loja_id' => $lojaB->id, 'nome' => 'Mais pedidos']);
        $this->assertDatabaseHas('mesas', ['loja_id' => $lojaB->id, 'numero' => 12]);
        $this->assertDatabaseHas('users', ['loja_id' => $lojaB->id, 'email' => 'garcom@compartilhado.test']);
        $this->assertDatabaseHas('clientes', [
            'loja_id' => $lojaB->id,
            'email' => 'cliente@compartilhado.test',
            'cpf' => '12345678901',
        ]);
    }

    public function test_employee_management_actions_keep_the_store_prefix(): void
    {
        $adminRole = Role::firstOrCreate(['nome' => 'admin']);
        $garcomRole = Role::firstOrCreate(['nome' => 'garcom']);
        $admin = $this->inStore($this->loja, fn () => User::create([
            'nome' => 'Administrador',
            'email' => 'admin@example.test',
            'senha' => 'segredo-123',
            'role_id' => $adminRole->id,
            'ativo' => true,
        ]));
        $this->inStore($this->loja, fn () => User::create([
            'nome' => 'Garcom',
            'email' => 'garcom@example.test',
            'senha' => 'segredo-123',
            'role_id' => $garcomRole->id,
            'ativo' => true,
        ]));

        $this->actingAs($admin)
            ->get(route('garcons.index', ['loja' => $this->loja->slug]))
            ->assertOk()
            ->assertSee('restaurantes\\/loja-teste\\/painel\\/garcons\\/__GARCOM__\\/permissions', false)
            ->assertSee('restaurantes\\/loja-teste\\/painel\\/garcons\\/__GARCOM__\\/toggle-status', false)
            ->assertDontSee('facial_obrigatorio', false)
            ->assertDontSee('Facial Cadastrado')
            ->assertDontSee('fetch(`/garcons/', false);
    }

    public function test_facial_authentication_is_no_longer_exposed(): void
    {
        $this->get(route('login', ['loja' => $this->loja->slug]))
            ->assertOk()
            ->assertDontSee('Reconhecimento Facial')
            ->assertDontSee('/face/login', false);

        $this->get('/restaurantes/'.$this->loja->slug.'/painel/face/login')
            ->assertNotFound();
        $this->post('/restaurantes/'.$this->loja->slug.'/painel/face/login', ['image' => 'unused'])
            ->assertNotFound();
    }

    public function test_pdv_item_actions_keep_the_store_prefix(): void
    {
        $adminRole = Role::firstOrCreate(['nome' => 'admin']);
        $admin = $this->inStore($this->loja, fn () => User::create([
            'nome' => 'Administrador',
            'email' => 'admin-pdv@example.test',
            'senha' => 'segredo-123',
            'role_id' => $adminRole->id,
            'ativo' => true,
        ]));
        $mesa = $this->inStore($this->loja, fn () => Mesa::create([
            'numero' => 21,
            'capacidade' => 4,
            'status' => 'ocupada',
            'ativo' => true,
        ]));
        $produto = $this->productFor($this->loja, 'Produto do PDV');
        $pedido = $this->inStore($this->loja, fn () => Pedido::create([
            'mesa_id' => $mesa->id,
            'user_id' => $admin->id,
            'numero_pedido' => 'TESTE-PDV-001',
            'status' => 'aberto',
            'tipo_pedido' => 'mesa',
            'total' => 0,
        ]));
        $this->inStore($this->loja, fn () => PedidoItem::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $produto->id,
            'quantidade' => 1,
            'preco_unitario' => 25,
            'subtotal' => 25,
            'status' => 'pendente',
        ]));

        $this->actingAs($admin)
            ->get(route('pdv.fechar', ['loja' => $this->loja->slug, 'mesa' => $mesa]))
            ->assertOk()
            ->assertSee('restaurantes\\/loja-teste\\/painel\\/garcom\\/item\\/__ITEM__\\/atualizar-quantidade', false)
            ->assertSee('restaurantes\\/loja-teste\\/painel\\/garcom\\/item\\/__ITEM__\\/cancelar', false)
            ->assertDontSee('fetch(`/garcom/item/', false);
    }

    public function test_employee_session_cannot_be_used_in_another_store(): void
    {
        $role = Role::firstOrCreate(['nome' => 'admin']);
        $user = $this->inStore($this->loja, fn () => User::create([
            'nome' => 'Dono A',
            'email' => 'dono-a@example.test',
            'senha' => 'segredo-123',
            'role_id' => $role->id,
            'ativo' => true,
        ]));
        $lojaB = $this->newStore('loja-b');

        $this->actingAs($user)
            ->get(route('home', ['loja' => $lojaB->slug]))
            ->assertRedirect(route('login', ['loja' => $lojaB->slug]));

        $this->assertGuest();
    }

    public function test_customer_session_cannot_be_used_in_another_store(): void
    {
        $cliente = $this->inStore($this->loja, fn () => Cliente::create([
            'nome' => 'Cliente A',
            'celular' => '84999990000',
            'ativo' => true,
        ]));
        $lojaB = $this->newStore('loja-b');

        $this->actingAs($cliente, 'cliente')
            ->get(route('cliente.perfil.edit', ['loja' => $lojaB->slug]))
            ->assertRedirect(route('cliente.login', ['loja' => $lojaB->slug]));

        $this->assertGuest('cliente');
    }

    public function test_platform_admin_can_create_a_store_with_its_owner(): void
    {
        $role = Role::firstOrCreate(['nome' => 'admin']);
        $platformAdmin = $this->inStore($this->loja, fn () => User::create([
            'nome' => 'Administrador da plataforma',
            'email' => 'plataforma@example.test',
            'senha' => 'segredo-123',
            'role_id' => $role->id,
            'ativo' => true,
            'is_platform_admin' => true,
        ]));

        $this->actingAs($platformAdmin)->post(route('platform.lojas.store'), [
            'nome' => 'Nova Pizzaria',
            'slug' => 'nova-pizzaria',
            'descricao' => 'Loja criada no teste automatizado.',
            'owner_nome' => 'Proprietário',
            'owner_email' => 'proprietario@example.test',
            'owner_password' => 'senha-forte-123',
            'owner_password_confirmation' => 'senha-forte-123',
        ])->assertRedirect(route('platform.lojas.index'));

        $novaLoja = Loja::where('slug', 'nova-pizzaria')->firstOrFail();
        $this->assertSame('Cardápio da casa', $novaLoja->titulo_cardapio);
        $this->assertTrue($novaLoja->exibir_textos_banner);
        $this->assertDatabaseHas('users', [
            'loja_id' => $novaLoja->id,
            'email' => 'proprietario@example.test',
            'ativo' => true,
        ]);
        $this->assertDatabaseHas('configuracoes_delivery', ['loja_id' => $novaLoja->id]);
    }

    public function test_whatsapp_button_is_only_visible_when_enabled_with_a_valid_number(): void
    {
        $this->loja->update([
            'whatsapp' => '(84) 99999-0000',
            'whatsapp_ativo' => true,
        ]);

        $this->get(route('cliente.cardapio', ['loja' => $this->loja->slug]))
            ->assertOk()
            ->assertSee('db-whatsapp-float')
            ->assertSee('https://wa.me/5584999990000', false);

        $this->loja->update(['whatsapp_ativo' => false]);

        $this->get(route('cliente.cardapio', ['loja' => $this->loja->slug]))
            ->assertOk()
            ->assertDontSee('db-whatsapp-float');
    }

    public function test_store_owner_can_upload_an_avif_banner(): void
    {
        Storage::fake('public');
        $role = Role::firstOrCreate(['nome' => 'admin']);
        $owner = $this->inStore($this->loja, fn () => User::create([
            'nome' => 'Dono da loja',
            'email' => 'dono-banner@example.test',
            'senha' => 'segredo-123',
            'role_id' => $role->id,
            'ativo' => true,
        ]));

        $temporaryPath = tempnam(sys_get_temp_dir(), 'banner-avif-');
        file_put_contents($temporaryPath, 'avif-test-content');
        $banner = new class($temporaryPath, 'banner.avif', null, UPLOAD_ERR_OK, true) extends UploadedFile
        {
            public function getMimeType(): ?string
            {
                return 'image/avif';
            }
        };
        $temporaryMobilePath = tempnam(sys_get_temp_dir(), 'banner-mobile-avif-');
        file_put_contents($temporaryMobilePath, 'mobile-avif-test-content');
        $mobileBanner = new class($temporaryMobilePath, 'banner-mobile.avif', null, UPLOAD_ERR_OK, true) extends UploadedFile
        {
            public function getMimeType(): ?string
            {
                return 'image/avif';
            }
        };

        $this->actingAs($owner)
            ->put(route('admin.loja.update', ['loja' => $this->loja->slug]), [
                'nome' => $this->loja->nome,
                'slug' => $this->loja->slug,
                'titulo_cardapio' => 'Sabor feito em casa',
                'cor_primaria' => '#293DAE',
                'cor_secundaria' => '#D9654C',
                'banner' => $banner,
                'banner_mobile' => $mobileBanner,
            ])
            ->assertRedirect();

        $loja = $this->loja->fresh();
        Storage::disk('public')->assertExists($loja->banner_path);
        Storage::disk('public')->assertExists($loja->banner_mobile_path);
        $this->assertFalse($loja->exibir_textos_banner);

        $customBannerResponse = $this->get(route('cliente.cardapio', ['loja' => $loja->slug]));
        $customBannerResponse
            ->assertOk()
            ->assertSee('db-hero-art-only')
            ->assertSee('db-hero-ops')
            ->assertSee('db-status')
            ->assertDontSee('Sabor feito em casa')
            ->assertSee('storage/'.$loja->banner_path, false)
            ->assertSee('storage/'.$loja->banner_mobile_path, false);
        $this->assertMatchesRegularExpression('/Aberto para pedidos|Pedidos fechados/', $customBannerResponse->getContent());

        $desktopPath = $loja->banner_path;
        $mobilePath = $loja->banner_mobile_path;

        $this->actingAs($owner)
            ->put(route('admin.loja.update', ['loja' => $loja->slug]), [
                'nome' => $loja->nome,
                'slug' => $loja->slug,
                'titulo_cardapio' => 'Cardápio da casa',
                'cor_primaria' => '#293DAE',
                'cor_secundaria' => '#D9654C',
                'exibir_textos_banner' => '1',
                'remover_banner' => '1',
                'remover_banner_mobile' => '1',
            ])
            ->assertRedirect();

        $loja = $loja->fresh();
        $this->assertNull($loja->banner_path);
        $this->assertNull($loja->banner_mobile_path);
        Storage::disk('public')->assertMissing($desktopPath);
        Storage::disk('public')->assertMissing($mobilePath);

        $this->get(route('cliente.cardapio', ['loja' => $loja->slug]))
            ->assertOk()
            ->assertSee('dobra/banner-padrao.avif', false)
            ->assertDontSee('storage/'.$desktopPath, false)
            ->assertDontSee('storage/'.$mobilePath, false);
    }

    private function newStore(string $slug): Loja
    {
        return Loja::create(['nome' => str($slug)->headline(), 'slug' => $slug, 'status' => 'ativa', 'plano' => 'teste']);
    }

    private function productFor(Loja $loja, string $name): Produto
    {
        return $this->inStore($loja, function () use ($name): Produto {
            $categoria = Categoria::create(['nome' => 'Categoria '.$name, 'tipo' => 'comida', 'ativo' => true]);

            return Produto::create([
                'categoria_id' => $categoria->id,
                'nome' => $name,
                'preco' => 25,
                'ativo' => true,
            ]);
        });
    }

    private function inStore(Loja $loja, callable $callback): mixed
    {
        $context = app(LojaContext::class);
        $context->set($loja);
        URL::defaults(['loja' => $loja->slug]);

        try {
            return $callback();
        } finally {
            $context->set($this->loja);
            URL::defaults(['loja' => $this->loja->slug]);
        }
    }
}
