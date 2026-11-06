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
            // Idade legal para doar: 16 a 69 anos
            'data_nascimento' => 'required|date|before_or_equal:-16 years|after:-70 years',
            // Define o intervalo entre doações (60 dias / 4 por ano ou 90 dias / 3 por ano)
            'sexo' => 'required|in:Masculino,Feminino',
            'peso' => 'required|numeric|min:50',
            'tipo_sanguineo' => 'required|string|max:3',
        ], [
            'data_nascimento.before_or_equal' => 'É preciso ter pelo menos 16 anos para doar sangue.',
            'data_nascimento.after' => 'A idade máxima para doar sangue é 69 anos.',
            'sexo.required' => 'Informe o sexo: ele define o intervalo entre as doações.',
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
            // Localização para alertas regionais: opcional, mas latitude e longitude andam juntas
            'latitude' => 'nullable|required_with:longitude|numeric|between:-90,90',
            'longitude' => 'nullable|required_with:latitude|numeric|between:-180,180',
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

    // Tira os alertas de urgência da lista de novos do painel
    public function marcarAlertasLidos(Request $request)
    {
        $request->user('doador')->unreadNotifications->markAsRead();

        return redirect()->route('dashboard');
    }
}