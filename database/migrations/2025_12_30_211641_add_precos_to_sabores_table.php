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
        Schema::table('sabores', function (Blueprint $table) {
            $table->decimal('preco_p', 10, 2)->nullable()->after('ingredientes');
            $table->decimal('preco_m', 10, 2)->nullable()->after('preco_p');
            $table->decimal('preco_g', 10, 2)->nullable()->after('preco_m');
            $table->decimal('preco_gg', 10, 2)->nullable()->after('preco_g');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sabores', function (Blueprint $table) {
            $table->dropColumn(['preco_p', 'preco_m', 'preco_g', 'preco_gg']);
        });
    }
};
