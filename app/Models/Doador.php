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

    public function doacoes()
    {
        return $this->hasMany(Doacao::class, 'fk_doador_id');
    }

    public function getDataProximaDoacaoAttribute()
    {
        $ultimaDoacao = $this->doacoes()->orderBy('data_coleta', 'desc')->first();
        
        if (!$ultimaDoacao) {
            return now()->startOfDay(); 
        }

        $intervaloDias = $this->sexo === 'Masculino' ? 60 : 90;
        $limiteAnual = $this->sexo === 'Masculino' ? 4 : 3;

        $dataLiberacaoIntervalo = \Carbon\Carbon::parse($ultimaDoacao->data_coleta)->addDays($intervaloDias)->startOfDay();

        $doacoesUltimos12Meses = $this->doacoes()
            ->where('data_coleta', '>=', now()->subMonths(12))
            ->orderBy('data_coleta', 'asc')
            ->get();

        if ($doacoesUltimos12Meses->count() >= $limiteAnual) {
            $primeiraDoCiclo = \Carbon\Carbon::parse($doacoesUltimos12Meses->first()->data_coleta);
            $dataLiberacaoAnual = $primeiraDoCiclo->addMonths(12)->startOfDay();
            
            return $dataLiberacaoIntervalo->max($dataLiberacaoAnual);
        }

        return $dataLiberacaoIntervalo;
    }

    public function getPodeDoarAttribute()
    {
        if ($this->doacoes()->count() === 0) {
            return true;
        }
        return now()->startOfDay()->greaterThanOrEqualTo(\Carbon\Carbon::parse($this->data_proxima_doacao)->startOfDay());
    }

    // 3. Calcula a Patente (Gamificação com cores de metais/gemas)
    public function getPatenteAttribute()
    {
        $totalDoacoes = $this->doacoes()->count();

        if ($totalDoacoes >= 40) {
            // MESTRE / DESAFIANTE (Roxo / Ametista)
            return ['nome' => 'Embaixador (Mestre)', 'bg' => 'bg-purple-100', 'text' => 'text-purple-700'];
        } elseif ($totalDoacoes >= 20) {
            // DIAMANTE (Ciano / Gelo)
            return ['nome' => 'Parceiro Vital (Diamante)', 'bg' => 'bg-cyan-100', 'text' => 'text-cyan-700'];
        } elseif ($totalDoacoes >= 10) {
            // PLATINA / ESMERALDA (Verde Esmeralda)
            return ['nome' => 'Veterano (Platina)', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-700'];
        } elseif ($totalDoacoes >= 6) {
            // OURO (Amarelo / Dourado)
            return ['nome' => 'Frequente (Ouro)', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-700'];
        } elseif ($totalDoacoes >= 3) {
            // PRATA (Cinza Metálico)
            return ['nome' => 'Voluntário (Prata)', 'bg' => 'bg-slate-200', 'text' => 'text-slate-700'];
        } elseif ($totalDoacoes >= 1) {
            // BRONZE (Laranja Escuro / Cobre)
            return ['nome' => 'Iniciante (Bronze)', 'bg' => 'bg-orange-100', 'text' => 'text-orange-800'];
        }

        return ['nome' => 'Sem Doações', 'bg' => 'bg-gray-100', 'text' => 'text-gray-500'];
    }
}