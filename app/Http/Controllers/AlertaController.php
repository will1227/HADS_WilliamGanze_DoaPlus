<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use App\Models\Hemocentro;
use Illuminate\Http\Request;

class AlertaController extends Controller
{
    public function index()
    {
        $alertas = Alerta::with('hemocentro')
            ->latest('data_publicacao')
            ->paginate(10);

        return view('alertas.index', compact('alertas'));
    }

    public function create()
    {
        $hemocentros = Hemocentro::where('status', 'ativo')->orderBy('nome')->get();

        return view('alertas.create', compact('hemocentros'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fk_hemocentro_id' => 'required|exists:hemocentros,id',
            'tipo_sanguineo' => 'required|string|max:3',
            'mensagem' => 'required|string|max:500',
        ]);

        $validated['data_publicacao'] = now();

        Alerta::create($validated);

        return redirect()->route('alertas.index')->with('success', 'Alerta disparado!');
    }

    public function show(string $id)
    {
        $alerta = Alerta::with('hemocentro')->findOrFail($id);

        return view('alertas.show', compact('alerta'));
    }

    public function edit(string $id)
    {
        $alerta = Alerta::findOrFail($id);
        $hemocentros = Hemocentro::where('status', 'ativo')->get();

        return view('alertas.edit', compact('alerta', 'hemocentros'));
    }

    public function update(Request $request, string $id)
    {
        $alerta = Alerta::findOrFail($id);

        $validated = $request->validate([
            'tipo_sanguineo' => 'required|string|max:3',
            'mensagem' => 'required|string|max:500',
        ]);

        $alerta->update($validated);

        return redirect()->route('alertas.index')->with('success', 'Alerta atualizado.');
    }

    public function destroy(string $id)
    {
        $alerta = Alerta::findOrFail($id);
        $alerta->delete();

        return redirect()->route('alertas.index')->with('success', 'Alerta removido.');
    }
}