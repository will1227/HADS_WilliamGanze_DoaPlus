<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class Alerta extends Model
{
    // Distância máxima entre o doador e o hemocentro para receber o alerta (HU10)
    public const RAIO_KM = 50;

    public const TIPOS_SANGUINEOS = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

    protected $table = 'alertas';

    protected $fillable = [
        'fk_hemocentro_id', 'tipo_sanguineo', 'mensagem', 'data_publicacao', 'data_expiracao'
    ];

    protected function casts(): array
    {
        return [
            'data_publicacao' => 'datetime',
            'data_expiracao' => 'datetime',
        ];
    }

    public function hemocentro(): BelongsTo
    {
        return $this->belongsTo(Hemocentro::class, 'fk_hemocentro_id');
    }

    // Alertas ainda no prazo e não encerrados pelo hemocentro
    public function scopeAtivos(Builder $query): void
    {
        $query->where('data_expiracao', '>', now());
    }

    // Doadores que recebem a notificação: mesmo tipo sanguíneo, a até RAIO_KM do hemocentro e aptos a doar agora
    public function destinatarios(): Collection
    {
        $hemocentro = $this->hemocentro;

        if (is_null($hemocentro->latitude) || is_null($hemocentro->longitude)) {
            return collect();
        }

        $lat = (float) $hemocentro->latitude;
        $lng = (float) $hemocentro->longitude;

        // Primeiro corta no banco por um quadrado em volta do hemocentro (1 grau de latitude ≈ 111 km)...
        $grausLat = self::RAIO_KM / 111;
        $grausLng = self::RAIO_KM / (111 * max(cos(deg2rad($lat)), 0.01));

        return Doador::where('tipo_sanguineo', $this->tipo_sanguineo)
            ->whereBetween('latitude', [$lat - $grausLat, $lat + $grausLat])
            ->whereBetween('longitude', [$lng - $grausLng, $lng + $grausLng])
            ->get()
            // ...e depois confere a distância real e a aptidão de cada um
            ->filter(fn (Doador $doador) => self::distanciaKm($lat, $lng, (float) $doador->latitude, (float) $doador->longitude) <= self::RAIO_KM)
            ->filter(fn (Doador $doador) => $doador->pode_doar)
            ->values();
    }

    // Distância em linha reta (fórmula de Haversine), a mesma usada no mapa da tela inicial
    public static function distanciaKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $s = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return 6371 * 2 * asin(sqrt($s));
    }
}
