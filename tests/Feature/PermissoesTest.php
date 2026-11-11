<?php

use App\Models\Agendamento;
use App\Models\Doador;
use App\Models\Hemocentro;
use App\Models\User;

// Auditoria de segurança (semana 10): cada perfil só usa as rotas do seu tipo de conta.
// Quem está logado em outro perfil é tratado como visitante e mandado para o login.

// Rotas protegidas e o perfil dono de cada uma
const ROTAS_POR_PERFIL = [
    'meus dados do doador' => ['get', '/meu-perfil', 'doador'],
    'excluir conta do doador' => ['delete', '/meu-perfil', 'doador'],
    'agendar doação' => ['post', '/agendar', 'doador'],
    'ativar push' => ['post', '/push/inscricao', 'doador'],
    'dados da instituição' => ['get', '/instituicao/perfil', 'hemocentro'],
    'mudar status de agendamento' => ['patch', '/agendamentos/1/status', 'hemocentro'],
    'publicar alerta' => ['post', '/instituicao/alertas', 'hemocentro'],
    'pré-cadastrar hemocentro' => ['post', '/admin/hemocentros/pre-cadastro', 'web'],
    'aprovar instituição' => ['post', '/admin/hemocentros/aprovar/1', 'web'],
];

// Cada rota contra cada perfil que NÃO é o dono (visitante incluído)
dataset('acessos de outro perfil', function () {
    foreach (ROTAS_POR_PERFIL as $rota => [$metodo, $url, $dono]) {
        foreach (array_diff(['visitante', 'web', 'doador', 'hemocentro'], [$dono]) as $perfil) {
            yield "{$rota}, como {$perfil}" => [$metodo, $url, $perfil];
        }
    }
});

test('manda para o login quem não é do perfil dono da rota', function (string $metodo, string $url, string $perfil) {
    $usuario = match ($perfil) {
        'visitante' => null,
        'web' => User::factory()->create(),
        'doador' => Doador::factory()->create(),
        'hemocentro' => Hemocentro::factory()->create(),
    };
    if ($usuario) {
        $this->actingAs($usuario, $perfil);
    }

    $resposta = $this->{$metodo}($url);

    $resposta->assertRedirectToRoute('login');
})->with('acessos de outro perfil');

test('hemocentro não consegue mudar agendamento de outro hemocentro', function () {
    $agendamento = Agendamento::factory()->create();

    $resposta = $this->actingAs(Hemocentro::factory()->create(), 'hemocentro')
        ->patch(route('agendamentos.status', $agendamento), ['status' => 'cancelado']);

    $resposta->assertNotFound();
    expect($agendamento->fresh()->status)->toBe('confirmado');
});

test('hemocentro não consegue encerrar alerta de outro hemocentro', function () {
    $alerta = Hemocentro::factory()->create()->alertas()->create([
        'tipo_sanguineo' => 'O-', 'mensagem' => 'Estoque crítico', 'data_publicacao' => now(), 'data_expiracao' => now()->addDays(5),
    ]);

    $resposta = $this->actingAs(Hemocentro::factory()->create(), 'hemocentro')
        ->patch(route('alertas.encerrar', $alerta));

    $resposta->assertNotFound();
    expect($alerta->fresh()->data_expiracao->isFuture())->toBeTrue();
});

test('hemocentro ainda não aprovado não publica alerta', function () {
    $hemocentro = Hemocentro::factory()->create(['status' => 'pendente']);

    $resposta = $this->actingAs($hemocentro, 'hemocentro')->post(route('alertas.store'), [
        'tipo_sanguineo' => 'O-', 'mensagem' => 'Estoque crítico', 'data_expiracao' => now()->addDay()->toDateString(),
    ]);

    $resposta->assertSessionHas('erro', 'Sua instituição precisa ser aprovada antes de publicar alertas.');
    $this->assertDatabaseEmpty('alertas');
});
