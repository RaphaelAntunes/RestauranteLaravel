<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE pedidos MODIFY COLUMN status ENUM('aberto', 'em_preparo', 'pronto', 'saiu_entrega', 'entregue', 'finalizado', 'cancelado') DEFAULT 'aberto'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE pedidos MODIFY COLUMN status ENUM('aberto', 'em_preparo', 'pronto', 'entregue', 'finalizado', 'cancelado') DEFAULT 'aberto'");
    }
};
