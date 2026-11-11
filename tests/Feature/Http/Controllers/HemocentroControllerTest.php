<?php

use App\Models\Hemocentro;

// Auditoria (semana 10): telefone repetido dava erro 500 em vez de avisar a instituição

test('cadastro de instituição com telefone já usado mostra o erro no campo', function () {
    Hemocentro::factory()->create(['telefone' => '54999990000']);

    $resposta = $this->post(route('hemocentros.store'), [
        'nome' => 'Hemocentro Novo',
        'email' => 'novo@example.com',
        'senha' => 'segredo123',
        'cnpj' => '11.222.333/0001-81',
        'telefone' => '(54) 99999-0000',
        'endereco' => 'Rua Um, 10',
    ]);

    $resposta->assertSessionHasErrors('telefone');
    $this->assertDatabaseCount('hemocentros', 1);
});

test('instituição não consegue trocar o telefone para o de outra', function () {
    Hemocentro::factory()->create(['telefone' => '54999990000']);
    $hemocentro = Hemocentro::factory()->create();

    $resposta = $this->actingAs($hemocentro, 'hemocentro')->put(route('hemocentro.perfil.update'), [
        'nome' => $hemocentro->nome,
        'email' => $hemocentro->email,
        'endereco' => $hemocentro->endereco,
        'telefone' => '(54) 99999-0000',
    ]);

    $resposta->assertSessionHasErrors('telefone');
    expect($hemocentro->fresh()->telefone)->not->toBe('54999990000');
});
