<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use App\Notifications\AlertaUrgente;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;

// Mural de urgências: o hemocentro publica e encerra alertas (HU09) e os doadores próximos são avisados (HU10)
class AlertaController extends Controller
{
    public function store(Request $request)
    {
        /** @var \App\Models\Hemocentro $hemocentro */
        $hemocentro = $request->user('hemocentro');

        // Só instituições aprovadas publicam no mural
        if ($hemocentro->status !== 'ativo') {
            return redirect()->route('dashboard')->with('erro', 'Sua instituição precisa ser aprovada antes de publicar alertas.');
        }

        $validated = $request->validate([
            'tipo_sanguineo' => ['required', Rule::in(Alerta::TIPOS_SANGUINEOS)],
            'mensagem' => 'required|string|max:500',
            'data_expiracao' => 'required|date_format:Y-m-d|after_or_equal:today|before_or_equal:+30 days',
        ], [
            'tipo_sanguineo.required' => 'Escolha o tipo sanguíneo em falta.',
            'tipo_sanguineo.in' => 'Escolha um tipo sanguíneo da lista.',
            'data_expiracao.after_or_equal' => 'O alerta precisa valer pelo menos até hoje.',
            'data_expiracao.before_or_equal' => 'Um alerta pode valer no máximo 30 dias. Publique outro depois, se precisar.',
        ]);

        $alerta = $hemocentro->alertas()->create([
            'tipo_sanguineo' => $validated['tipo_sanguineo'],
            'mensagem' => $validated['mensagem'],
            'data_publicacao' => now(),
            'data_expiracao' => Carbon::createFromFormat('Y-m-d', $validated['data_expiracao'])->endOfDay(),
        ]);

        $destinatarios = $alerta->destinatarios();
        Notification::send($destinatarios, new AlertaUrgente($alerta));

        $avisados = $destinatarios->count() === 1
            ? '1 doador próximo foi avisado.'
            : "{$destinatarios->count()} doadores próximos foram avisados.";

        return redirect()->route('dashboard')->with('status', "Alerta de {$alerta->tipo_sanguineo} publicado no mural. {$avisados}");
    }

    public function encerrar(Request $request, string $id)
    {
        // Só o hemocentro dono do alerta pode encerrá-lo
        $alerta = Alerta::where('fk_hemocentro_id', $request->user('hemocentro')->id)
            ->ativos()
            ->findOrFail($id);

        $alerta->update(['data_expiracao' => now()]);

        return redirect()->route('dashboard')->with('status', "Alerta de {$alerta->tipo_sanguineo} encerrado e retirado do mural.");
    }
}
