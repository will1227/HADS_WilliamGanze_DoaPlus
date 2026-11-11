<?php

use App\Models\Doador;

// E2E do cadastro do doador com o consentimento da LGPD (HU01, RNF06)

function preencherCadastro($pagina)
{
    return $pagina
        ->fill('nome', 'Bruno Lima')
        ->type('cpf', '12345678909')
        ->fill('data_nascimento', '1994-05-20')
        ->select('sexo', 'Masculino')
        ->fill('peso', '78')
        ->select('tipo_sanguineo', 'B+')
        ->fill('email', 'bruno@example.com')
        ->fill('senha', 'segredo123');
}

test('doador se cadastra aceitando a Política de Privacidade e entra no painel', function () {
    $pagina = preencherCadastro(visit('/register/doador'))
        ->check('consentimento')
        ->press('Criar conta de doador')
        ->assertSee('Conta de Doador criada com sucesso! Faça seu login.');

    entrar($pagina, 'bruno@example.com', 'segredo123')
        ->assertSee('Olá, Bruno.')
        ->assertSee('Você já pode doar.')
        ->assertNoJavaScriptErrors();

    expect(Doador::where('email', 'bruno@example.com')->value('consentimento_em'))->not->toBeNull();
});

test('sem marcar o consentimento o navegador não envia o cadastro', function () {
    preencherCadastro(visit('/register/doador'))
        ->press('Criar conta de doador')
        ->assertPathIs('/register/doador')
        ->assertNotChecked('consentimento');

    $this->assertDatabaseEmpty('doadores');
});

test('o link da Política de Privacidade abre a página que explica os dados', function () {
    visit('/privacidade')
        ->assertSee('Quais dados guardamos')
        ->assertSee('Excluir minha conta')
        ->assertNoJavaScriptErrors();
});
