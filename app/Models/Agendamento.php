<?php

namespace App\Models;

use Database\Factories\AgendamentoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Agendamento extends Model
{
    /** @use HasFactory<AgendamentoFactory> */
    use HasFactory;

    protected $table = 'agendamentos';

    protected $fillable = [
        'fk_doador_id', 'fk_hemocentro_id', 'data_hora', 'status'
    ];

    // Faz o Laravel tratar data_hora como data (Carbon)
    protected function casts(): array
    {
        return [
            'data_hora' => 'datetime',
        ];
    }

    // Doador que marcou o agendamento
    public function doador(): BelongsTo
    {
        return $this->belongsTo(Doador::class, 'fk_doador_id');
    }

    // Hemocentro onde a doação vai acontecer
    public function hemocentro(): BelongsTo
    {
        return $this->belongsTo(Hemocentro::class, 'fk_hemocentro_id');
    }
}