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
        Schema::create('pedido_item_sabores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_item_id')->constrained('pedido_itens')->cascadeOnDelete();
            $table->foreignId('sabor_id')->constrained('sabores')->restrictOnDelete();
            $table->timestamps();

            $table->index('pedido_item_id');
            $table->index('sabor_id');
        });

        // Adicionar coluna produto_tamanho_id na tabela pedido_itens
        Schema::table('pedido_itens', function (Blueprint $table) {
            $table->foreignId('produto_tamanho_id')->nullable()->after('produto_id')->constrained('produto_tamanhos')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedido_itens', function (Blueprint $table) {
            $table->dropForeign(['produto_tamanho_id']);
            $table->dropColumn('produto_tamanho_id');
        });

        Schema::dropIfExists('pedido_item_sabores');
    }
};
