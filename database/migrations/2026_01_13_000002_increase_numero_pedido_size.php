<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            // Aumentar tamanho de numero_pedido para suportar pedidos online
            // PED-ONLINE-YYYYMMDD-NNNN = 24 caracteres
            $table->string('numero_pedido', 30)->change();
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->string('numero_pedido', 20)->change();
        });
    }
};
