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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('pode_lancar_pedidos')->default(true)->after('ativo');
            $table->boolean('pode_fechar_mesas')->default(true)->after('pode_lancar_pedidos');
            $table->boolean('pode_cancelar_itens')->default(true)->after('pode_fechar_mesas');
            $table->boolean('pode_cancelar_pedidos')->default(true)->after('pode_cancelar_itens');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['pode_lancar_pedidos', 'pode_fechar_mesas', 'pode_cancelar_itens', 'pode_cancelar_pedidos']);
        });
    }
};
