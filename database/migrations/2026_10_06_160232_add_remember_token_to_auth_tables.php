<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Adiciona a coluna na tabela de doadores
        Schema::table('doadores', function (Blueprint $table) {
            $table->rememberToken();
        });

        // Adiciona a coluna na tabela de hemocentros
        Schema::table('hemocentros', function (Blueprint $table) {
            $table->rememberToken();
        });
    }

    public function down(): void
    {
        Schema::table('doadores', function (Blueprint $table) {
            $table->dropColumn('remember_token');
        });

        Schema::table('hemocentros', function (Blueprint $table) {
            $table->dropColumn('remember_token');
        });
    }
};
