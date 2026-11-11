<?php

use App\Models\Hemocentro;

use function Pest\Laravel\travelTo;

// Mural público de urgências com o botão de compartilhar no WhatsApp (HU13)

test('cada alerta do mural tem link de WhatsApp com o hemocentro, o tipo, a validade e o link do DoaPlus', function () {
    travelTo('2026-11-10 10:00:00');
    $hemocentro = Hemocentro::factory()->create(['nome' => 'Hemopasso']);
    $hemocentro->alertas()->create(['tipo_sanguineo' => 'O-', 'mensagem' => 'Estoque crítico', 'data_publicacao' => now(), 'data_expiracao' => now()->addDays(5)]);

    $resposta = $this->get(route('home'));

    $resposta->assertSee(
        'https://wa.me/?text=O%20Hemopasso%20precisa%20de%20sangue%20O-%20at%C3%A9%2015%2F11.%20Se%20voc%C3%AA%20puder%2C%20doe%3A%20http%3A%2F%2Flocalhost',
        false
    );
});

test('nome de hemocentro com HTML vai codificado no link de compartilhar', function () {
    $hemocentro = Hemocentro::factory()->create(['nome' => '"><script>alert(1)</script>']);
    $hemocentro->alertas()->create(['tipo_sanguineo' => 'O-', 'mensagem' => 'Estoque crítico', 'data_publicacao' => now(), 'data_expiracao' => now()->addDays(5)]);

    $html = $this->get(route('home'))->getContent();

    expect($html)
        ->toContain('%22%3E%3Cscript%3Ealert%281%29%3C%2Fscript%3E')
        ->not->toContain('<script>alert(1)</script>');
});

test('a Política de Privacidade é pública e explica como excluir a conta', function () {
    $resposta = $this->get(route('privacidade'));

    $resposta->assertSee('Política de Privacidade')->assertSee('Excluir minha conta');
});

test('o "Criar conta" do cabeçalho leva à escolha entre doador e instituição', function () {
    $this->get(route('home'))
        ->assertSee('<a href="'.route('register').'" class="criar">Criar conta</a>', false);

    $this->get(route('register'))
        ->assertOk()
        ->assertSee(url('/register/doador'))
        ->assertSee(url('/register/hemocentro'));
});
