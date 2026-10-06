<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Agendamento extends Model
{
    protected $table = 'agendamentos';

    protected $fillable = [
        'fk_doador_id', 'fk_hemocentro_id', 'data_hora', 'status'
    ];

    protected function casts(): array
    {
        return [
            'data_hora' => 'datetime',
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