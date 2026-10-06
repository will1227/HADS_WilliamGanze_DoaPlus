<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Doacao extends Model
{
    protected $table = 'doacoes';

    protected $fillable = [
        'fk_doador_id', 'fk_hemocentro_id', 'data_coleta', 'status_validacao'
    ];

    protected function casts(): array
    {
        return [
            'data_coleta' => 'date',
        ];
    }

    public function doador(): BelongsTo
    {
        return $this->belongsTo(Doador::class, 'fk_doador_id');
    }

    public function hemocentro(): BelongsTo
    {
        return $this->belongsTo(Hemocentro::class, 'fk_hemocentro_id');
    }
}