<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // Importação alterada
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hemocentro extends Authenticatable
{
    use Notifiable;

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
}
