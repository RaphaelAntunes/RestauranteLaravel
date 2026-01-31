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
            $table->boolean('taxa_servico_aplicada')->default(false)->after('valor_acrescimo');
            $table->decimal('taxa_servico', 5, 2)->default(0)->after('taxa_servico_aplicada');
            $table->decimal('valor_taxa_servico', 10, 2)->default(0)->after('taxa_servico');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagamentos', function (Blueprint $table) {
            $table->dropColumn(['taxa_servico_aplicada', 'taxa_servico', 'valor_taxa_servico']);
        });
    }
};
