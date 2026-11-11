<?php

use App\Models\Hemocentro;
use App\Models\User;

// Etapa entre o cadastro e a aprovação (HU02): a instituição só entra na fila do admin
// depois de assumir um ponto pré-cadastrado ou informar as coordenadas da unidade

function preCadastro(): Hemocentro
{
    return Hemocentro::factory()->create(['nome' => 'Hemopasso', 'status' => 'provisorio']);
}

test('instituição recém-cadastrada fica incompleta e não aparece na fila do admin', function () {
    $this->post(route('hemocentros.store'), [
        'nome' => 'Hemocentro Novo',
        'email' => 'novo@example.com',
        'senha' => 'segredo123',
        'cnpj' => '11.222.333/0001-81',
        'telefone' => '(54) 99999-0000',
        'endereco' => 'Rua Um, 10',
    ])->assertRedirect(route('login'));

    expect(Hemocentro::firstWhere('email', 'novo@example.com')->status)->toBe('incompleto');

    $this->actingAs(User::factory()->create())->get(route('dashboard'))
        ->assertSee('Nenhuma instituição para aprovar.')
        ->assertDontSee('Hemocentro Novo');
});

test('o painel da instituição incompleta oferece os pontos pré-cadastrados e o formulário de coordenadas', function () {
    preCadastro();
    $hemocentro = Hemocentro::factory()->create(['status' => 'incompleto']);

    $this->actingAs($hemocentro, 'hemocentro')->get(route('dashboard'))
        ->assertSee('Hemopasso')
        ->assertSee('Assumir este ponto')
        ->assertSee('Minha unidade não está na lista')
        ->assertSee('Enviar para aprovação');
});

test('assumir um ponto pré-cadastrado envia o pedido para a fila do admin', function () {
    $ponto = preCadastro();
    $hemocentro = Hemocentro::factory()->create(['nome' => 'Hemocentro Real', 'status' => 'incompleto']);

    $this->actingAs($hemocentro, 'hemocentro')
        ->post(route('hemocentro.reivindicar'), ['fk_reivindicacao_id' => $ponto->id])
        ->assertRedirect(route('dashboard'));

    expect($hemocentro->fresh())->status->toBe('pendente')->fk_reivindicacao_id->toBe($ponto->id);

    $this->actingAs(User::factory()->create())->get(route('dashboard'))
        ->assertSee('Hemocentro Real')
        ->assertSee('Quer assumir o ponto');
});

test('informar as coordenadas envia o local novo para a fila do admin', function () {
    $hemocentro = Hemocentro::factory()->create(['status' => 'incompleto', 'latitude' => null, 'longitude' => null]);

    $this->actingAs($hemocentro, 'hemocentro')
        ->post(route('hemocentro.localizacao'), ['latitude' => '-29.1678', 'longitude' => '-51.1794'])
        ->assertRedirect(route('dashboard'));

    expect($hemocentro->fresh())
        ->status->toBe('pendente')
        ->latitude->toEqual(-29.1678)
        ->longitude->toEqual(-51.1794);
});

test('coordenadas são obrigatórias e precisam ser válidas', function () {
    $hemocentro = Hemocentro::factory()->create(['status' => 'incompleto']);

    $this->actingAs($hemocentro, 'hemocentro')
        ->post(route('hemocentro.localizacao'), ['latitude' => '200', 'longitude' => ''])
        ->assertSessionHasErrors(['latitude', 'longitude']);

    expect($hemocentro->fresh()->status)->toBe('incompleto');
});

test('instituição já aprovada não volta para a fila ao reivindicar ou informar local', function () {
    $ponto = preCadastro();
    $hemocentro = Hemocentro::factory()->create(['status' => 'ativo']);

    $this->actingAs($hemocentro, 'hemocentro')
        ->post(route('hemocentro.reivindicar'), ['fk_reivindicacao_id' => $ponto->id]);
    $this->actingAs($hemocentro, 'hemocentro')
        ->post(route('hemocentro.localizacao'), ['latitude' => '-29', 'longitude' => '-51']);

    expect($hemocentro->fresh())->status->toBe('ativo')->fk_reivindicacao_id->toBeNull();
});

test('instituição em análise pode cancelar o pedido e escolher de novo', function () {
    $ponto = preCadastro();
    $hemocentro = Hemocentro::factory()->create(['status' => 'pendente', 'fk_reivindicacao_id' => $ponto->id]);

    $this->actingAs($hemocentro, 'hemocentro')
        ->delete(route('hemocentro.pedido.cancelar'))
        ->assertRedirect(route('dashboard'));

    expect($hemocentro->fresh())->status->toBe('incompleto')->fk_reivindicacao_id->toBeNull();
});

test('admin rejeita um local novo e a instituição volta para a escolha com aviso', function () {
    $hemocentro = Hemocentro::factory()->create(['status' => 'pendente']);

    $this->actingAs(User::factory()->create())
        ->post(route('admin.rejeitar', $hemocentro->id))
        ->assertSessionHas('erro');

    expect($hemocentro->fresh()->status)->toBe('rejeitado');

    // Desloga o admin: com ele logado, o /dashboard mostraria a tela de aprovações
    $this->app['auth']->forgetGuards();

    $this->actingAs($hemocentro->fresh(), 'hemocentro')->get(route('dashboard'))
        ->assertSee('recusou o seu último pedido')
        ->assertSee('Enviar para aprovação');

    $this->actingAs($hemocentro->fresh(), 'hemocentro')
        ->post(route('hemocentro.localizacao'), ['latitude' => '-29', 'longitude' => '-51']);

    expect($hemocentro->fresh()->status)->toBe('pendente');
});

test('admin não rejeita instituição que não está na fila', function () {
    $hemocentro = Hemocentro::factory()->create(['status' => 'ativo']);

    $this->actingAs(User::factory()->create())
        ->post(route('admin.rejeitar', $hemocentro->id))
        ->assertNotFound();

    expect($hemocentro->fresh()->status)->toBe('ativo');
});
