<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use App\Models\Doador;
use App\Models\Hemocentro;
use Illuminate\Http\Request;

class AgendamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $agendamentos = Agendamento::with(['doador', 'hemocentro'])
            ->latest('data_hora')->paginate(15);

        return view('agendamentos.index', compact('agendamentos'));
    }

    public function create()
    {
        $doadores = Doador::orderBy('nome')->get();
        $hemocentros = Hemocentro::where('status', 'ativo')->orderBy('nome')->get();

        return view('agendamentos.create', compact('doadores', 'hemocentros'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fk_hemocentro_id' => 'required|exists:hemocentros,id',
            'data' => 'required|date_format:Y-m-d|after_or_equal:today',
            'hora' => 'required|date_format:H:i',
        ]);

        $doador = $request->user('doador');

        if (!$doador->pode_doar) {
            return redirect()->route('dashboard')->with('erro', 'Você ainda está no intervalo de descanso e não pode agendar.');
        }

        $dataHora = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $validated['data'].' '.$validated['hora']);

        if ($dataHora->isPast()) {
            return back()->withInput()->withErrors(['hora' => 'Escolha um horário futuro.']);
        }

        Agendamento::create([
            'fk_doador_id' => $doador->id,
            'fk_hemocentro_id' => $validated['fk_hemocentro_id'],
            'data_hora' => $dataHora,
            'status' => 'pendente',
        ]);

        return redirect()->route('dashboard')->with('status', 'Agendamento marcado com sucesso!');
    }

    public function show(string $id)
    {
        $agendamento = Agendamento::with(['doador', 'hemocentro'])->findOrFail($id);

        return view('agendamentos.show', compact('agendamento'));
    }

    public function edit(string $id)
    {
        $agendamento = Agendamento::findOrFail($id);
        $doadores = Doador::orderBy('nome')->get();
        $hemocentros = Hemocentro::where('status', 'ativo')->orderBy('nome')->get();

        return view('agendamentos.edit', compact('agendamento', 'doadores', 'hemocentros'));
    }

    public function update(Request $request, string $id)
    {
        $agendamento = Agendamento::findOrFail($id);

        $validated = $request->validate([
            'data_hora' => 'required|date',
            'status' => 'required|in:pendente,confirmado,cancelado',
        ]);

        $agendamento->update($validated);

        return redirect()->route('agendamentos.index')->with('success', 'Agendamento atualizado.');
    }

    public function atualizarStatus(Request $request, string $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:confirmado,cancelado,concluido',
        ]);

        // Só o hemocentro dono do agendamento pode alterá-lo
        $agendamento = Agendamento::where('fk_hemocentro_id', $request->user('hemocentro')->id)
            ->findOrFail($id);

        // Agendamentos já cancelados/concluídos não mudam mais (evita doação duplicada)
        if (!in_array($agendamento->status, ['pendente', 'confirmado'])) {
            return redirect()->route('dashboard')->with('status', 'Este agendamento já foi finalizado.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($agendamento, $validated) {
            $agendamento->update(['status' => $validated['status']]);

            // Doação concluída entra no histórico do doador (e reinicia o intervalo de descanso)
            if ($validated['status'] === 'concluido') {
                \App\Models\Doacao::create([
                    'fk_doador_id' => $agendamento->fk_doador_id,
                    'fk_hemocentro_id' => $agendamento->fk_hemocentro_id,
                    'data_coleta' => $agendamento->data_hora->toDateString(),
                    'status_validacao' => 'validada',
                ]);
            }
        });

        $mensagens = [
            'confirmado' => 'Agendamento confirmado!',
            'cancelado' => 'Agendamento cancelado.',
            'concluido' => 'Doação registrada com sucesso!',
        ];

        return redirect()->route('dashboard')->with('status', $mensagens[$validated['status']]);
    }

    public function destroy(string $id)
    {
        $agendamento = Agendamento::findOrFail($id);
        $agendamento->delete();

        return redirect()->route('agendamentos.index')->with('success', 'Agendamento cancelado.');
    }
}
