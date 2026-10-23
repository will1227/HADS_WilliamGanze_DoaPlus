<?php

namespace App\Http\Controllers;

use App\Models\Hemocentro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class HemocentroController extends Controller
{
    public function index()
    {
        $hemocentros = Hemocentro::paginate(10);
        return view('hemocentros.index', compact('hemocentros'));
    }

    public function create()
    {
        return view('hemocentros.create');
    }

    public function store(Request $request)
    {
        // 1. Limpa as máscaras de CNPJ e Telefone
        $request->merge([
            'cnpj' => preg_replace('/[^0-9]/', '', $request->cnpj),
            'telefone' => preg_replace('/[^0-9]/', '', $request->telefone)
        ]);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:hemocentros,email',
            'senha' => 'required|string|min:6',
            'cnpj' => 'required|string|size:14|unique:hemocentros,cnpj',
            'telefone' => 'required|string|max:20',
            'endereco' => 'required|string|max:255',
        ]);

        // 2. Criptografa a senha e define o status inicial
        $validated['senha'] = Hash::make($validated['senha']);
        $validated['status'] = 'pendente';

        Hemocentro::create($validated);

        // 3. Redireciona para o login com mensagem de sucesso
        return redirect()->route('login')->with('status', 'Instituição cadastrada com sucesso! Faça seu login para acompanhar a aprovação.');
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
            'telefone' => 'required|string|max:20',
            'endereco' => 'required|string|max:255',
            'status' => 'required|in:pendente,ativo',
        ]);

        $hemocentro->update($validated);

        return redirect()->route('hemocentros.show', $hemocentro->id)->with('success', 'Dados atualizados.');
    }

    public function destroy(string $id)
    {
        $hemocentro = Hemocentro::findOrFail($id);
        $hemocentro->delete();
        
        return redirect()->route('hemocentros.index')->with('success', 'Hemocentro removido.');
    }
    public function editProfile()
    {
        $hemocentro = Auth::guard('hemocentro')->user();
        return view('painel.hemocentro-edit', compact('hemocentro'));
    }

    public function updateProfile(Request $request)
    {
        /** @var \App\Models\Hemocentro $hemocentro */
        $hemocentro = Auth::guard('hemocentro')->user();

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'required|string|max:20',
            'endereco' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'email' => 'required|email|unique:hemocentros,email,' . $hemocentro->id,
            'senha' => 'nullable|string|min:6',
        ]);

        // Limpa a máscara do telefone
        $validated['telefone'] = preg_replace('/[^0-9]/', '', $validated['telefone']);

        if ($request->filled('senha')) {
            $validated['senha'] = Hash::make($validated['senha']);
        } else {
            unset($validated['senha']);
        }

        $hemocentro->update($validated);

        return redirect()->route('hemocentro.perfil')->with('status', 'Dados institucionais e localização no mapa atualizados com sucesso!');
    }

    // Recebe o ID do local provisório e salva o pedido
    public function reivindicar(Request $request)
    {
        $request->validate([
            'fk_reivindicacao_id' => 'required|exists:hemocentros,id'
        ]);

        /** @var \App\Models\Hemocentro $hemocentro */
        $hemocentro = Auth::guard('hemocentro')->user();
        
        $hemocentro->fk_reivindicacao_id = $request->fk_reivindicacao_id;
        $hemocentro->save();

        return redirect()->route('dashboard')->with('status', 'Pedido enviado! Aguarde a aprovação do administrador para unificar a sua conta com o mapa.');
    }
}
