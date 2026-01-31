<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracoes_delivery', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo_taxa', ['fixa', 'gratis_acima'])->default('fixa');
            $table->decimal('valor_taxa_fixa', 10, 2)->default(5.00);
            $table->decimal('valor_minimo_gratis', 10, 2)->nullable();
            $table->integer('tempo_medio_preparo')->default(40);
            $table->decimal('pedido_minimo', 10, 2)->default(0.00);
            $table->boolean('ativo')->default(true);
            $table->time('horario_inicio')->nullable();
            $table->time('horario_fim')->nullable();
            $table->json('dias_funcionamento')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracoes_delivery');
    }
};
