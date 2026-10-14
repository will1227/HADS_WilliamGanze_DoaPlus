<?php

namespace App\Http\Controllers;

use App\Models\Doador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Importação necessária para a senha
use Illuminate\Support\Facades\Auth; // Importação necessária para saber quem está logado

class DoadorController extends Controller
{
    public function index()
    {
        $doadores = Doador::paginate(10);
        return view('doadores.index', compact('doadores'));
    }

    public function create()
    {
        return view('doadores.create');
    }

    public function store(Request $request)
    {
        // 1. Limpa a máscara do CPF (deixa apenas números)
        $request->merge([
            'cpf' => preg_replace('/[^0-9]/', '', $request->cpf)
        ]);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:doadores,email',
            'senha' => 'required|string|min:6',
            'cpf' => 'required|string|size:11|unique:doadores,cpf',
            'data_nascimento' => 'required|date',
            'sexo' => 'nullable|string|max:20',
            'peso' => 'required|numeric|min:50',
            'tipo_sanguineo' => 'required|string|max:3',
        ]);

        // 2. Criptografa a senha antes de salvar
        $validated['senha'] = Hash::make($validated['senha']);

        Doador::create($validated);

        // 3. Redireciona para o login com mensagem de sucesso
        return redirect()->route('login')->with('status', 'Conta de Doador criada com sucesso! Faça seu login.');
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

    // Exibe a tela de edição do próprio doador logado
    public function editProfile()
    {
        $doador = Auth::guard('doador')->user();
        return view('painel.doador-edit', compact('doador'));
    }

    // Processa a atualização dos dados
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\Doador $doador */
        $doador = Auth::guard('doador')->user();

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:doadores,email,' . $doador->id,
            'peso' => 'required|numeric|min:50',
            'senha' => 'nullable|string|min:6', // Senha é opcional na edição
        ]);

        // Se ele digitou uma senha nova, criptografa. Se não, remove do array para não apagar a antiga.
        if ($request->filled('senha')) {
            $validated['senha'] = Hash::make($validated['senha']);
        } else {
            unset($validated['senha']);
        }

        $doador->update($validated);

        return redirect()->route('doador.perfil')->with('status', 'Seus dados foram atualizados com sucesso!');
    }
}