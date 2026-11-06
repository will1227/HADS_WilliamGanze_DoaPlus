<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Pré-cadastros antigos foram gravados como 'ativo'; agora eles têm status próprio
        DB::table('hemocentros')
            ->where('email', 'like', 'precadastro_%')
            ->update(['status' => 'provisorio']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('hemocentros')
            ->where('status', 'provisorio')
            ->update(['status' => 'ativo']);
    }
};
