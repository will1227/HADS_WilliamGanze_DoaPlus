<?php

use App\Models\Doacao;
use App\Models\Doador;

use function Pest\Laravel\travelTo;

// Painel do doador: saldo de pontos (HU11), vitrine de medalhas (HU12) e compartilhar (HU13)

test('o painel do doador mostra o saldo, as medalhas conquistadas e quanto falta para as outras', function () {
    travelTo('2026-11-10 10:00:00');
    $doador = Doador::factory()->create();
    Doacao::factory()->count(2)->for($doador)->create(['data_coleta' => '2026-06-01']);

    $resposta = $this->actingAs($doador->fresh(), 'doador')->get(route('dashboard'));

    $resposta->assertSeeInOrder(['Pontos', '200', 'Suas medalhas', '2 de 6'])
        ->assertSee('Conquistada em 10/11/2026')
        ->assertSee('Bloqueada · faltam 300 pontos');
});

test('a medalha conquistada tem botão que abre o WhatsApp com a mensagem e o link da página pública', function () {
    $doador = Doador::factory()->create();
    Doacao::factory()->for($doador)->create(['data_coleta' => '2026-06-01']);

    $resposta = $this->actingAs($doador->fresh(), 'doador')->get(route('dashboard'));

    $resposta->assertSee(
        'https://wa.me/?text=Conquistei%20a%20medalha%20%E2%80%9CPrimeira%20Gota%E2%80%9D%20no%20DoaPlus%20doando%20sangue%21'
        .'%20Uma%20doa%C3%A7%C3%A3o%20pode%20ajudar%20at%C3%A9%204%20pessoas.%20Venha%20doar%20tamb%C3%A9m%3A%20http%3A%2F%2Flocalhost',
        false
    );
});

test('doador sem medalha não vê botão de compartilhar', function () {
    $doador = Doador::factory()->create();

    $resposta = $this->actingAs($doador, 'doador')->get(route('dashboard'));

    $resposta->assertSee('0 de 6')->assertDontSee('wa.me', false);
});
