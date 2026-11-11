<?php

use App\Models\Doador;
use App\Models\Hemocentro;
use App\Notifications\AlertaUrgente;
use Illuminate\Support\Facades\Notification;
use NotificationChannels\WebPush\WebPushChannel;

use function Pest\Laravel\travelTo;

// O alerta de urgência sai também por push para quem ativou (RNF02)

function publicarAlertaO(Hemocentro $hemocentro)
{
    return test()->actingAs($hemocentro, 'hemocentro')->post(route('alertas.store'), [
        'tipo_sanguineo' => 'O-',
        'mensagem' => 'Estoque crítico',
        'data_expiracao' => '2026-11-12',
    ]);
}

test('doador próximo que ativou o push recebe o alerta no painel e por push', function () {
    travelTo('2026-11-10 10:00:00');
    $hemocentro = Hemocentro::factory()->create();
    $doador = Doador::factory()->tipo('O-')->em(-28.25, -52.41)->create();
    $doador->updatePushSubscription('https://fcm.googleapis.com/fcm/send/abc', 'chave', 'segredo');
    Notification::fake();

    publicarAlertaO($hemocentro);

    Notification::assertSentTo($doador, AlertaUrgente::class,
        fn (AlertaUrgente $aviso, array $canais) => $canais === ['database', WebPushChannel::class]);
});

test('doador próximo sem push recebe o alerta só no painel', function () {
    travelTo('2026-11-10 10:00:00');
    $hemocentro = Hemocentro::factory()->create();
    $doador = Doador::factory()->tipo('O-')->em(-28.25, -52.41)->create();
    Notification::fake();

    publicarAlertaO($hemocentro);

    Notification::assertSentTo($doador, AlertaUrgente::class,
        fn (AlertaUrgente $aviso, array $canais) => $canais === ['database']);
});
