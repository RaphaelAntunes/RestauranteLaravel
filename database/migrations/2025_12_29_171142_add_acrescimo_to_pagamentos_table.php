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
        Schema::table('pagamentos', function (Blueprint $table) {
            $table->decimal('acrescimo', 5, 2)->default(0)->after('valor_desconto')->comment('Porcentagem de acréscimo');
            $table->decimal('valor_acrescimo', 10, 2)->default(0)->after('acrescimo')->comment('Valor do acréscimo em reais');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagamentos', function (Blueprint $table) {
            $table->dropColumn(['acrescimo', 'valor_acrescimo']);
        });
    }
};
