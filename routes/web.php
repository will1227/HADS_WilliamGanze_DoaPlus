<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DoadorController;
use App\Http\Controllers\HemocentroController;
use App\Http\Controllers\AgendamentoController;
use App\Http\Controllers\DoacaoController;
use App\Http\Controllers\AlertaController;
use App\Http\Controllers\MedalhaController;

Route::get('/', function () {
    $hemocentros = \App\Models\Hemocentro::where('status', 'ativo')->get(['nome', 'endereco', 'latitude', 'longitude']);
    return view('welcome', compact('hemocentros'));
});

Route::prefix('app')->group(function () {
    Route::resource('doadores', DoadorController::class);
    Route::resource('hemocentros', HemocentroController::class);
    Route::resource('agendamentos', AgendamentoController::class);
    Route::resource('doacoes', DoacaoController::class);
    Route::resource('alertas', AlertaController::class);
    Route::resource('medalhas', MedalhaController::class);
});

require __DIR__.'/auth.php';
