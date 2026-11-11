@extends('layouts.painel')

@section('titulo', 'Meus dados')

@section('conteudo')
<div class="max-w-2xl">
    <h1 class="manchete">Meus dados</h1>

    <form method="POST" action="{{ route('doador.perfil.update') }}" class="mt-10 grid gap-5 sm:grid-cols-2">
        @csrf
        @method('PUT')

        <div class="sm:col-span-2">
            <label for="nome" class="rotulo">Nome completo</label>
            <input id="nome" type="text" name="nome" value="{{ old('nome', $doador->nome) }}" required autocomplete="name" class="campo">
            <x-input-error :messages="$errors->get('nome')" class="mt-2" />
        </div>

        <div class="sm:col-span-2">
            <label for="email" class="rotulo">E-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email', $doador->email) }}" required autocomplete="email" class="campo">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <label for="peso" class="rotulo">Peso (kg)</label>
            <input id="peso" type="number" step="0.1" name="peso" value="{{ old('peso', $doador->peso) }}" required min="50" inputmode="decimal" aria-describedby="pesoAjuda" class="campo">
            <p id="pesoAjuda" class="ajuda">O mínimo para doar é 50 kg.</p>
            <x-input-error :messages="$errors->get('peso')" class="mt-2" />
        </div>

        <div>
            <label for="senha" class="rotulo">Senha nova</label>
            <input id="senha" type="password" name="senha" autocomplete="new-password" aria-describedby="senhaAjuda" class="campo">
            <p id="senhaAjuda" class="ajuda">Deixe em branco para manter a atual.</p>
            <x-input-error :messages="$errors->get('senha')" class="mt-2" />
        </div>

        {{-- Localização para os alertas regionais (HU10): opcional, pega pelo navegador --}}
        <fieldset id="localizacao" class="mt-4 border-t border-linha pt-6 sm:col-span-2">
            <legend class="float-left mb-1 w-full font-titulo text-[1.0625rem] font-bold">Alertas de urgência perto de você</legend>
            <p class="clear-left max-w-[60ch] text-cinza">
                Com sua localização, avisamos quando um hemocentro a até {{ \App\Models\Alerta::RAIO_KM }} km precisar do seu tipo sanguíneo.
                Ela não aparece para ninguém e você pode remover quando quiser.
            </p>

            <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $doador->latitude) }}">
            <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $doador->longitude) }}">

            <p id="localizacaoStatus" class="mt-4 font-bold" aria-live="polite">
                {{ $doador->temLocalizacao() ? 'Localização informada. Você recebe alertas da sua região.' : 'Localização não informada. Você não recebe alertas.' }}
            </p>
            <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-2">
                <button type="button" id="btnLocalizacao" class="botao-contorno botao-pequeno">
                    {{ $doador->temLocalizacao() ? 'Atualizar com minha localização atual' : 'Usar minha localização' }}
                </button>
                <button type="button" id="btnRemoverLocalizacao" @class(['botao-texto text-sm', 'hidden' => ! $doador->temLocalizacao()])>Remover localização</button>
            </div>
            <x-input-error :messages="$errors->get('latitude')" class="mt-2" />

            {{-- Notificação push (RNF02): vale para este navegador e é gravada na hora, sem "Salvar dados" --}}
            @if (config('webpush.vapid.public_key'))
                <div id="push" data-chave="{{ config('webpush.vapid.public_key') }}" class="mt-6">
                    <p class="rotulo">Avisos neste aparelho</p>
                    <p id="pushStatus" class="ajuda max-w-[60ch]" aria-live="polite">Verificando se este navegador recebe notificações...</p>
                    <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-2">
                        <button type="button" id="btnAtivarPush" class="botao-contorno botao-pequeno hidden">Receber alertas como notificação</button>
                        <button type="button" id="btnDesativarPush" class="botao-texto hidden text-sm">Parar de receber neste aparelho</button>
                    </div>
                </div>
            @endif
        </fieldset>

        {{-- Dados de registro: não podem ser alterados pelo doador --}}
        <fieldset class="mt-4 grid gap-5 border-t border-linha pt-6 sm:col-span-2 sm:grid-cols-3">
            <legend class="float-left mb-1 w-full font-titulo text-[1.0625rem] font-bold sm:col-span-3">Dados de registro</legend>
            <div>
                <label for="cpf" class="rotulo">CPF</label>
                <input id="cpf" type="text" value="{{ $doador->cpf }}" disabled class="campo">
            </div>
            <div>
                <label for="nascimento" class="rotulo">Nascimento</label>
                <input id="nascimento" type="date" value="{{ $doador->data_nascimento }}" disabled class="campo">
            </div>
            <div>
                <label for="tipo" class="rotulo">Tipo sanguíneo</label>
                <input id="tipo" type="text" value="{{ $doador->tipo_sanguineo }}" disabled class="campo">
            </div>
            <p class="ajuda mt-0 sm:col-span-3">Para corrigir estes dados, fale com a equipe DoaPlus.</p>
        </fieldset>

        <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-3 sm:col-span-2">
            <button type="submit" class="botao-sangue">Salvar dados</button>
            <a href="{{ route('dashboard') }}" class="botao-texto">Cancelar</a>
        </div>
    </form>

    {{-- LGPD (RNF06): exclusão da conta. Formulário separado porque um form não pode ficar dentro de outro --}}
    <section id="excluir" class="mt-16 border-t border-linha pt-6" aria-labelledby="excluirTitulo">
        <h2 id="excluirTitulo" class="font-titulo text-[1.0625rem] font-bold">Excluir minha conta</h2>
        <p class="mt-1 max-w-[60ch] text-cinza">
            Apaga para sempre seus dados, seu histórico de doações, agendamentos, medalhas e avisos.
            Não dá para desfazer. Os registros oficiais das doações continuam com cada hemocentro.
        </p>
        <form method="POST" action="{{ route('doador.excluir') }}" class="mt-4 max-w-sm"
              onsubmit="return confirm('Excluir sua conta e todos os seus dados? Não dá para desfazer.')">
            @csrf
            @method('DELETE')
            <label for="senha_atual" class="rotulo">Digite sua senha para confirmar</label>
            <input id="senha_atual" type="password" name="senha_atual" required autocomplete="current-password" class="campo">
            <x-input-error :messages="$errors->get('senha_atual')" class="mt-2" />
            <button type="submit" class="botao mt-4 border border-sangue bg-white text-sangue hover:bg-sangue-claro">Excluir minha conta</button>
        </form>
    </section>
