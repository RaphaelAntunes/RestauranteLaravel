<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->update([
            'face_embedding' => null,
            'facial_obrigatorio' => false,
        ]);
    }

    public function down(): void
    {
        // Dados biométricos removidos não devem ser reconstruídos.
    }
};
