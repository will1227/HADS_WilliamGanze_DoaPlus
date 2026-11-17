<?php

namespace Database\Seeders;

use App\Models\Doacao;
use App\Models\Doador;
use App\Models\Hemocentro;
use Illuminate\Database\Seeder;

class DoadorSeeder extends Seeder
{
    // Cria dois doadores de exemplo: um novo e um veterano com medalhas
    public function run(): void
    {
        // Doadora nova: conta criada agora, sem nenhuma doação (pode doar já)
        Doador::create([
            'nome' => 'Ana Souza',
            'email' => 'ana@doaplus.com',
            'senha' => 'senha123',
            'cpf' => '11111111111',
            'data_nascimento' => '2000-05-12',
            'sexo' => 'Feminino',
            'peso' => 62,
            'tipo_sanguineo' => 'O-',
            'consentimento_em' => now(),
        ]);

        // Doador veterano: 10 doações validadas, uma a cada 120 dias, a última há 30 dias.
        // Respeita o intervalo de 60 dias e o limite de 4 por ano (homem), por isso
        // ainda não pode doar hoje e o painel mostra a data da próxima doação.
        $carlos = Doador::create([
            'nome' => 'Carlos Pereira',
            'email' => 'carlos@doaplus.com',
            'senha' => 'senha123',
            'cpf' => '22222222222',
            'data_nascimento' => '1985-09-03',
            'sexo' => 'Masculino',
            'peso' => 80,
            'tipo_sanguineo' => 'A+',
            'latitude' => -28.2620,
            'longitude' => -52.4064,
            'consentimento_em' => now()->subYears(3),
        ]);

        $passoFundo = Hemocentro::where('nome', 'like', 'Hemopasso%')->firstOrFail();
        $portoAlegre = Hemocentro::where('nome', 'like', 'HEMORGS%')->firstOrFail();

        for ($i = 0; $i < 10; $i++) {
            // Criar a doação já "validada" dispara o gatilho do model Doacao, que soma
            // os pontos e libera as medalhas como no sistema real (RF10/HU11 e RF11/HU12)
            Doacao::create([
                'fk_doador_id' => $carlos->id,
                'fk_hemocentro_id' => ($i % 4 === 3 ? $portoAlegre : $passoFundo)->id,
                'data_coleta' => now()->subDays(30 + 120 * $i)->toDateString(),
                'status_validacao' => 'validada',
            ]);
        }
    }
}
