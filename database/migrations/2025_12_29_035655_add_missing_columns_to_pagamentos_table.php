<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pagamentos', function (Blueprint $table) {
            // Adicionar mesa_id como chave estrangeira
            $table->foreignId('mesa_id')->nullable()->after('user_id')->constrained('mesas')->nullOnDelete();

            // Adicionar colunas de valores
            $table->decimal('subtotal', 10, 2)->default(0.00)->after('mesa_id');
            $table->decimal('total', 10, 2)->default(0.00)->after('subtotal');
            $table->decimal('valor_desconto', 10, 2)->default(0.00)->after('desconto');

            // Adicionar forma_pagamento (similar ao metodo_pagamento)
            $table->string('forma_pagamento')->nullable()->after('metodo_pagamento');

            // Adicionar status do pagamento
            $table->enum('status', ['pendente', 'aprovado', 'cancelado', 'estornado'])->default('pendente')->after('observacoes');

            // Adicionar índice
            $table->index('mesa_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagamentos', function (Blueprint $table) {
            $table->dropForeign(['mesa_id']);
            $table->dropColumn([
                'mesa_id',
                'subtotal',
                'total',
                'valor_desconto',
                'forma_pagamento',
                'status'
            ]);
        });
    }
};
