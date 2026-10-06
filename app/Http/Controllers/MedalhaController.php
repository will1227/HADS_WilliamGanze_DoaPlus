<?php

namespace App\Http\Controllers;

use App\Models\Medalha;
use Illuminate\Http\Request;

class MedalhaController extends Controller
{
    public function index()
    {
        $medalhas = Medalha::paginate(12);

        return view('medalhas.index', compact('medalhas'));
    }

    public function create()
    {
        return view('medalhas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255|unique:medalhas,nome',
            'descricao' => 'required|string|max:255',
            'pontos_necessarios' => 'required|integer|min:1',
        ]);

        Medalha::create($validated);

        return redirect()->route('medalhas.index')->with('success', 'Nova medalha adicionada!');
    }

    public function show(string $id)
    {
        $medalha = Medalha::with('doadores')->findOrFail($id);

        return view('medalhas.show', compact('medalha'));
    }

    public function edit(string $id)
    {
        $medalha = Medalha::findOrFail($id);

        return view('medalhas.edit', compact('medalha'));
    }

    public function update(Request $request, string $id)
    {
        $medalha = Medalha::findOrFail($id);

        $validated = $request->validate([
            'nome' => 'required|string|max:255|unique:medalhas,nome,' . $medalha->id,
            'descricao' => 'required|string|max:255',
            'pontos_necessarios' => 'required|integer|min:1',
        ]);

        $medalha->update($validated);

        return redirect()->route('medalhas.index')->with('success', 'Medalha atualizada.');
    }

    public function destroy(string $id)
    {
        $medalha = Medalha::findOrFail($id);
        $medalha->delete();

        return redirect()->route('medalhas.index')->with('success', 'Medalha excluída.');
    }
}