<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alerta extends Model
{
    protected $table = 'alertas';

    protected $fillable = [
        'fk_hemocentro_id', 'tipo_sanguineo', 'mensagem', 'data_publicacao'
    ];

    protected function casts(): array
    {
        return [
            'data_publicacao' => 'datetime',
        ];
    }

    public function hemocentro(): BelongsTo
    {
        return $this->belongsTo(Hemocentro::class, 'fk_hemocentro_id');
    }
}