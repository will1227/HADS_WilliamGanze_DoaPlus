@extends('layouts.painel')

@section('titulo', 'Dados da instituição')

@section('conteudo')
<div class="max-w-2xl">
    <h1 class="manchete">Dados da instituição</h1>
    <p class="mt-4 max-w-[46ch] text-[1.0625rem] leading-normal text-cinza">O nome, o endereço e o telefone aparecem para os doadores no mapa.</p>

    <form method="POST" action="{{ route('hemocentro.perfil.update') }}" class="mt-10 grid gap-5 sm:grid-cols-2">
        @csrf
        @method('PUT')

        <div class="sm:col-span-2">
            <label for="nome" class="rotulo">Nome do hemocentro</label>
            <input id="nome" type="text" name="nome" value="{{ old('nome', $hemocentro->nome) }}" required class="campo">
            <x-input-error :messages="$errors->get('nome')" class="mt-2" />
        </div>

        <div>
            <label for="email" class="rotulo">E-mail institucional</label>
            <input id="email" type="email" name="email" value="{{ old('email', $hemocentro->email) }}" required autocomplete="email" class="campo">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <label for="telefone" class="rotulo">Telefone (WhatsApp)</label>
            <input id="telefone" type="tel" name="telefone" value="{{ old('telefone', $hemocentro->telefone) }}" required class="campo">
            <x-input-error :messages="$errors->get('telefone')" class="mt-2" />
        </div>

        <div class="sm:col-span-2">
            <label for="endereco" class="rotulo">Endereço</label>
            <input id="endereco" type="text" name="endereco" value="{{ old('endereco', $hemocentro->endereco) }}" required autocomplete="street-address" class="campo">
            <x-input-error :messages="$errors->get('endereco')" class="mt-2" />
        </div>

        <fieldset class="mt-4 grid gap-5 border-t border-linha pt-6 sm:col-span-2 sm:grid-cols-2">
            <legend class="float-left mb-1 w-full font-titulo text-[1.0625rem] font-bold sm:col-span-2">Posição no mapa</legend>
            <div>
                <label for="latitude" class="rotulo">Latitude</label>
                <input id="latitude" type="text" name="latitude" value="{{ old('latitude', $hemocentro->latitude) }}" required inputmode="decimal" class="campo">
                <x-input-error :messages="$errors->get('latitude')" class="mt-2" />
            </div>
            <div>
                <label for="longitude" class="rotulo">Longitude</label>
                <input id="longitude" type="text" name="longitude" value="{{ old('longitude', $hemocentro->longitude) }}" required inputmode="decimal" class="campo">
                <x-input-error :messages="$errors->get('longitude')" class="mt-2" />
            </div>
            <p class="ajuda mt-0 sm:col-span-2">No Google Maps, clique com o botão direito na sua unidade para copiar as coordenadas.</p>
        </fieldset>

        <div class="sm:col-span-2">
            <label for="senha" class="rotulo">Senha nova</label>
            <input id="senha" type="password" name="senha" autocomplete="new-password" aria-describedby="senhaAjuda" class="campo">
            <p id="senhaAjuda" class="ajuda">Deixe em branco para manter a atual.</p>
            <x-input-error :messages="$errors->get('senha')" class="mt-2" />
        </div>

        <fieldset class="mt-4 grid gap-5 border-t border-linha pt-6 sm:col-span-2 sm:grid-cols-2">
            <legend class="float-left mb-1 w-full font-titulo text-[1.0625rem] font-bold sm:col-span-2">Dados de registro</legend>
            <div>
                <label for="cnpj" class="rotulo">CNPJ</label>
                <input id="cnpj" type="text" value="{{ $hemocentro->cnpj }}" disabled class="campo">
            </div>
            <div>
                <label for="situacao" class="rotulo">Situação da conta</label>
                <input id="situacao" type="text" value="{{ $hemocentro->status === 'ativo' ? 'Aprovada' : 'Aguardando aprovação' }}" disabled class="campo">
            </div>
            <p class="ajuda mt-0 sm:col-span-2">Para corrigir o CNPJ, fale com a equipe DoaPlus.</p>
        </fieldset>

        <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-3 sm:col-span-2">
            <button type="submit" class="botao-sangue">Salvar dados</button>
            <a href="{{ route('dashboard') }}" class="botao-texto">Cancelar</a>
        </div>
    </form>
</div>
@endsection
