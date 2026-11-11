<?php

use App\Models\Doador;

// Inscrição de notificação push de cada aparelho (RNF02)

// Formato que o navegador gera: chave pública de 87 caracteres e segredo de 22 (base64url)
const CHAVE_P256DH = 'BEl62iUYgUivxIkv69yViEuiBIa-Ib9-SkvMeAtA3LFgDzkrxZJjSgSnfckjBJuBkr3qBUYIHBQFLXYp5Nksh8U';
const CHAVE_AUTH = 'tBHItJI5svbpez7KI4CCXg';

test('visitante sem login não consegue ativar o push', function () {
    $resposta = $this->post(route('push.inscrever'), [
        'endpoint' => 'https://fcm.googleapis.com/fcm/send/abc',
        'keys' => ['p256dh' => CHAVE_P256DH, 'auth' => CHAVE_AUTH],
    ]);

    $resposta->assertRedirectToRoute('login');
    $this->assertDatabaseEmpty('push_subscriptions');
});

test('ativar o push grava a inscrição deste aparelho para o doador logado', function () {
    $doador = Doador::factory()->create();

    $resposta = $this->actingAs($doador, 'doador')->postJson(route('push.inscrever'), [
        'endpoint' => 'https://fcm.googleapis.com/fcm/send/abc',
        'keys' => ['p256dh' => CHAVE_P256DH, 'auth' => CHAVE_AUTH],
    ]);

    $resposta->assertNoContent();
    $this->assertDatabaseHas('push_subscriptions', [
        'subscribable_type' => Doador::class,
        'subscribable_id' => $doador->id,
        'endpoint' => 'https://fcm.googleapis.com/fcm/send/abc',
        'public_key' => CHAVE_P256DH,
        'auth_token' => CHAVE_AUTH,
    ]);
});

test('recusa endpoint que não é de um serviço de push conhecido', function (string $endpoint) {
    $doador = Doador::factory()->create();

    $resposta = $this->actingAs($doador, 'doador')->postJson(route('push.inscrever'), [
        'endpoint' => $endpoint,
        'keys' => ['p256dh' => CHAVE_P256DH, 'auth' => CHAVE_AUTH],
    ]);

    $resposta->assertJsonValidationErrors(['endpoint' => 'Serviço de notificação não reconhecido.']);
    $this->assertDatabaseEmpty('push_subscriptions');
})->with([
    'serviço interno do servidor' => 'https://127.0.0.1:8000/push',
    'domínio que só começa igual' => 'https://fcm.googleapis.com.atacante.com/push',
    'domínio que só termina parecido' => 'https://atacantefcm.googleapis.com/push',
]);

test('recusa chaves fora do formato gerado pelo navegador', function () {
    $doador = Doador::factory()->create();

    $resposta = $this->actingAs($doador, 'doador')->postJson(route('push.inscrever'), [
        'endpoint' => 'https://fcm.googleapis.com/fcm/send/abc',
        'keys' => ['p256dh' => 'BAbc', 'auth' => 'xyz'],
    ]);

    $resposta->assertJsonValidationErrors(['keys.p256dh', 'keys.auth']);
    $this->assertDatabaseEmpty('push_subscriptions');
});

test('desativar não apaga a inscrição de outro doador', function () {
    $outro = Doador::factory()->create();
    $outro->updatePushSubscription('https://fcm.googleapis.com/fcm/send/do-outro', CHAVE_P256DH, CHAVE_AUTH);

    $resposta = $this->actingAs(Doador::factory()->create(), 'doador')
        ->deleteJson(route('push.cancelar'), ['endpoint' => 'https://fcm.googleapis.com/fcm/send/do-outro']);

    $resposta->assertNoContent();
    $this->assertDatabaseHas('push_subscriptions', ['subscribable_id' => $outro->id]);
});

test('desativar remove a inscrição do próprio aparelho', function () {
    $doador = Doador::factory()->create();
    $doador->updatePushSubscription('https://fcm.googleapis.com/fcm/send/meu', CHAVE_P256DH, CHAVE_AUTH);

    $this->actingAs($doador, 'doador')
        ->deleteJson(route('push.cancelar'), ['endpoint' => 'https://fcm.googleapis.com/fcm/send/meu']);

    $this->assertDatabaseEmpty('push_subscriptions');
});

// LGPD (RNF06): consentimento no cadastro e exclusão da conta

function cadastroDoador(array $mudancas = []): array
{
    return array_merge([
        'nome' => 'Ana Souza',
        'email' => 'ana@example.com',
        'senha' => 'segredo123',
        'cpf' => '123.456.789-09',
        'data_nascimento' => '1995-03-10',
        'sexo' => 'Feminino',
        'peso' => 62,
        'tipo_sanguineo' => 'A+',
        'consentimento' => '1',
    ], $mudancas);
}

test('cadastro com consentimento cria a conta e grava quando ele foi dado', function () {
    Pest\Laravel\travelTo('2026-11-10 10:00:00');

    $resposta = $this->post(route('doadores.store'), cadastroDoador());

    $resposta->assertRedirectToRoute('login');
    $this->assertDatabaseHas('doadores', [
        'cpf' => '12345678909',
        'consentimento_em' => '2026-11-10 10:00:00',
    ]);
});

test('cadastro sem aceitar a Política de Privacidade é recusado', function () {
    $resposta = $this->post(route('doadores.store'), cadastroDoador(['consentimento' => null]));

    $resposta->assertSessionHasErrors(['consentimento' => 'Para criar a conta, aceite a Política de Privacidade.']);
    $this->assertDatabaseEmpty('doadores');
});

test('cadastro com tipo sanguíneo fora da lista é recusado', function () {
    $resposta = $this->post(route('doadores.store'), cadastroDoador(['tipo_sanguineo' => 'XYZ']));

    $resposta->assertSessionHasErrors(['tipo_sanguineo' => 'Escolha um tipo sanguíneo da lista.']);
    $this->assertDatabaseEmpty('doadores');
});

test('excluir a conta apaga o doador e todos os dados ligados a ele', function () {
    $doador = Doador::factory()->create();
    App\Models\Doacao::factory()->for($doador)->create();
    App\Models\Agendamento::factory()->for($doador)->create();
    $doador->updatePushSubscription('https://fcm.googleapis.com/fcm/send/abc', CHAVE_P256DH, CHAVE_AUTH);
    $alerta = App\Models\Hemocentro::factory()->create()->alertas()->create([
        'tipo_sanguineo' => 'O-', 'mensagem' => 'Estoque crítico', 'data_publicacao' => now(), 'data_expiracao' => now()->addDays(5),
    ]);
    $doador->notify(new App\Notifications\AlertaUrgente($alerta));

    $resposta = $this->actingAs($doador, 'doador')->delete(route('doador.excluir'), ['senha_atual' => 'password']);

    $resposta->assertRedirectToRoute('home');
    $this->assertGuest('doador');
    $this->assertModelMissing($doador);
    foreach (['doacoes', 'agendamentos', 'conquistas', 'push_subscriptions', 'notifications'] as $tabela) {
        $this->assertDatabaseEmpty($tabela);
    }
});

test('excluir a conta com a senha errada não apaga nada', function () {
    $doador = Doador::factory()->create();

    $resposta = $this->actingAs($doador, 'doador')->delete(route('doador.excluir'), ['senha_atual' => 'errada']);

    $resposta->assertSessionHasErrors(['senha_atual' => 'Senha incorreta.']);
    $this->assertModelExists($doador);
});
