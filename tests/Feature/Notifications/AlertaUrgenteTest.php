<?php

use App\Models\Doador;
use App\Models\Hemocentro;
use App\Notifications\AlertaUrgente;

// Conteúdo da notificação que aparece no celular (RNF02)

test('a notificação push diz quem precisa de qual sangue e abre os alertas do painel', function () {
    $hemocentro = Hemocentro::factory()->create(['nome' => 'Hemopasso']);
    $alerta = $hemocentro->alertas()->create(['tipo_sanguineo' => 'O-', 'mensagem' => 'Estoque crítico', 'data_publicacao' => now(), 'data_expiracao' => now()->addDays(5)]);
    $aviso = new AlertaUrgente($alerta);

    $mensagem = $aviso->toWebPush(Doador::factory()->make(), $aviso)->toArray();

    expect($mensagem)->toMatchArray([
        'title' => 'Hemopasso precisa de sangue O-',
        'body' => 'Estoque crítico',
        'tag' => 'alerta-'.$alerta->id,
        'data' => ['url' => 'http://localhost/dashboard#alertas'],
    ]);
});
