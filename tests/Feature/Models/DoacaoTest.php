<?php

use App\Models\Doacao;
use App\Models\Doador;

// Gatilho da gamificação no model Doacao (HU11/HU12)

test('doação pendente não dá pontos', function () {
    $doador = Doador::factory()->create();

    Doacao::factory()->pendente()->for($doador)->create();

    expect($doador->fresh()->pontos)->toBe(0);
});

test('doação que passa de pendente para validada dá 100 pontos', function () {
    $doacao = Doacao::factory()->pendente()->create();

    $doacao->update(['status_validacao' => 'validada']);

    expect($doacao->doador->fresh()->pontos)->toBe(100);
});

test('corrigir outro campo de uma doação já validada não soma pontos outra vez', function () {
    $doacao = Doacao::factory()->create();

    $doacao->update(['data_coleta' => '2026-11-01']);

    expect($doacao->doador->fresh()->pontos)->toBe(100);
});

test('as medalhas acompanham o número de doações validadas sem repetir', function (int $doacoes, array $medalhas) {
    $doador = Doador::factory()->create();

    Doacao::factory()->count($doacoes)->for($doador)->create();

    expect($doador->fresh()->pontos)->toBe($doacoes * 100);
    expect($doador->medalhas()->orderBy('pontos_necessarios')->pluck('nome')->all())->toBe($medalhas);
    $this->assertDatabaseCount('conquistas', count($medalhas));
})->with([
    'segunda doação' => [2, ['Primeira Gota', 'De Volta']],
    'quatro doações, ainda sem Mão Amiga' => [4, ['Primeira Gota', 'De Volta']],
    'quinta doação libera Mão Amiga' => [5, ['Primeira Gota', 'De Volta', 'Mão Amiga']],
]);
