<?php

namespace Database\Seeders;

use App\Models\Hemocentro;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class HemocentroSeeder extends Seeder
{
    public function run(): void
    {
        $hemocentros = [
            [
                'nome' => 'Hemopasso (Hemocentro Regional de Passo Fundo)',
                'endereco' => 'Avenida Sete de Setembro, 1055, Passo Fundo - RS',
                'latitude' => -28.261298,
                'longitude' => -52.408332,
            ],
            [
                'nome' => 'HEMORGS (Hemocentro do Estado do RS)',
                'endereco' => 'Avenida Bento Gonçalves, 3722 - Partenon, Porto Alegre - RS',
                'latitude' => -30.059235,
                'longitude' => -51.173873,
            ],
            [
                'nome' => 'Hemocs (Hemocentro Regional de Caxias do Sul)',
                'endereco' => 'Rua Ernesto Alves, 2260, Caxias do Sul - RS',
                'latitude' => -29.167389,
                'longitude' => -51.179421,
            ],
            [
                'nome' => 'Hemosm (Hemocentro Regional de Santa Maria)',
                'endereco' => 'Alameda Santiago do Chile, 35 - Nossa Sra. de Lourdes, Santa Maria - RS',
                'latitude' => -29.691456,
                'longitude' => -53.795143,
            ],
            [
                'nome' => 'Hemopel (Hemocentro Regional de Pelotas)',
                'endereco' => 'Avenida Bento Gonçalves, 4569 - Centro, Pelotas - RS',
                'latitude' => -31.764512,
                'longitude' => -52.337587,
            ],
            [
                'nome' => 'Hemocruz (Hemocentro Regional de Cruz Alta)',
                'endereco' => 'Rua Barão do Rio Branco, 1445, Cruz Alta - RS',
                'latitude' => -28.638541,
                'longitude' => -53.606219,
            ],
            [
                'nome' => 'Hemosar (Hemocentro Regional de Santa Rosa)',
                'endereco' => 'Rua Boa Vista, 401, Santa Rosa - RS',
                'latitude' => -27.873295,
                'longitude' => -54.478931,
            ],
            [
                'nome' => 'Hemoeste (Hemocentro Regional de Alegrete)',
                'endereco' => 'Rua General Sampaio, 10, Alegrete - RS',
                'latitude' => -29.791552,
                'longitude' => -55.794689,
            ],
            [
                'nome' => 'Hospital de Clínicas de Porto Alegre',
                'endereco' => 'Rua São Manoel, 543, 2º andar, Porto Alegre - RS',
                'latitude' => -30.040147,
                'longitude' => -51.196582,
            ],
        ];

        foreach ($hemocentros as $key => $data) {
            Hemocentro::create([
                'nome' => $data['nome'],
                'endereco' => $data['endereco'],
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'email' => 'contato' . $key . '@hemocentro.rs.gov.br',
                'senha' => Hash::make('senha123'), // Senha padrão para todos
                'cnpj' => mt_rand(10000000, 99999999) . '0001' . mt_rand(10, 99), // CNPJ aleatório fictício
                'telefone' => '(51) 3' . mt_rand(100, 999) . '-' . mt_rand(1000, 9999), // Telefone aleatório
                'status' => 'ativo', // Importante para aparecer no mapa
            ]);
        }
    }
}
