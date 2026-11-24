<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lojas', function (Blueprint $table): void {
            $table->string('titulo_cardapio', 120)
                ->default('Cardápio da casa')
                ->after('descricao');
        });
    }

    public function down(): void
    {
        Schema::table('lojas', function (Blueprint $table): void {
            $table->dropColumn('titulo_cardapio');
        });
    }
};
