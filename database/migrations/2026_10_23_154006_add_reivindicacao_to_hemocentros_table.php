<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hemocentros', function (Blueprint $table) {
            // Adiciona a coluna que aponta para o ID do hemocentro provisório
            $table->foreignId('fk_reivindicacao_id')
                  ->nullable()
                  ->after('status')
                  ->constrained('hemocentros')
                  ->nullOnDelete(); // Se o admin apagar o provisório, o pedido é cancelado
        });
    }

    public function down(): void
    {
        Schema::table('hemocentros', function (Blueprint $table) {
            $table->dropForeign(['fk_reivindicacao_id']);
            $table->dropColumn('fk_reivindicacao_id');
        });
    }
};