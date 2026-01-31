<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mesas', function (Blueprint $table) {
            $table->enum('tipo', ['normal', 'delivery', 'retirada'])->default('normal')->after('numero');
            $table->foreignId('pedido_online_id')->nullable()->after('tipo')->comment('ID do pedido online vinculado');
            $table->index('tipo');
        });
    }

    public function down(): void
    {
        Schema::table('mesas', function (Blueprint $table) {
            $table->dropIndex(['tipo']);
            $table->dropColumn(['tipo', 'pedido_online_id']);
        });
    }
};
