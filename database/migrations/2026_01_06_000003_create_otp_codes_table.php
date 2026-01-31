<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id();
            $table->string('celular', 15)->index();
            $table->string('codigo', 6);
            $table->integer('tentativas')->default(0);
            $table->timestamp('expirado_em');
            $table->timestamp('usado_em')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index('codigo');
            $table->index('expirado_em');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_codes');
    }
};
