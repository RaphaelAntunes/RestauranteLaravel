<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lojas', function (Blueprint $table): void {
            $table->string('banner_mobile_path')->nullable()->after('banner_path');
            $table->boolean('exibir_textos_banner')->default(true)->after('banner_mobile_path');
        });
    }

    public function down(): void
    {
        Schema::table('lojas', function (Blueprint $table): void {
            $table->dropColumn(['banner_mobile_path', 'exibir_textos_banner']);
        });
    }
};
