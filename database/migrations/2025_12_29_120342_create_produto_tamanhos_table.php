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
        Schema::create('produto_tamanhos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produto_id')->constrained('produtos')->cascadeOnDelete();
            $table->string('nome'); // P, M, G, GG
            $table->string('descricao')->nullable(); // Pequena, Média, Grande, Gigante
            $table->decimal('preco', 10, 2); // Preço específico para esse tamanho
            $table->integer('max_sabores')->default(1); // Máximo de sabores permitidos
            $table->boolean('ativo')->default(true);
            $table->integer('ordem')->default(0);
            $table->timestamps();

            $table->index('produto_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produto_tamanhos');
    }
};
