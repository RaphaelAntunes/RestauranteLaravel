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
        Schema::create('mesas', function (Blueprint $table) {
            $table->id();
            $table->integer('numero')->unique();
            $table->integer('capacidade')->default(4);
            $table->enum('status', ['disponivel', 'ocupada', 'reservada', 'manutencao'])->default('disponivel');
            $table->string('localizacao', 100)->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index('status');
            $table->index('numero');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mesas');
    }
};
