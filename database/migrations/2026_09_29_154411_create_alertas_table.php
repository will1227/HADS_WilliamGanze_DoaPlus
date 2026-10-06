<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fk_hemocentro_id')->constrained('hemocentros')->cascadeOnDelete();
            $table->string('tipo_sanguineo', 3);
            $table->text('mensagem');
            $table->dateTime('data_publicacao');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertas');
    }
};