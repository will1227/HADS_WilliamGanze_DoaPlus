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
</script>
@endsection
