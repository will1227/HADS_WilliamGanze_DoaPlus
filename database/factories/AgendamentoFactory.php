<?php

namespace Database\Factories;

use App\Models\Agendamento;
use App\Models\Doador;
use App\Models\Hemocentro;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Agendamento>
 */
class AgendamentoFactory extends Factory
{
    /**
     * Agendamento confirmado para uma hora atrás (já pode ser concluído).
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'fk_doador_id' => Doador::factory(),
            'fk_hemocentro_id' => Hemocentro::factory(),
            'data_hora' => now()->subHour(),
            'status' => 'confirmado',
        ];
    }

    // Nos testes: agendamento ainda pendente
    public function pendente(): static
    {
        return $this->state(fn () => ['status' => 'pendente']);
    }
}
