<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agendamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fk_doador_id')->constrained('doadores')->cascadeOnDelete();
            $table->foreignId('fk_hemocentro_id')->constrained('hemocentros')->cascadeOnDelete();
            $table->dateTime('data_hora');
            $table->string('status')->default('pendente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agendamentos');
    }
};