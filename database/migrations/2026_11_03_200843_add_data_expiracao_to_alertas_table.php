<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alertas', function (Blueprint $table) {
            // Até quando o alerta fica no mural; encerrar antes grava a hora do encerramento
            $table->dateTime('data_expiracao')->nullable()->after('data_publicacao');
        });

        // Alertas antigos passam a valer 7 dias a partir da publicação
        DB::table('alertas')->orderBy('id')->each(function ($alerta) {
            DB::table('alertas')->where('id', $alerta->id)->update([
                'data_expiracao' => Carbon::parse($alerta->data_publicacao)->addDays(7),
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('alertas', function (Blueprint $table) {
            $table->dropColumn('data_expiracao');
        });
    }
};
