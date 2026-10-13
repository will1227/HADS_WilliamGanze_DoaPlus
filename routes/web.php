<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DoadorController;
use App\Http\Controllers\HemocentroController;
use App\Http\Controllers\AgendamentoController;
use App\Http\Controllers\DoacaoController;
use App\Http\Controllers\AlertaController;
use App\Http\Controllers\MedalhaController;

// 1. Rota da Tela Inicial (Mapa)
Route::get('/', function () {
    $hemocentros = \App\Models\Hemocentro::where('status', 'ativo')->get(['nome', 'endereco', 'latitude', 'longitude']);
    return view('welcome', compact('hemocentros'));
});
// dashboard para diferentes tipos de usuários (admin, doador, hemocentro)
Route::get('/dashboard', function () {
    if (Auth::guard('web')->check()) {
        return view('painel.admin');
    }
    if (Auth::guard('doador')->check()) {
        // Envia os dados do doador logado para a view
        $doador = Auth::guard('doador')->user();
        return view('painel.doador', compact('doador'));
    }
    if (Auth::guard('hemocentro')->check()) {
        return view('painel.hemocentro');
    }
    return redirect('/');
})->middleware('auth:web,doador,hemocentro')->name('dashboard');

Route::post('/admin/hemocentros/pre-cadastro', function (Illuminate\Http\Request $request) {
    $request->validate([
        'nome' => 'required|string|max:255',
        'endereco' => 'required|string|max:255',
        'telefone' => 'required|string|max:20',
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
    ]);

    \App\Models\Hemocentro::create([
        'nome' => $request->nome,
        'endereco' => $request->endereco,
        'telefone' => preg_replace('/[^0-9]/', '', $request->telefone),
        'latitude' => $request->latitude,
        'longitude' => $request->longitude,
        
        // Dados gerados automaticamente
        'email' => 'precadastro_' . time() . '@doaplus.com.br',
        'senha' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(16)),
        'cnpj' => mt_rand(10000000, 99999999) . '0001' . mt_rand(10, 99),
        'status' => 'ativo', 
    ]);

    return back()->with('status', 'Hemocentro pré-cadastrado com sucesso! Ele já está visível no mapa inicial.');
})->middleware('auth:web')->name('admin.precadastro');

Route::prefix('app')->group(function () {
    Route::resource('doadores', DoadorController::class);
    Route::resource('hemocentros', HemocentroController::class);
    Route::resource('agendamentos', AgendamentoController::class);
    Route::resource('doacoes', DoacaoController::class);
    Route::resource('alertas', AlertaController::class);
    Route::resource('medalhas', MedalhaController::class);
});

Route::get('/register/doador', function () {
    return view('auth.register-doador');
});

Route::get('/register/hemocentro', function () {
    return view('auth.register-hemocentro');
});

require __DIR__.'/auth.php';