<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Notifications\Notifiable; 
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doador extends Authenticatable
{
    use Notifiable;

    protected $table = 'doadores';

    protected $fillable = [
        'nome', 'cpf', 'data_nascimento', 'sexo', 
        'peso', 'tipo_sanguineo', 'email', 'senha'
    ];

    protected $hidden = [
        'senha', 'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->senha;
    }

    protected function casts(): array
    {
        return [
            'senha' => 'hashed',
        ];
    }

    public function agendamentos(): HasMany
    {
        return $this->hasMany(Agendamento::class, 'fk_doador_id');
    }

    public function medalhas(): HasMany
    {
        return $this->hasMany(Medalha::class, 'fk_doador_id');
    }

    // Relacionamento (Um Doador tem várias Doações)
    public function doacoes()
    {
        return $this->hasMany(Doacao::class, 'fk_doador_id');
    }

    // 1. Calcula a data exata em que ele poderá doar novamente
    public function getDataProximaDoacaoAttribute()
    {
        // Pega a última doação do doador
        $ultimaDoacao = $this->doacoes()->orderBy('data_coleta', 'desc')->first();
        
        // Se nunca doou, pode doar hoje
        if (!$ultimaDoacao) {
            return now(); 
        }

        $intervaloDias = $this->sexo === 'Masculino' ? 60 : 90;
        $limiteAnual = $this->sexo === 'Masculino' ? 4 : 3;

        // Regra 1: Data liberada com base no intervalo mínimo (60 ou 90 dias)
        $dataLiberacaoIntervalo = \Carbon\Carbon::parse($ultimaDoacao->data_coleta)->addDays($intervaloDias);

        // Regra 2: Verifica limite anual (últimos 12 meses)
        $doacoesUltimos12Meses = $this->doacoes()
            ->where('data_coleta', '>=', now()->subMonths(12))
            ->orderBy('data_coleta', 'asc')
            ->get();

        if ($doacoesUltimos12Meses->count() >= $limiteAnual) {
            // Se bateu o limite, ele só pode doar 1 ano após a PRIMEIRA doação desse ciclo
            $primeiraDoCiclo = \Carbon\Carbon::parse($doacoesUltimos12Meses->first()->data_coleta);
            $dataLiberacaoAnual = $primeiraDoCiclo->addMonths(12);
            
            return $dataLiberacaoIntervalo->max($dataLiberacaoAnual);
        }

        return $dataLiberacaoIntervalo;
    }

    // 2. Retorna true ou false se ele está apto hoje
    public function getPodeDoarAttribute()
    {
        return now()->greaterThanOrEqualTo($this->data_proxima_doacao);
    }
}
