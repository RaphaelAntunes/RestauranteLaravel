<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            // Tornar mesa_id nullable
            $table->foreignId('mesa_id')->nullable()->change();

            // Novos campos
            $table->foreignId('cliente_id')->nullable()->after('mesa_id')->constrained('clientes')->onDelete('set null');
            $table->enum('tipo_pedido', ['mesa', 'delivery', 'retirada'])->default('mesa')->after('cliente_id');
            $table->foreignId('cliente_endereco_id')->nullable()->after('tipo_pedido')->constrained('cliente_enderecos')->onDelete('set null');
            $table->decimal('taxa_entrega', 10, 2)->default(0.00)->after('total');
            $table->text('observacoes_entrega')->nullable()->after('observacoes');
            $table->timestamp('previsao_entrega')->nullable()->after('data_finalizacao');

            $table->index('cliente_id');
            $table->index('tipo_pedido');
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropForeign(['cliente_endereco_id']);
            $table->dropForeign(['cliente_id']);
            $table->dropIndex(['tipo_pedido']);
            $table->dropIndex(['cliente_id']);

            $table->dropColumn([
                'cliente_id',
                'tipo_pedido',
                'cliente_endereco_id',
                'taxa_entrega',
                'observacoes_entrega',
                'previsao_entrega'
            ]);

            // Reverter mesa_id para NOT NULL
            $table->foreignId('mesa_id')->nullable(false)->change();
        });
    }
};
