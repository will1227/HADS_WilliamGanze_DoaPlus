<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Doador extends Model
{
    protected $table = 'doadores';

    protected $fillable = [
        'nome', 'email', 'senha', 'cpf', 
        'data_nascimento', 'sexo', 'peso', 
        'tipo_sanguineo', 'pontos'
    ];

    protected $hidden = [
        'senha', // Esconde a senha em serializações (JSON)
    ];

    protected function casts(): array
    {
        return [
            'data_nascimento' => 'date',
            'peso' => 'decimal:2',
            'senha' => 'hashed', // Prepara para Auth futuramente
        ];
    }

    public function agendamentos(): HasMany
    {
        return $this->hasMany(Agendamento::class, 'fk_doador_id');
    }

    public function doacoes(): HasMany
    {
        return $this->hasMany(Doacao::class, 'fk_doador_id');
    }

    public function medalhas(): BelongsToMany
    {
        return $this->belongsToMany(Medalha::class, 'conquistas', 'fk_doador_id', 'fk_medalha_id')
                    ->withTimestamps();
    }
}