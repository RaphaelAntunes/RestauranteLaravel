<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carrinho_item_sabores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carrinho_item_id')->constrained('carrinho_itens')->onDelete('cascade');
            $table->foreignId('sabor_id')->constrained('sabores')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carrinho_item_sabores');
    }
};
