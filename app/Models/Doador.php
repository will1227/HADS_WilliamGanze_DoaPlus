<?php

namespace App\Models;

use Database\Factories\DoadorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Notifications\Notifiable; 
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use App\Notifications\AlertaUrgente;
use NotificationChannels\WebPush\HasPushSubscriptions;

class Doador extends Authenticatable
{
    // HasPushSubscriptions: inscrições de notificação push, uma por navegador/aparelho (RNF02)
    /** @use HasFactory<DoadorFactory> */
    use HasFactory, HasPushSubscriptions, Notifiable;

    // Pontos ganhos a cada doação validada pelo hemocentro (RF10/HU11)
    public const PONTOS_POR_DOACAO = 100;

    protected $table = 'doadores';

    protected $fillable = [
        'nome', 'cpf', 'data_nascimento', 'sexo', 
        'peso', 'tipo_sanguineo', 'latitude', 'longitude', 'email', 'senha', 'consentimento_em'
    ];

    protected $hidden = [
        'senha', 'remember_token',
    ];

    // Diz ao Laravel que a coluna de senha se chama 'senha'
    public function getAuthPassword()
    {
        return $this->senha;
    }

    // A senha é criptografada sozinha ao salvar; pontos vira número e consentimento_em vira data
    protected function casts(): array
    {
        return [
            'senha' => 'hashed',
            'pontos' => 'integer',
            'consentimento_em' => 'datetime',
        ];
    }

    // Todos os agendamentos do doador
    public function agendamentos(): HasMany
    {
        return $this->hasMany(Agendamento::class, 'fk_doador_id');
    }

    // Alertas de urgência recebidos, ainda não vistos e ainda no mural (RF12/HU10).
    // Confere o alerta no banco porque o hemocentro pode encerrá-lo antes da validade.
    public function alertasNovos(): Collection
    {
        $notificacoes = $this->unreadNotifications->where('type', AlertaUrgente::class);

        $alertasAtivos = Alerta::ativos()
            ->whereIn('id', $notificacoes->pluck('data.alerta_id'))
            ->pluck('id');

        return $notificacoes
            ->filter(fn ($notificacao) => $alertasAtivos->contains($notificacao->data['alerta_id']))
            ->values();
    }

    // Diz se o doador já informou latitude e longitude
    public function temLocalizacao(): bool
    {
        return ! is_null($this->latitude) && ! is_null($this->longitude);
    }

    // Agendamentos ainda não finalizados (o doador só pode ter um por vez) (RF02/HU04)
    public function agendamentosEmAberto(): HasMany
    {
        return $this->agendamentos()->whereIn('status', ['pendente', 'confirmado']);
    }

    // Medalhas conquistadas (tabela conquistas) (RF11/HU12)
    public function medalhas(): BelongsToMany
    {
        return $this->belongsToMany(Medalha::class, 'conquistas', 'fk_doador_id', 'fk_medalha_id')
                    ->withTimestamps();
    }

    // Histórico de doações do doador
    public function doacoes()
    {
        return $this->hasMany(Doacao::class, 'fk_doador_id');
    }

    // Soma os pontos de uma doação validada e libera as medalhas alcançadas (RF10/HU11 e RF11/HU12).
    // Chamado pelo model Doacao; increment() soma direto no banco, sem ler o saldo antes.
    public function receberPontosDaDoacao(): void
    {
        $this->increment('pontos', self::PONTOS_POR_DOACAO);

        $this->medalhas()->syncWithoutDetaching(
            Medalha::where('pontos_necessarios', '<=', $this->pontos)->pluck('id')
        );
    }

    // Calcula a partir de quando o doador pode doar de novo (intervalo de 60/90 dias e limite de 4/3 por ano) (RF03/HU03)
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

    // Faixa etária legal para doação: 16 a 69 anos (RF03/HU03)
    public function getDentroDaFaixaEtariaAttribute()
    {
        $idade = \Carbon\Carbon::parse($this->data_nascimento)->age;

        return $idade >= 16 && $idade <= 69;
    }

    // Diz se o doador pode doar hoje: idade dentro da faixa e intervalo de descanso cumprido (RF03/HU03)
    public function getPodeDoarAttribute()
    {
        if (!$this->dentro_da_faixa_etaria) {
            return false;
        }

        if ($this->doacoes()->count() === 0) {
            return true;
        }
        return now()->startOfDay()->greaterThanOrEqualTo(\Carbon\Carbon::parse($this->data_proxima_doacao)->startOfDay());
    }

    // 3. Calcula a Patente (Gamificação com cores de metais/gemas) (RF10/HU11)
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