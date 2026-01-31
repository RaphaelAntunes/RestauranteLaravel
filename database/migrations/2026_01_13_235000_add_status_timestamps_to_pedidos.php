<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->timestamp('em_preparo_at')->nullable()->after('data_abertura');
            $table->timestamp('pronto_at')->nullable()->after('em_preparo_at');
            $table->timestamp('saiu_entrega_at')->nullable()->after('pronto_at');
            $table->timestamp('entregue_at')->nullable()->after('saiu_entrega_at');
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn(['em_preparo_at', 'pronto_at', 'saiu_entrega_at', 'entregue_at']);
        });
    }
};
