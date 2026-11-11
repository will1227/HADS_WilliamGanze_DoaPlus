<?php

use App\Models\Hemocentro;
use App\Models\User;

// E2E da aprovação: só depois que o administrador aprova a instituição recebe agendamentos (HU02)

test('administrador aprova a instituição e ela passa a ver a própria agenda', function () {
    $admin = User::factory()->create();
    $hemocentro = Hemocentro::factory()->create(['nome' => 'Hemocentro Serrano', 'status' => 'pendente']);

    $pagina = entrar(visit('/'), $admin->email)
        ->assertSee('Hemocentro Serrano')
        ->press('Aprovar')
        ->assertSee('Instituição aprovada com sucesso!')
        ->press('Sair');

    entrar($pagina, $hemocentro->email)
        ->assertSee('Publicar alerta de urgência');

    expect($hemocentro->fresh()->status)->toBe('ativo');
});
