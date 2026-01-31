<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cliente_enderecos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->string('nome_endereco', 50);
            $table->string('cep', 9);
            $table->string('logradouro', 255);
            $table->string('numero', 10);
            $table->string('complemento', 100)->nullable();
            $table->string('bairro', 100);
            $table->string('cidade', 100);
            $table->string('estado', 2);
            $table->text('referencia')->nullable();
            $table->boolean('padrao')->default(false);
            $table->timestamps();

            $table->index('cliente_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cliente_enderecos');
    }
};
