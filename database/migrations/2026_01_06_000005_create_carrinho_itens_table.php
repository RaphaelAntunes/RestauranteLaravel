<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carrinho_itens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->onDelete('cascade');
            $table->string('session_id', 100)->index();
            $table->foreignId('produto_id')->constrained('produtos')->onDelete('cascade');
            $table->foreignId('produto_tamanho_id')->nullable()->constrained('produto_tamanhos')->onDelete('set null');
            $table->integer('quantidade')->default(1);
            $table->decimal('preco_unitario', 10, 2);
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carrinho_itens');
    }
};
