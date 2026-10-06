<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hemocentro extends Model
{
    protected $table = 'hemocentros';

    protected $fillable = [
        'nome', 'email', 'senha', 'cnpj', 
        'telefone', 'endereco', 'latitude', 'longitude', 'status'
    ];

    protected $hidden = [
        'senha',
    ];

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
