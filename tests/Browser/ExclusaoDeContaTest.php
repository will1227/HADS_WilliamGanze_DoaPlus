<?php

use App\Models\Doacao;
use App\Models\Doador;

// E2E da exclusão de conta pela LGPD (RNF06)

test('doador exclui a própria conta e não consegue mais entrar', function () {
    $doador = Doador::factory()->create(['email' => 'davi@example.com']);
    Doacao::factory()->for($doador)->create();

    $pagina = entrar(visit('/'), 'davi@example.com')
        ->navigate('/meu-perfil')
        ->fill('senha_atual', 'password');
    // Responde "OK" ao confirm() "Excluir sua conta e todos os seus dados?"
    $pagina->script('window.confirm = () => true');

    $pagina->press('#excluir button[type=submit]')
        ->assertPathIs('/')
        ->assertSee('Entrar');

    entrar($pagina, 'davi@example.com')
        ->assertSee('Essas credenciais não foram encontradas');

    $this->assertModelMissing($doador);
    $this->assertDatabaseEmpty('doacoes');
});
