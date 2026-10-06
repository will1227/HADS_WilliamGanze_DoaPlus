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

    protected function casts(): array
    {
        return [
            'pontos_necessarios' => 'integer',
        ];
    }

    public function doadores(): BelongsToMany
    {
        return $this->belongsToMany(Doador::class, 'conquistas', 'fk_medalha_id', 'fk_doador_id')
                    ->withTimestamps();
    }
}