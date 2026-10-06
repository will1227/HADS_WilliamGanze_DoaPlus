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
            'fk_doador_id' => 'required|exists:doadores,id',
            'fk_hemocentro_id' => 'required|exists:hemocentros,id',
            'data_hora' => 'required|date|after:today',
        ]);

        $validated['status'] = 'pendente';
        Agendamento::create($validated);

        return redirect()->route('agendamentos.index')->with('success', 'Agendamento marcado!');
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

    public function destroy(string $id)
    {
        $agendamento = Agendamento::findOrFail($id);
        $agendamento->delete();

        return redirect()->route('agendamentos.index')->with('success', 'Agendamento cancelado.');
    }
}
