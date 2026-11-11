<?php

use App\Models\Doador;

// E2E de responsividade e PWA (RNF01, RNF02): telas principais no tamanho de celular

test('as telas públicas abrem no celular sem erro de JavaScript e com o manifesto do PWA', function (string $url, string $texto) {
    visit($url)->on()->mobile()
        ->assertSee($texto)
        ->assertSourceHas('<link rel="manifest"')
        ->assertNoJavaScriptErrors();
})->with([
    'mapa e mural' => ['/', 'Onde doar'],
    'login' => ['/login', 'E-mail, CPF ou CNPJ'],
    'escolha do cadastro' => ['/register', 'Você vai doar ou representa uma instituição?'],
    'cadastro do doador' => ['/register/doador', 'Cadastro de doador'],
    'política de privacidade' => ['/privacidade', 'Quais dados guardamos'],
]);

test('o painel do doador abre no celular sem erro de JavaScript', function () {
    Doador::factory()->create(['email' => 'eva@example.com']);

    entrar(visit('/')->on()->mobile(), 'eva@example.com')
        ->assertSee('Suas medalhas')
        ->assertNoJavaScriptErrors();
});
