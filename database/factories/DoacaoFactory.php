<?php

namespace Database\Factories;

use App\Models\Doacao;
use App\Models\Doador;
use App\Models\Hemocentro;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Doacao>
 */
class DoacaoFactory extends Factory
{
    /**
     * Doação já validada pelo hemocentro.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'fk_doador_id' => Doador::factory(),
            'fk_hemocentro_id' => Hemocentro::factory(),
            'data_coleta' => now()->toDateString(),
            'status_validacao' => 'validada',
        ];
    }

    // Nos testes: doação que ainda não foi validada
    public function pendente(): static
    {
        return $this->state(fn () => ['status_validacao' => 'pendente']);
    }
}
