<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->string('celular', 15)->unique()->index();
            $table->string('email', 100)->nullable();
            $table->string('cpf', 14)->nullable()->unique();
            $table->boolean('ativo')->default(true);
            $table->timestamp('ultimo_acesso')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
