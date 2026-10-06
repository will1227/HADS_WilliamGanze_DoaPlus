<?php

namespace App\Http\Controllers;

use App\Models\Hemocentro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class HemocentroController extends Controller
{
    public function index()
    {
        $hemocentros = Hemocentro::all();

        return view('hemocentros.index', compact('hemocentros'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:hemocentros,email',
            'senha' => 'required|string|min:6',
            'cnpj' => 'required|string|unique:hemocentros,cnpj',
            'telefone' => 'required|string|unique:hemocentros,telefone',
            'endereco' => 'required|string|max:255',
            'status' => 'nullable|in:pendente,ativo',
        ]);

        if (!isset($validated['status'])) {
            $validated['status'] = 'pendente';
        }

        $validated['senha'] = Hash::make($validated['senha']);

        Hemocentro::create($validated);

        return redirect()->route('hemocentros.index')->with('success', 'Hemocentro cadastrado. Aguardando ativação.');
    }

    public function show(string $id)
    {
        $hemocentro = Hemocentro::with(['alertas', 'agendamentos.doador'])->findOrFail($id);

        return view('hemocentros.show', compact('hemocentro'));
    }

    public function edit(string $id)
    {
        $hemocentro = Hemocentro::findOrFail($id);

        return view('hemocentros.edit', compact('hemocentro'));
    }

    public function update(Request $request, string $id)
    {
        $hemocentro = Hemocentro::findOrFail($id);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:hemocentros,email,' . $hemocentro->id,
            'senha' => 'sometimes|string|min:6',
            'telefone' => 'required|string|unique:hemocentros,telefone,' . $hemocentro->id,
            'endereco' => 'required|string|max:255',
            'status' => 'required|in:pendente,ativo',
        ]);

        if ($request->filled('senha')) {
            $validated['senha'] = Hash::make($validated['senha']);
        }

        $hemocentro->update($validated);

        return redirect()->route('hemocentros.show', $hemocentro->id)->with('success', 'Dados atualizados.');
    }

    public function destroy(string $id)
    {
        $hemocentro = Hemocentro::findOrFail($id);
        $hemocentro->delete();

        return redirect()->route('hemocentros.index')->with('success', 'Hemocentro removido.');
    }
}