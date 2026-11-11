<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // LGPD (RNF06): data e hora em que o doador aceitou a Política de Privacidade.
    // Fica vazio para contas criadas antes desta versão.
    public function up(): void
    {
        Schema::table('doadores', function (Blueprint $table) {
            $table->timestamp('consentimento_em')->nullable()->after('senha');
        });
    }

    public function down(): void
    {
        Schema::table('doadores', function (Blueprint $table) {
            $table->dropColumn('consentimento_em');
        });
    }
};
