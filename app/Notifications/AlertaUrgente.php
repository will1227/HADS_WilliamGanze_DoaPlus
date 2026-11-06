<?php

namespace App\Notifications;

use App\Models\Alerta;
use Illuminate\Notifications\Notification;

// Aviso de urgência que aparece no painel do doador (HU10). O push da semana 9 entra como mais um canal aqui.
class AlertaUrgente extends Notification
{
    public function __construct(public Alerta $alerta)
    {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Cópia dos dados do alerta no momento do envio.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'alerta_id' => $this->alerta->id,
            'hemocentro_id' => $this->alerta->hemocentro->id,
            'hemocentro' => $this->alerta->hemocentro->nome,
            'tipo_sanguineo' => $this->alerta->tipo_sanguineo,
            'mensagem' => $this->alerta->mensagem,
            'data_expiracao' => $this->alerta->data_expiracao->toIso8601String(),
        ];
    }
}
