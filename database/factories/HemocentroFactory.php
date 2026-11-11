<?php

namespace Database\Factories;

use App\Models\Hemocentro;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Hemocentro>
 */
class HemocentroFactory extends Factory
{
    /**
     * Hemocentro aprovado (ativo), com posição no mapa em Passo Fundo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => 'Hemocentro '.fake()->city(),
            'email' => fake()->unique()->safeEmail(),
            'senha' => 'password',
            'cnpj' => fake()->unique()->numerify('##############'),
            'telefone' => fake()->unique()->numerify('549########'),
            'endereco' => fake()->streetAddress(),
            'status' => 'ativo',
            'latitude' => -28.2612,
            'longitude' => -52.4083,
        ];
    }
}
