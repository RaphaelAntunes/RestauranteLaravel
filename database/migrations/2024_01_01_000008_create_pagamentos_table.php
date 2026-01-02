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
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete()->comment('Operador do caixa');
            $table->decimal('valor_total', 10, 2);
            $table->enum('metodo_pagamento', ['dinheiro', 'pix', 'credito', 'debito', 'multiplo']);
            $table->decimal('valor_pago', 10, 2);
            $table->decimal('troco', 10, 2)->default(0.00);
            $table->decimal('desconto', 10, 2)->default(0.00);
            $table->text('observacoes')->nullable();
            $table->timestamp('data_pagamento')->useCurrent();
            $table->timestamp('created_at')->useCurrent();

            $table->index('pedido_id');
            $table->index('metodo_pagamento');
            $table->index('data_pagamento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagamentos');
    }
};
