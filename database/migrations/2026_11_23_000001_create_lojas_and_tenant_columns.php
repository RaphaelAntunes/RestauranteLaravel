<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    private array $tenantTables = [
        'users', 'mesas', 'categorias', 'produtos', 'pedidos', 'pedido_itens',
        'pagamentos', 'pagamento_detalhes', 'logs', 'configuracoes',
        'produto_tamanhos', 'sabores', 'pedido_item_sabores', 'clientes',
        'cliente_enderecos', 'otp_codes', 'carrinho_itens',
        'carrinho_item_sabores', 'configuracoes_delivery',
    ];

    public function up(): void
    {
        Schema::create('lojas', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 120);
            $table->string('slug', 120)->unique();
            $table->text('descricao')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('banner_path')->nullable();
            $table->string('cor_primaria', 7)->default('#E5484D');
            $table->string('cor_secundaria', 7)->default('#151515');
            $table->string('telefone', 30)->nullable();
            $table->string('whatsapp', 30)->nullable();
            $table->text('endereco')->nullable();
            $table->string('status', 20)->default('ativa')->index();
            $table->string('plano', 40)->default('piloto');
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamps();
        });

        Schema::create('loja_banners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loja_id')->constrained('lojas')->cascadeOnDelete();
            $table->string('titulo', 120)->nullable();
            $table->string('subtitulo', 220)->nullable();
            $table->string('imagem_path');
            $table->string('link')->nullable();
            $table->unsignedInteger('ordem')->default(0);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            $table->index(['loja_id', 'ativo', 'ordem']);
        });

        foreach ($this->tenantTables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignId('loja_id')->nullable()->after('id')->constrained('lojas')->restrictOnDelete();
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_platform_admin')->default(false)->after('loja_id');
        });

        $nome = DB::table('configuracoes')->where('chave', 'nome_restaurante')->value('valor') ?: 'Dobra';
        $slug = Str::slug((string) $nome) ?: 'loja-principal';
        $lojaId = DB::table('lojas')->insertGetId([
            'nome' => $nome,
            'slug' => $slug,
            'status' => 'ativa',
            'plano' => 'fundador',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ($this->tenantTables as $tableName) {
            DB::table($tableName)->whereNull('loja_id')->update(['loja_id' => $lojaId]);
        }

        $firstAdmin = DB::table('users')
            ->join('roles', 'roles.id', '=', 'users.role_id')
            ->where('roles.nome', 'admin')
            ->orderBy('users.id')
            ->value('users.id');
        if ($firstAdmin) {
            DB::table('users')->where('id', $firstAdmin)->update(['is_platform_admin' => true]);
        }

        $this->replaceGlobalUniqueIndexes();
    }

    private function replaceGlobalUniqueIndexes(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_email_unique');
            $table->unique(['loja_id', 'email']);
        });
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropUnique('clientes_celular_unique');
            $table->dropUnique('clientes_cpf_unique');
            $table->unique(['loja_id', 'celular']);
            $table->unique(['loja_id', 'cpf']);
        });
        Schema::table('configuracoes', function (Blueprint $table) {
            $table->dropUnique('configuracoes_chave_unique');
            $table->unique(['loja_id', 'chave']);
        });
        Schema::table('mesas', function (Blueprint $table) {
            $table->dropUnique('mesas_numero_unique');
            $table->unique(['loja_id', 'numero']);
        });
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropUnique('pedidos_numero_pedido_unique');
            $table->unique(['loja_id', 'numero_pedido']);
        });
        Schema::table('configuracoes_delivery', function (Blueprint $table) {
            $table->unique('loja_id');
        });
    }

    public function down(): void
    {
        Schema::table('configuracoes_delivery', fn (Blueprint $table) => $table->dropUnique(['loja_id']));
        foreach (array_reverse($this->tenantTables) as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropConstrainedForeignId('loja_id');
            });
        }
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('is_platform_admin'));
        Schema::dropIfExists('loja_banners');
        Schema::dropIfExists('lojas');
    }
};
