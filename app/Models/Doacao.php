<?php

namespace App\Models;

use Database\Factories\DoacaoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Doacao extends Model
{
    /** @use HasFactory<DoacaoFactory> */
    use HasFactory;

    protected $table = 'doacoes';

    protected $fillable = [
        'fk_doador_id', 'fk_hemocentro_id', 'data_coleta', 'status_validacao'
    ];

    // Gatilho da gamificação (RF10/HU11): quando a doação passa a "validada", o doador ganha os pontos.
    // isDirty() garante que salvar de novo uma doação já validada não pontua duas vezes.
    protected static function booted(): void
    {
        static::saved(function (Doacao $doacao) {
            if ($doacao->status_validacao === 'validada' && $doacao->isDirty('status_validacao')) {
                $doacao->doador->receberPontosDaDoacao();
            }
        });
    }

    // Faz data_coleta virar data (Carbon)
    protected function casts(): array
    {
        return [
            'data_coleta' => 'date',
        ];
    }

    // Quem doou
    public function doador(): BelongsTo
    {
        return $this->belongsTo(Doador::class, 'fk_doador_id');
    }

    // Onde a coleta foi feita
    public function hemocentro(): BelongsTo
    {
        return $this->belongsTo(Hemocentro::class, 'fk_hemocentro_id');
    }
}