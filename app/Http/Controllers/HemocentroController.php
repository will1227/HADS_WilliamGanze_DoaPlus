<?php

namespace App\Http\Controllers;

use App\Models\Hemocentro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class HemocentroController extends Controller
{
    // Cadastra uma nova instituição vinda da tela pública de cadastro (RF06/HU02)
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
            // Único no banco: sem esta regra, um telefone repetido dava erro 500
            'telefone' => 'required|string|max:20|unique:hemocentros,telefone',
            'endereco' => 'required|string|max:255',
        ]);

        // 2. Criptografa a senha. A conta nasce 'incompleto': só entra na fila do admin
        // depois que a instituição escolhe um ponto pré-cadastrado ou informa a localização
        $validated['senha'] = Hash::make($validated['senha']);
        $validated['status'] = 'incompleto';

        Hemocentro::create($validated);

        // 3. Redireciona para o login com mensagem de sucesso
        return redirect()->route('login')->with('status', 'Instituição cadastrada com sucesso! Faça seu login e informe onde fica a sua unidade para enviar à aprovação.');
    }

    // Exibe a tela de edição do perfil da instituição logada (RF06/HU02)
    public function editProfile()
    {
        $hemocentro = Auth::guard('hemocentro')->user();
        return view('painel.hemocentro-edit', compact('hemocentro'));
    }

    // Salva os dados da instituição, inclusive a localização no mapa (RF06/HU02)
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\Hemocentro $hemocentro */
        $hemocentro = Auth::guard('hemocentro')->user();

        // Limpa a máscara antes de validar, para comparar com o telefone gravado (só números)
        $request->merge(['telefone' => preg_replace('/[^0-9]/', '', (string) $request->telefone)]);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'required|string|max:20|unique:hemocentros,telefone,' . $hemocentro->id,
            'endereco' => 'required|string|max:255',
            'latitude' => 'nullable|required_with:longitude|numeric|between:-90,90',
            'longitude' => 'nullable|required_with:latitude|numeric|between:-180,180',
            'email' => 'required|email|unique:hemocentros,email,' . $hemocentro->id,
            'senha' => 'nullable|string|min:6',
        ]);

        if ($request->filled('senha')) {
            $validated['senha'] = Hash::make($validated['senha']);
        } else {
            unset($validated['senha']);
        }

        $hemocentro->update($validated);

        return redirect()->route('hemocentro.perfil')->with('status', 'Dados institucionais e localização no mapa atualizados com sucesso!');
    }

    // Etapa 1 do cadastro, opção A: assumir um ponto pré-cadastrado. Envia para a aprovação do admin. (RF06/HU02)
    public function reivindicar(Request $request)
    {
        $request->validate([
            'fk_reivindicacao_id' => [
                'required',
                Rule::exists('hemocentros', 'id')->where('status', 'provisorio'),
            ],
        ]);

        /** @var \App\Models\Hemocentro $hemocentro */
        $hemocentro = Auth::guard('hemocentro')->user();

        if (! $hemocentro->precisaEscolherLocal()) {
            return redirect()->route('dashboard');
        }

        $hemocentro->fk_reivindicacao_id = $request->fk_reivindicacao_id;
        $hemocentro->status = 'pendente';
        $hemocentro->save();

        return redirect()->route('dashboard')->with('status', 'Pedido enviado! Aguarde a aprovação do administrador para unificar a sua conta com o mapa.');
    }

    // Etapa 1 do cadastro, opção B: unidade fora da lista, informa as coordenadas. Envia para a aprovação do admin. (RF06/HU02)
    public function informarLocalizacao(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        /** @var \App\Models\Hemocentro $hemocentro */
        $hemocentro = Auth::guard('hemocentro')->user();

        if (! $hemocentro->precisaEscolherLocal()) {
            return redirect()->route('dashboard');
        }

        $hemocentro->fill($validated);
        $hemocentro->fk_reivindicacao_id = null;
        $hemocentro->status = 'pendente';
        $hemocentro->save();

        return redirect()->route('dashboard')->with('status', 'Localização enviada! Aguarde a aprovação do administrador para a sua unidade aparecer no mapa.');
    }

    // Desiste do pedido em análise e volta para a etapa de escolher o local (RF06/HU02)
    public function cancelarPedido()
    {
        /** @var \App\Models\Hemocentro $hemocentro */
        $hemocentro = Auth::guard('hemocentro')->user();

        if ($hemocentro->status !== 'pendente') {
            return redirect()->route('dashboard');
        }

        $hemocentro->fk_reivindicacao_id = null;
        $hemocentro->status = 'incompleto';
        $hemocentro->save();

        return redirect()->route('dashboard')->with('status', 'Pedido cancelado. Escolha novamente onde fica a sua unidade.');
    }
}
