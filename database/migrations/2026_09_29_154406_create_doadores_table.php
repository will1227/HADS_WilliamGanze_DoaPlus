<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doadores', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('email')->unique();
            $table->string('senha');
            $table->string('cpf')->unique();
            $table->date('data_nascimento');
            $table->string('sexo', 20)->nullable();
            $table->decimal('peso', 5, 2); // Ex: 120.50
            $table->string('tipo_sanguineo', 3); // Ex: AB+
            $table->integer('pontos')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doadores');
    }
};