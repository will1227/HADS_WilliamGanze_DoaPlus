<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // Dados padrão do sistema: "php artisan migrate:fresh --seed".
    // Sem WithoutModelEvents: o DoadorSeeder precisa do gatilho de pontos do model Doacao.
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            HemocentroSeeder::class,
            DoadorSeeder::class,
        ]);
    }
}
