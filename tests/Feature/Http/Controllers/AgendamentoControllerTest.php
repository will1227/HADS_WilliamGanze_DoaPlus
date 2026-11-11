<?php

use App\Models\Agendamento;

use function Pest\Laravel\travelTo;

// Gamificação (HU11/HU12): os pontos só entram quando o hemocentro conclui a doação (HU06)

test('concluir a doação dá 100 pontos e libera a medalha Primeira Gota', function () {
    travelTo('2026-11-10 10:00:00');
    $agendamento = Agendamento::factory()->create();

    $resposta = $this->actingAs($agendamento->hemocentro, 'hemocentro')
        ->patch(route('agendamentos.status', $agendamento), ['status' => 'concluido']);

    $resposta->assertRedirectToRoute('dashboard')->assertSessionHas('status', 'Doação registrada com sucesso!');
    $this->assertDatabaseHas('doacoes', ['fk_doador_id' => $agendamento->fk_doador_id, 'status_validacao' => 'validada']);
    expect($agendamento->doador->fresh()->pontos)->toBe(100);
    expect($agendamento->doador->medalhas()->pluck('nome')->all())->toBe(['Primeira Gota']);
});

test('confirmar o horário não dá pontos nem medalhas', function () {
    $agendamento = Agendamento::factory()->pendente()->create();

    $this->actingAs($agendamento->hemocentro, 'hemocentro')
        ->patch(route('agendamentos.status', $agendamento), ['status' => 'confirmado']);

    expect($agendamento->doador->fresh()->pontos)->toBe(0);
    $this->assertDatabaseEmpty('conquistas');
});

test('enviar de novo a conclusão de uma doação já concluída não soma pontos outra vez', function () {
    travelTo('2026-11-10 10:00:00');
    $agendamento = Agendamento::factory()->create();
    $this->actingAs($agendamento->hemocentro, 'hemocentro')
        ->patch(route('agendamentos.status', $agendamento), ['status' => 'concluido']);

    $resposta = $this->patch(route('agendamentos.status', $agendamento), ['status' => 'concluido']);

    $resposta->assertSessionHas('status', 'Este agendamento já foi finalizado.');
    $this->assertDatabaseCount('doacoes', 1);
    expect($agendamento->doador->fresh()->pontos)->toBe(100);
});
