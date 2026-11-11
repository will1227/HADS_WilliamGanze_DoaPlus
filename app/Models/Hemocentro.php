<?php

namespace App\Models;

use Database\Factories\HemocentroFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Importação alterada
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hemocentro extends Authenticatable
{
    /** @use HasFactory<HemocentroFactory> */
    use HasFactory, Notifiable;

    protected $table = 'hemocentros';

    protected $fillable = [
        'nome', 'email', 'senha', 'cnpj', 
        'telefone', 'endereco', 'latitude', 'longitude', 'status'
    ];

    protected $hidden = [
        'senha', 'remember_token',
    ];

    // Diz ao Laravel que a coluna de senha se chama 'senha'
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
        return $this->hasMany(Agendamento::class, 'fk_hemocentro_id');
    }

    public function doacoes(): HasMany
    {
        return $this->hasMany(Doacao::class, 'fk_hemocentro_id');
    }

    public function alertas(): HasMany
    {
        return $this->hasMany(Alerta::class, 'fk_hemocentro_id');
    }

    // Conta recém-criada ('incompleto') ou com pedido recusado pelo admin ('rejeitado'):
    // ainda precisa dizer onde fica a unidade antes de entrar na fila de aprovação
    public function precisaEscolherLocal(): bool
    {
        return in_array($this->status, ['incompleto', 'rejeitado']);
    }

    public function localReivindicado()
    {
        return $this->belongsTo(Hemocentro::class, 'fk_reivindicacao_id');
    }
}
