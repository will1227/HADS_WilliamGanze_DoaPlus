<?php

namespace Database\Factories;

use App\Models\Doador;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Doador>
 */
class DoadorFactory extends Factory
{
    /**
     * Doador apto a doar: 30 anos, sem doações e sem localização.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => fake()->name(),
            'cpf' => fake()->unique()->numerify('###########'),
            'data_nascimento' => now()->subYears(30)->toDateString(),
            'sexo' => 'Feminino',
            'peso' => 65,
            'tipo_sanguineo' => 'O-',
            'email' => fake()->unique()->safeEmail(),
            'senha' => 'password',
        ];
    }

    public function tipo(string $tipoSanguineo): static
    {
        return $this->state(fn () => ['tipo_sanguineo' => $tipoSanguineo]);
    }

    public function em(float $latitude, float $longitude): static
    {
        return $this->state(fn () => ['latitude' => $latitude, 'longitude' => $longitude]);
    }
}
