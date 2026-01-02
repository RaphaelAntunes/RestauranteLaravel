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
        // Adicionar sessao_atual às mesas
        Schema::table('mesas', function (Blueprint $table) {
            $table->string('sessao_atual')->nullable()->after('cliente_nome');
        });

        // Adicionar sessao_id aos pedidos
        Schema::table('pedidos', function (Blueprint $table) {
            $table->string('sessao_id')->nullable()->after('mesa_id');
            $table->index('sessao_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mesas', function (Blueprint $table) {
            $table->dropColumn('sessao_atual');
        });

        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropIndex(['sessao_id']);
            $table->dropColumn('sessao_id');
        });
    }
};