</div>

<script>
    // Preenche latitude/longitude com o GPS do navegador; a localização só é gravada ao salvar o formulário
    (function () {
        const lat = document.getElementById('latitude');
        const lng = document.getElementById('longitude');
        const status = document.getElementById('localizacaoStatus');
        const remover = document.getElementById('btnRemoverLocalizacao');

        document.getElementById('btnLocalizacao').addEventListener('click', function () {
            if (!('geolocation' in navigator) || !window.isSecureContext) {
                status.textContent = 'Este navegador não informa a localização. Use HTTPS ou outro navegador.';
                return;
            }
            status.textContent = 'Procurando sua localização...';
            navigator.geolocation.getCurrentPosition(function (pos) {
                lat.value = pos.coords.latitude.toFixed(6);
                lng.value = pos.coords.longitude.toFixed(6);
                remover.classList.remove('hidden');
                status.textContent = 'Localização encontrada. Clique em "Salvar dados" para confirmar.';
            }, function (erro) {
                status.textContent = erro.code === 1
                    ? 'Você não liberou a localização. Libere nas configurações do navegador e tente de novo.'
                    : 'Não conseguimos achar sua localização. Tente de novo em alguns segundos.';
            }, { enableHighAccuracy: false, timeout: 10000 });
        });

        remover.addEventListener('click', function () {
            lat.value = '';
            lng.value = '';
            remover.classList.add('hidden');
            status.textContent = 'Localização removida. Clique em "Salvar dados" para confirmar.';
        });
    })();

    // Liga/desliga a notificação push deste navegador (RNF02)
    (function () {
        const area = document.getElementById('push');
        if (!area) return;

        const status = document.getElementById('pushStatus');
        const ativar = document.getElementById('btnAtivarPush');
        const desativar = document.getElementById('btnDesativarPush');
        const suportado = 'serviceWorker' in navigator && 'PushManager' in window && window.isSecureContext;

        function enviar(metodo, corpo) {
            return fetch('{{ route('push.inscrever') }}', {
                method: metodo,
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify(corpo),
            });
        }

        function mostrar(texto, botao) {
            status.textContent = texto;
            ativar.classList.toggle('hidden', botao !== ativar);
            desativar.classList.toggle('hidden', botao !== desativar);
        }

        async function atualizar() {
            if (!suportado) {
                return mostrar('Este navegador não recebe notificações. No iPhone, primeiro adicione o DoaPlus à Tela de Início (Compartilhar > Adicionar à Tela de Início).', null);
            }
            if (Notification.permission === 'denied') {
                return mostrar('As notificações do DoaPlus estão bloqueadas. Libere nas configurações do navegador.', null);
            }
            const registro = await navigator.serviceWorker.ready;
            const inscricao = await registro.pushManager.getSubscription();
            if (inscricao) {
                // Reenvia para o servidor: se outra conta usou este aparelho antes, a inscrição passa para você
                await enviar('POST', inscricao.toJSON());
                return mostrar('Ativado. Os alertas de urgência chegam como notificação, mesmo com o DoaPlus fechado.', desativar);
            }
            mostrar('Desativado. Os alertas aparecem só no seu painel.', ativar);
        }

        // Se der erro, o mesmo botão continua na tela para tentar de novo
        const falhou = (botao) => () => mostrar('Não foi possível mudar as notificações agora. Confira a internet e tente de novo.', botao);

        ativar.addEventListener('click', () => ligar().catch(falhou(ativar)));
        desativar.addEventListener('click', () => desligar().catch(falhou(desativar)));

        async function ligar() {
            if (await Notification.requestPermission() !== 'granted') {
                return atualizar();
            }
            const registro = await navigator.serviceWorker.ready;
            const inscricao = await registro.pushManager.subscribe({ userVisibleOnly: true, applicationServerKey: area.dataset.chave });
            const resposta = await enviar('POST', inscricao.toJSON());
            if (!resposta.ok) {
                await inscricao.unsubscribe();
                return mostrar('Não foi possível ativar agora. Tente de novo em alguns minutos.', ativar);
            }
            atualizar();
        }

        async function desligar() {
            const registro = await navigator.serviceWorker.ready;
            const inscricao = await registro.pushManager.getSubscription();
            if (inscricao) {
                await enviar('DELETE', { endpoint: inscricao.endpoint });
                await inscricao.unsubscribe();
            }
            atualizar();
        }

        atualizar().catch(() => mostrar('Não foi possível verificar as notificações neste navegador.', null));
    })();
</script>
@endsection
