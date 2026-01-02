<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pedido_itens', function (Blueprint $table) {
            $table->string('produto_nome')->nullable()->after('produto_id');
        });

        // Popular com os nomes dos produtos existentes
        DB::statement('
            UPDATE pedido_itens
            INNER JOIN produtos ON pedido_itens.produto_id = produtos.id
            SET pedido_itens.produto_nome = produtos.nome
            WHERE pedido_itens.produto_nome IS NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedido_itens', function (Blueprint $table) {
            $table->dropColumn('produto_nome');
        });
    }
};
