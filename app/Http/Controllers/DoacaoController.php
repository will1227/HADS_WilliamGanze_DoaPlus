<?php

namespace App\Http\Controllers;

use App\Models\Doacao;
use App\Models\Doador;
use App\Models\Hemocentro;
use Illuminate\Http\Request;

class DoacaoController extends Controller
{
    public function index()
    {
        $doacoes = Doacao::with(['doador', 'hemocentro'])
            ->orderByDesc('data_coleta')
            ->paginate(15);

        return view('doacoes.index', compact('doacoes'));
    }

    public function create()
    {
        $doadores = Doador::orderBy('nome')->get();
        $hemocentros = Hemocentro::where('status', 'ativo')->orderBy('nome')->get();

        return view('doacoes.create', compact('doadores', 'hemocentros'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fk_doador_id' => 'required|exists:doadores,id',
            'fk_hemocentro_id' => 'required|exists:hemocentros,id',
            'data_coleta' => 'required|date|before_or_equal:today',
        ]);

        $validated['status_validacao'] = 'pendente';
        Doacao::create($validated);

        return redirect()->route('doacoes.index')->with('success', 'Registro inserido.');
    }

    public function show(string $id)
    {
        $doacao = Doacao::with(['doador', 'hemocentro'])->findOrFail($id);

        return view('doacoes.show', compact('doacao'));
    }

    public function edit(string $id)
    {
        $doacao = Doacao::findOrFail($id);

        return view('doacoes.edit', compact('doacao'));
    }

    public function update(Request $request, string $id)
    {
        $doacao = Doacao::findOrFail($id);

        $validated = $request->validate([
            'status_validacao' => 'required|in:pendente,validada,rejeitada',
        ]);

        $doacao->update($validated);

        return redirect()->route('doacoes.index')->with('success', 'Status atualizado.');
    }

    public function destroy(string $id)
    {
        $doacao = Doacao::findOrFail($id);
        $doacao->delete();

        return redirect()->route('doacoes.index')->with('success', 'Doação excluída.');
    }
}
