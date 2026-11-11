<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use App\Models\Doador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Importação necessária para a senha
use Illuminate\Support\Facades\Auth; // Importação necessária para saber quem está logado
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
            'tipo_sanguineo' => ['required', Rule::in(Alerta::TIPOS_SANGUINEOS)],
            // LGPD (RNF06): consentimento explícito com a Política de Privacidade
            'consentimento' => 'accepted',
        ], [
            'data_nascimento.before_or_equal' => 'É preciso ter pelo menos 16 anos para doar sangue.',
            'data_nascimento.after' => 'A idade máxima para doar sangue é 69 anos.',
            'sexo.required' => 'Informe o sexo: ele define o intervalo entre as doações.',
            'tipo_sanguineo.in' => 'Escolha um tipo sanguíneo da lista.',
            'consentimento.accepted' => 'Para criar a conta, aceite a Política de Privacidade.',
        ]);

        // 2. Criptografa a senha e guarda quando o consentimento foi dado (prova exigida pela LGPD)
        $validated['senha'] = Hash::make($validated['senha']);
        $validated['consentimento_em'] = now();
        unset($validated['consentimento']);

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

    // LGPD (RNF06): o doador apaga a própria conta e os dados ligados a ela.
    // Doações, agendamentos e medalhas saem junto pelo cascadeOnDelete das chaves estrangeiras;
    // notificações e inscrições de push usam relação polimórfica (sem chave estrangeira) e são apagadas aqui.
    public function excluirConta(Request $request)
    {
        $request->validate([
            'senha_atual' => 'required|current_password:doador',
        ], [
            'senha_atual.required' => 'Digite sua senha para confirmar.',
            'senha_atual.current_password' => 'Senha incorreta.',
        ]);

        /** @var \App\Models\Doador $doador */
        $doador = $request->user('doador');

        DB::transaction(function () use ($doador) {
            $doador->notifications()->delete();
            $doador->pushSubscriptions()->delete();
            $doador->delete();
        });

        Auth::guard('doador')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    // Tira os alertas de urgência da lista de novos do painel
    public function marcarAlertasLidos(Request $request)
    {
        $request->user('doador')->unreadNotifications->markAsRead();

        return redirect()->route('dashboard');
    }

    // Serviços de push dos navegadores (Chrome/Edge/Brave, Firefox, Safari, Windows).
    // O servidor faz requisições para o endpoint, então só aceitamos esses domínios.
    private const SERVICOS_PUSH = [
        'fcm.googleapis.com', 'push.services.mozilla.com', 'push.apple.com', 'notify.windows.com',
    ];

    // Grava a inscrição de push deste navegador (RNF02). Cada aparelho tem a sua.
    public function salvarInscricaoPush(Request $request)
    {
        $validated = $request->validate([
            'endpoint' => ['required', 'url:https', 'max:1024', $this->servicoPushConhecido(...)],
            // Formato exato que o navegador gera (base64url): chave pública de 65 bytes e segredo de 16.
            // Uma chave inválida faria o envio dar erro e travar a publicação do alerta para todos.
            'keys.p256dh' => ['required', 'string', 'regex:/^[A-Za-z0-9_-]{87}=?$/'],
            'keys.auth' => ['required', 'string', 'regex:/^[A-Za-z0-9_-]{22}(==)?$/'],
        ]);

        $request->user('doador')->updatePushSubscription(
            $validated['endpoint'], $validated['keys']['p256dh'], $validated['keys']['auth']
        );

        return response()->noContent();
    }

    public function removerInscricaoPush(Request $request)
    {
        $validated = $request->validate(['endpoint' => 'required|string|max:1024']);

        $request->user('doador')->deletePushSubscription($validated['endpoint']);

        return response()->noContent();
    }

    private function servicoPushConhecido(string $atributo, mixed $valor, \Closure $falhar): void
    {
        $host = parse_url((string) $valor, PHP_URL_HOST) ?: '';

        $conhecido = collect(self::SERVICOS_PUSH)
            ->contains(fn ($dominio) => $host === $dominio || str_ends_with($host, '.'.$dominio));

        if (! $conhecido) {
            $falhar('Serviço de notificação não reconhecido.');
        }
    }
}