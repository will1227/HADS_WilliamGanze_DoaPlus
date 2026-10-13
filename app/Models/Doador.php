<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // Importação alterada
use Illuminate\Notifications\Notifiable; // Permite envio de e-mails de redefinição de senha
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
}
