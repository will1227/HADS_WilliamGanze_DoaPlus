<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Doador; // <--- A importação está aqui!

class DoadorController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:doadores,email',
            'senha' => 'required|string|min:6',
            'cpf' => 'required|string|unique:doadores,cpf',
            'data_nascimento' => 'required|date',
            'sexo' => 'nullable|string|max:20',
            'peso' => 'required|numeric|min:50',
            'tipo_sanguineo' => 'required|string|max:3',
        ]);

        $validated['senha'] = Hash::make($validated['senha']);

        Doador::create($validated);

        return redirect()->route('doadores.index')->with('success', 'Doador cadastrado com sucesso!');
    }

    public function show(string $id)
    {
        $doador = Doador::with(['agendamentos.hemocentro', 'medalhas'])->findOrFail($id);
        return view('doadores.show', compact('doador'));
    }

    public function edit(string $id)
    {
        $doador = Doador::findOrFail($id);
        return view('doadores.edit', compact('doador'));
    }

    public function update(Request $request, string $id)
    {
        $doador = Doador::findOrFail($id);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'peso' => 'required|numeric|min:50',
            'email' => 'required|email|unique:doadores,email,' . $doador->id,
        ]);

        $doador->update($validated);

        return redirect()->route('doadores.show', $doador->id)->with('success', 'Dados atualizados com sucesso!');
    }

    public function destroy(string $id)
    {
        $doador = Doador::findOrFail($id);
        $doador->delete();

        return redirect()->route('doadores.index')->with('success', 'Conta de doador encerrada.');
    }
}