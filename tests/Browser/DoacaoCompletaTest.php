<?php

use App\Models\Doador;
use App\Models\Hemocentro;

// E2E da jornada principal: o doador agenda, o hemocentro confirma e registra a doação,
// e o doador vê os pontos, a medalha e o botão de compartilhar (HU04, HU05, HU06, HU11, HU12, HU13)

test('doador agenda, hemocentro registra a doação e o doador ganha pontos e medalha', function () {
    $hemocentro = Hemocentro::factory()->create(['nome' => 'Hemopasso']);
    $doador = Doador::factory()->create(['nome' => 'Ana Souza', 'email' => 'ana@example.com']);

    // 1. Doador agenda para hoje (23:59: tem que ser um horário futuro e a coleta só é registrada no dia)
    $pagina = entrar(visit('/'), 'ana@example.com')
        ->assertSee('Você já pode doar.')
        ->navigate('/agendar')
        ->radio('fk_hemocentro_id', (string) $hemocentro->id)
        ->fill('data', today()->toDateString())
        ->fill('hora', '23:59')
        ->press('main button[type=submit]')
        ->assertSee('Agendamento marcado com sucesso!')
        ->assertSee('no Hemopasso')
        ->assertSee('Aguardando confirmação')
        ->press('Sair');

    // 2. Hemocentro confirma o horário e registra a doação
    entrar($pagina, $hemocentro->email)
        ->assertSee('Ana Souza')
        ->press('Confirmar horário')
        ->assertSee('Agendamento confirmado!')
        ->press('Registrar doação')
        ->assertSee('Doação registrada com sucesso!')
        ->press('Sair');

    // 3. Doador vê os pontos, a medalha colorida e o botão do WhatsApp
    entrar($pagina, 'ana@example.com')
        ->assertSee('Primeira Gota')
        ->assertSee('1 de 6')
        ->assertSeeIn('dl:first-of-type', '100')
        ->assertSee('Conquistada em '.today()->format('d/m/Y'))
        ->assertAttributeContains('a[href^="https://wa.me/"]', 'href', 'Conquistei%20a%20medalha%20%E2%80%9CPrimeira%20Gota%E2%80%9D')
        ->assertNoJavaScriptErrors();

    expect($doador->fresh()->pontos)->toBe(100);
});
