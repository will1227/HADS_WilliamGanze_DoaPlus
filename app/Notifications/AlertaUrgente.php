<?php

namespace App\Notifications;

use App\Models\Alerta;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

// Aviso de urgência: aparece no painel do doador (HU10) e, se ele ativou, chega como notificação push (RNF02)
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
        // Só tenta o push para quem ativou em algum aparelho
        return $notifiable->pushSubscriptions()->exists()
            ? ['database', WebPushChannel::class]
            : ['database'];
    }

    // Conteúdo da notificação do celular; o service worker (public/sw.js) é quem a mostra
    public function toWebPush(object $notifiable, Notification $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title($this->alerta->hemocentro->nome.' precisa de sangue '.$this->alerta->tipo_sanguineo)
            ->body($this->alerta->mensagem)
            ->icon(asset('icons/icon-192.png'))
            ->badge(asset('icons/badge-96.png'))
            ->tag('alerta-'.$this->alerta->id)
            ->data(['url' => route('dashboard').'#alertas']);
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
