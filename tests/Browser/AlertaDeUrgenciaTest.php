<?php

use App\Models\Doador;
use App\Models\Hemocentro;

// E2E do mural de urgências: hemocentro publica, o mural público e o doador próximo mostram,
// e os dois têm o botão de compartilhar no WhatsApp (HU09, HU10, HU13)

test('alerta publicado aparece no mural e no painel do doador próximo, com botão de compartilhar', function () {
    $hemocentro = Hemocentro::factory()->create(['nome' => 'Hemopasso']);
    Doador::factory()->tipo('O-')->em(-28.25, -52.41)->create(['email' => 'carla@example.com']);

    $pagina = entrar(visit('/'), $hemocentro->email)
        ->click('O-') // a pílula do tipo (o input de rádio fica escondido)
        ->fill('mensagem', 'Estoque de O- crítico, precisamos de doadores esta semana.')
        ->fill('data_expiracao', today()->addDays(3)->toDateString())
        ->press('Publicar alerta')
        ->assertSee('Alerta de O- publicado no mural. 1 doador próximo foi avisado.')
        ->press('Sair');

    // Mural público (sem login)
    $pagina->navigate('/')
        ->assertSee('Precisam de sangue agora')
        ->assertSee('Estoque de O- crítico, precisamos de doadores esta semana.')
        ->assertAttributeContains('.urg-zap', 'href', 'https://wa.me/?text=O%20Hemopasso%20precisa%20de%20sangue%20O-');

    // Painel do doador que estava a menos de 50 km
    entrar($pagina, 'carla@example.com')
        ->assertSee('Um hemocentro perto de você precisa do seu sangue')
        ->assertSee('Agendar neste hemocentro')
        ->assertAttributeContains('#alertas a[href^="https://wa.me/"]', 'href', 'precisa%20de%20sangue%20O-');
});
