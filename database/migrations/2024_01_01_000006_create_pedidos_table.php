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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mesa_id')->constrained('mesas')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete()->comment('Garçom que criou o pedido');
            $table->string('numero_pedido', 20)->unique();
            $table->enum('status', ['aberto', 'em_preparo', 'pronto', 'entregue', 'finalizado', 'cancelado'])->default('aberto');
            $table->decimal('total', 10, 2)->default(0.00);
            $table->text('observacoes')->nullable();
            $table->timestamp('data_abertura')->useCurrent();
            $table->timestamp('data_finalizacao')->nullable();
            $table->timestamps();

            $table->index('mesa_id');
            $table->index('status');
            $table->index('numero_pedido');
            $table->index('data_abertura');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
