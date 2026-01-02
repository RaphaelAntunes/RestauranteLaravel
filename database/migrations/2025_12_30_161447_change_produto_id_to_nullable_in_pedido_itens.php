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
        Schema::table('pedido_itens', function (Blueprint $table) {
            // Remover a foreign key existente
            $table->dropForeign(['produto_id']);

            // Tornar a coluna nullable
            $table->foreignId('produto_id')->nullable()->change();

            // Recriar a foreign key com nullOnDelete
            $table->foreign('produto_id')
                ->references('id')
                ->on('produtos')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedido_itens', function (Blueprint $table) {
            // Remover a foreign key
            $table->dropForeign(['produto_id']);

            // Tornar a coluna não-nullable novamente
            $table->foreignId('produto_id')->nullable(false)->change();

            // Recriar a foreign key com restrictOnDelete
            $table->foreign('produto_id')
                ->references('id')
                ->on('produtos')
                ->restrictOnDelete();
        });
    }
};
