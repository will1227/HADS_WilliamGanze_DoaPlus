<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Medalha extends Model
{
    protected $table = 'medalhas';

    protected $fillable = [
        'nome', 'descricao', 'pontos_necessarios'
    ];

    // Faz pontos_necessarios virar número
    protected function casts(): array
    {
        return [
            'pontos_necessarios' => 'integer',
        ];
    }

    // Doadores que já ganharam esta medalha (RF11/HU12)
    public function doadores(): BelongsToMany
    {
        return $this->belongsToMany(Doador::class, 'conquistas', 'fk_medalha_id', 'fk_doador_id')
                    ->withTimestamps();
    }
}