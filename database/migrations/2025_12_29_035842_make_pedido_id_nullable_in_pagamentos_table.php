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
            // Remover a constraint de chave estrangeira primeiro
            $table->dropForeign(['pedido_id']);

            // Modificar a coluna para aceitar nulos
            $table->foreignId('pedido_id')->nullable()->change()->constrained('pedidos')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagamentos', function (Blueprint $table) {
            // Remover a constraint
            $table->dropForeign(['pedido_id']);

            // Reverter para NOT NULL
            $table->foreignId('pedido_id')->nullable(false)->change()->constrained('pedidos')->restrictOnDelete();
        });
    }
};
