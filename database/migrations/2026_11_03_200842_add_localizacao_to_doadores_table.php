<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doadores', function (Blueprint $table) {
            // Opcional: o doador informa pelo navegador para receber alertas de hemocentros próximos (HU10)
            $table->decimal('latitude', 10, 8)->nullable()->after('tipo_sanguineo');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });
    }

    public function down(): void
    {
        Schema::table('doadores', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};
