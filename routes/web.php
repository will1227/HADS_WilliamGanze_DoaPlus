<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DoadorController;
use App\Http\Controllers\HemocentroController;
use App\Http\Controllers\AgendamentoController;
use App\Http\Controllers\AlertaController;

// 1. Rota da Tela Inicial (Mapa e FAQ)
Route::get('/', function () {
    // Busca os hemocentros ativos e provisórios que têm latitude e longitude preenchidas
    // (contas ainda aguardando aprovação não aparecem).
    // Só os campos públicos vão para o navegador (nada de e-mail ou CNPJ).
    $hemocentros = \App\Models\Hemocentro::whereIn('status', ['ativo', 'provisorio'])
                                         ->whereNotNull('latitude')
                                         ->whereNotNull('longitude')
                                         ->orderBy('nome')
                                         ->get()
                                         ->map(fn ($h) => [
                                             'id' => $h->id,
                                             'nome' => $h->nome,
                                             'endereco' => $h->endereco,
                                             'telefone' => $h->telefone,
                                             'latitude' => (float) $h->latitude,
                                             'longitude' => (float) $h->longitude,
                                             'agendavel' => $h->status === 'ativo',
                                         ]);

    // Mural de urgências (HU09): alertas no prazo de hemocentros ativos que estão no mapa
    $urgencias = \App\Models\Alerta::ativos()
        ->whereHas('hemocentro', fn ($q) => $q->where('status', 'ativo')->whereNotNull('latitude')->whereNotNull('longitude'))
        ->with('hemocentro')
        ->latest('data_publicacao')
        ->get();

    // Doador logado já vê o mural filtrado pelo próprio tipo sanguíneo
    $tipoDoDoador = Auth::guard('doador')->user()?->tipo_sanguineo;

    return view('welcome', compact('hemocentros', 'urgencias', 'tipoDoDoador'));
})->name('home');

// 2. Dashboard central para diferentes tipos de usuários (admin, doador, hemocentro)
Route::get('/dashboard', function () {
    if (Auth::guard('web')->check()) {
        // Busca todas as instituições aguardando aprovação (com ou sem pedido de um local provisório)
        $pedidos = \App\Models\Hemocentro::with('localReivindicado')
            ->where('status', 'pendente')
            ->get();
        return view('painel.admin', compact('pedidos'));
    }
    
    if (Auth::guard('doador')->check()) {
        $doador = Auth::guard('doador')->user();
        return view('painel.doador', compact('doador'));
    }
    
    if (Auth::guard('hemocentro')->check()) {
        $hemocentro = Auth::guard('hemocentro')->user();
        $locaisProvisorios = \App\Models\Hemocentro::where('status', 'provisorio')->orderBy('nome')->get();
        
        // Se a conta já for ativa, busca os agendamentos e os alertas no mural
        $agendamentos = collect();
        $alertas = collect();
        if ($hemocentro->status === 'ativo') {
            $agendamentos = \App\Models\Agendamento::with('doador')
                ->where('fk_hemocentro_id', $hemocentro->id)
                ->whereIn('status', ['pendente', 'confirmado'])
                ->orderBy('data_hora', 'asc')
                ->get();
            $alertas = $hemocentro->alertas()->ativos()->latest('data_publicacao')->get();
        }

        return view('painel.hemocentro', compact('hemocentro', 'locaisProvisorios', 'agendamentos', 'alertas'));
    }
    
    return redirect('/');
})->middleware('auth:web,doador,hemocentro')->name('dashboard');

// 3. Rotas do Perfil do Doador
Route::middleware('auth:doador')->group(function () {
    Route::get('/meu-perfil', [DoadorController::class, 'editProfile'])->name('doador.perfil');
    Route::put('/meu-perfil', [DoadorController::class, 'updateProfile'])->name('doador.perfil.update');

    // Rotas de Agendamento do Doador
    Route::get('/agendar', [AgendamentoController::class, 'create'])->name('agendamentos.create');
    Route::post('/agendar', [AgendamentoController::class, 'store'])->name('agendamentos.store');

    // Alertas de urgência recebidos (HU10)
    Route::post('/alertas/lidos', [DoadorController::class, 'marcarAlertasLidos'])->name('alertas.lidos');
});

