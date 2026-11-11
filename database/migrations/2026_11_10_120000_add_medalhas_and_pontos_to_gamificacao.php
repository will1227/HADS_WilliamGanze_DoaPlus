<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Medalhas da vitrine (HU12). Cada doação validada vale 100 pontos (HU11),
    // então "pontos_necessarios" / 100 = número de doações do marco.
    private array $medalhas = [
        ['nome' => 'Primeira Gota', 'descricao' => 'Fez a primeira doação de sangue.', 'pontos_necessarios' => 100],
        ['nome' => 'De Volta', 'descricao' => 'Voltou para doar pela segunda vez.', 'pontos_necessarios' => 200],
        ['nome' => 'Mão Amiga', 'descricao' => 'Completou 5 doações.', 'pontos_necessarios' => 500],
        ['nome' => 'Coração de Ouro', 'descricao' => 'Completou 10 doações.', 'pontos_necessarios' => 1000],
        ['nome' => 'Herói do Sangue', 'descricao' => 'Completou 25 doações.', 'pontos_necessarios' => 2500],
        ['nome' => 'Lenda Viva', 'descricao' => 'Completou 50 doações.', 'pontos_necessarios' => 5000],
    ];

    public function up(): void
    {
        // Um doador não pode ganhar a mesma medalha duas vezes
        Schema::table('conquistas', function (Blueprint $table) {
            $table->unique(['fk_doador_id', 'fk_medalha_id']);
        });

        $agora = now();

        DB::table('medalhas')->insert(array_map(
            fn ($medalha) => $medalha + ['created_at' => $agora, 'updated_at' => $agora],
            $this->medalhas
        ));

        // Doações validadas antes desta versão também contam pontos e medalhas
        $doacoesPorDoador = DB::table('doacoes')
            ->where('status_validacao', 'validada')
            ->select('fk_doador_id', DB::raw('count(*) as total'))
            ->groupBy('fk_doador_id')
            ->pluck('total', 'fk_doador_id');

        $medalhas = DB::table('medalhas')->get();

        foreach ($doacoesPorDoador as $doadorId => $total) {
            $pontos = $total * 100;

            DB::table('doadores')->where('id', $doadorId)->update(['pontos' => $pontos]);

            DB::table('conquistas')->insert(
                $medalhas->where('pontos_necessarios', '<=', $pontos)
                    ->map(fn ($medalha) => [
                        'fk_doador_id' => $doadorId,
                        'fk_medalha_id' => $medalha->id,
                        'created_at' => $agora,
                        'updated_at' => $agora,
                    ])
                    ->values()
                    ->all()
            );
        }
    }

    public function down(): void
    {
        // Apagar as medalhas remove as conquistas em cascata
        DB::table('medalhas')->whereIn('nome', array_column($this->medalhas, 'nome'))->delete();
        DB::table('doadores')->update(['pontos' => 0]);

        // O MySQL passa a usar o índice único na chave estrangeira de fk_doador_id
        // (e pode apagar o índice antigo), então ele precisa existir antes
        $recriarIndice = ! Schema::hasIndex('conquistas', 'conquistas_fk_doador_id_foreign');

        Schema::table('conquistas', function (Blueprint $table) use ($recriarIndice) {
            if ($recriarIndice) {
                $table->index('fk_doador_id', 'conquistas_fk_doador_id_foreign');
            }
            $table->dropUnique(['fk_doador_id', 'fk_medalha_id']);
        });
    }
};
