<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
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

// 2. Dashboard central para diferentes tipos de usuários (admin, doador, hemocentro)
Route::get('/dashboard', function () {
    if (Auth::guard('web')->check()) {
        // Busca hemocentros pendentes que solicitaram um local
        $pedidos = \App\Models\Hemocentro::with('localReivindicado')
            ->where('status', 'pendente')
            ->whereNotNull('fk_reivindicacao_id')
            ->get();
        return view('painel.admin', compact('pedidos'));
    }
    
    if (Auth::guard('doador')->check()) {
        $doador = Auth::guard('doador')->user();
        return view('painel.doador', compact('doador'));
    }
    
    if (Auth::guard('hemocentro')->check()) {
        $hemocentro = Auth::guard('hemocentro')->user();
        // Busca todos os hemocentros provisórios criados pelo Admin (que não têm "dono" ainda)
        $locaisProvisorios = \App\Models\Hemocentro::where('email', 'like', 'precadastro_%')->get();
        return view('painel.hemocentro', compact('hemocentro', 'locaisProvisorios'));
    }
    
    return redirect('/');
})->middleware('auth:web,doador,hemocentro')->name('dashboard');

// 3. Rotas do Perfil do Doador
Route::middleware('auth:doador')->group(function () {
    Route::get('/meu-perfil', [DoadorController::class, 'editProfile'])->name('doador.perfil');
    Route::put('/meu-perfil', [DoadorController::class, 'updateProfile'])->name('doador.perfil.update');
});

// 4. Rotas do Perfil e Reivindicação do Hemocentro
Route::middleware('auth:hemocentro')->group(function () {
    Route::get('/instituicao/perfil', [HemocentroController::class, 'editProfile'])->name('hemocentro.perfil');
    Route::put('/instituicao/perfil', [HemocentroController::class, 'updateProfile'])->name('hemocentro.perfil.update');
    
    // Rota para a instituição solicitar a posse de um ponto no mapa
    Route::post('/instituicao/reivindicar', [HemocentroController::class, 'reivindicar'])->name('hemocentro.reivindicar');
});

// 5. Rotas do Administrador (Pré-cadastro, Aprovação e Rejeição)
Route::middleware('auth:web')->group(function () {
    
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
            
            // Dados gerados automaticamente para segurar o pino no mapa
            'email' => 'precadastro_' . time() . '@doaplus.com.br',
            'senha' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(16)),
            'cnpj' => mt_rand(10000000, 99999999) . '0001' . mt_rand(10, 99),
            'status' => 'ativo', 
        ]);

        return back()->with('status', 'Hemocentro pré-cadastrado com sucesso! Ele já está visível no mapa inicial.');
    })->name('admin.precadastro');

    // Rota para Aprovar a Reivindicação (Fusão)
    Route::post('/admin/hemocentros/aprovar/{id}', function ($id) {
        $hemocentroReal = \App\Models\Hemocentro::findOrFail($id);
        $provisorio = $hemocentroReal->localReivindicado;

        if ($provisorio) {
            // 1. O perfil real herda as coordenadas do provisório
            $hemocentroReal->latitude = $provisorio->latitude;
            $hemocentroReal->longitude = $provisorio->longitude;
            
            // 2. Ativa a conta da instituição
            $hemocentroReal->status = 'ativo';
            $hemocentroReal->save();

            // 3. Deleta o pino provisório
            $provisorio->delete();
        }

        return back()->with('status', 'Instituição aprovada e unificada ao mapa com sucesso!');
    })->name('admin.aprovar');

    // Rota para Rejeitar a Reivindicação
    Route::post('/admin/hemocentros/rejeitar/{id}', function ($id) {
        $hemocentroReal = \App\Models\Hemocentro::findOrFail($id);
        
        // Apenas remove o vínculo, permitindo que a instituição tente outro local
        $hemocentroReal->fk_reivindicacao_id = null;
        $hemocentroReal->save();

        return back()->with('erro', 'Pedido de reivindicação rejeitado.');
    })->name('admin.rejeitar');
    
});

// 6. Rotas de Recursos (CRUD Padrão do Sistema)
Route::prefix('app')->group(function () {
    Route::resource('doadores', DoadorController::class);
    Route::resource('hemocentros', HemocentroController::class);
    Route::resource('agendamentos', AgendamentoController::class);
    Route::resource('doacoes', DoacaoController::class);
    Route::resource('alertas', AlertaController::class);
    Route::resource('medalhas', MedalhaController::class);
});

// 7. Rotas de Registro Personalizadas (Frontend)
Route::get('/register/doador', function () {
    return view('auth.register-doador');
});

Route::get('/register/hemocentro', function () {
    return view('auth.register-hemocentro');
});

// 8. Arquivo de Autenticação base do Laravel Breeze
require __DIR__.'/auth.php';