// 4. Rotas do Perfil e Reivindicação do Hemocentro
Route::middleware('auth:hemocentro')->group(function () {
    Route::get('/instituicao/perfil', [HemocentroController::class, 'editProfile'])->name('hemocentro.perfil');
    Route::put('/instituicao/perfil', [HemocentroController::class, 'updateProfile'])->name('hemocentro.perfil.update');
    
    // Rota para a instituição solicitar a posse de um ponto no mapa
    Route::post('/instituicao/reivindicar', [HemocentroController::class, 'reivindicar'])->name('hemocentro.reivindicar');

    // Rotas de Gestão do Hemocentro
    Route::patch('/agendamentos/{id}/status', [AgendamentoController::class, 'atualizarStatus'])->name('agendamentos.status');

    // Mural de urgências (HU09)
    Route::post('/instituicao/alertas', [AlertaController::class, 'store'])->name('alertas.store');
    Route::patch('/instituicao/alertas/{id}/encerrar', [AlertaController::class, 'encerrar'])->name('alertas.encerrar');
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
            // Aparece no mapa como "Em breve", mas só recebe agendamentos depois que uma instituição o assumir
            'status' => 'provisorio',
        ]);

        return back()->with('status', 'Hemocentro pré-cadastrado com sucesso! Ele já está visível no mapa inicial.');
    })->name('admin.precadastro');

    // Rota para Aprovar a Instituição (com fusão, se ela reivindicou um ponto provisório)
    Route::post('/admin/hemocentros/aprovar/{id}', function ($id) {
        $hemocentroReal = \App\Models\Hemocentro::where('status', 'pendente')->findOrFail($id);
        $provisorio = $hemocentroReal->localReivindicado;

        \Illuminate\Support\Facades\DB::transaction(function () use ($hemocentroReal, $provisorio) {
            if ($provisorio) {
                // 1. O perfil real herda as coordenadas do provisório
                $hemocentroReal->latitude = $provisorio->latitude;
                $hemocentroReal->longitude = $provisorio->longitude;
                $hemocentroReal->fk_reivindicacao_id = null;

                // 2. Transfere o histórico do provisório (o delete em cascata apagaria esses registros)
                $provisorio->agendamentos()->update(['fk_hemocentro_id' => $hemocentroReal->id]);
                $provisorio->doacoes()->update(['fk_hemocentro_id' => $hemocentroReal->id]);
                $provisorio->alertas()->update(['fk_hemocentro_id' => $hemocentroReal->id]);
            }

            // 3. Ativa a conta da instituição
            $hemocentroReal->status = 'ativo';
            $hemocentroReal->save();

            // 4. Deleta o pino provisório
            $provisorio?->delete();
        });

        $mensagem = $provisorio
            ? 'Instituição aprovada e unificada ao mapa com sucesso!'
            : 'Instituição aprovada com sucesso!';

        return back()->with('status', $mensagem);
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

// 6. Cadastro público de doadores e instituições (só para quem ainda não está logado)
Route::middleware('guest:web,doador,hemocentro')->group(function () {
    Route::get('/register/doador', function () {
        return view('auth.register-doador');
    });

    Route::get('/register/hemocentro', function () {
        return view('auth.register-hemocentro');
    });

    Route::post('/app/doadores', [DoadorController::class, 'store'])->name('doadores.store');
    Route::post('/app/hemocentros', [HemocentroController::class, 'store'])->name('hemocentros.store');
});

// 8. Arquivo de Autenticação base do Laravel Breeze
require __DIR__.'/auth.php';