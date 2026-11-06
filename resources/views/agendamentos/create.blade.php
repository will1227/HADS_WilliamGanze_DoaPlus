@extends('layouts.painel')

@section('titulo', 'Agendar doação')

@section('conteudo')
<div class="max-w-2xl">
    <h1 class="manchete">Agendar doação</h1>
    <p class="mt-4 max-w-[44ch] text-[1.0625rem] leading-normal text-cinza">O hemocentro confirma o horário. Leve um documento oficial com foto no dia.</p>

    <x-erros class="mt-6" />

    <form action="{{ route('agendamentos.store') }}" method="POST" class="mt-10">
        @csrf

        {{-- Escolha do local no mesmo desenho da lista "Onde doar" da tela inicial --}}
        @php($selecionado = old('fk_hemocentro_id', request('hemocentro')))
        <fieldset>
            <legend class="secao-cab w-full">
                <h2>Onde você vai doar</h2>
            </legend>
            <ul class="linhas">
                @forelse ($hemocentros as $local)
                    <li>
                        <label class="grid cursor-pointer grid-cols-[14px_1fr_auto] items-start gap-x-3.5 py-4 pl-1 pr-2 hover:bg-gaze has-[:checked]:bg-sangue-claro has-[:checked]:shadow-[inset_3px_0_0_#9B1027]">
                            <x-application-logo class="mt-1 h-[15px] w-3 text-arterial" />
                            <span>
                                <span class="block font-bold leading-snug">{{ $local->nome }}</span>
                                <span class="block text-sm leading-snug text-cinza">{{ $local->endereco }}</span>
                            </span>
                            <input type="radio" name="fk_hemocentro_id" value="{{ $local->id }}" required
                                   @checked($selecionado == $local->id)
                                   class="mt-1 border-borda text-sangue focus:ring-sangue">
                        </label>
                    </li>
                @empty
                    <li class="py-6 text-cinza">Nenhum hemocentro está recebendo agendamentos agora. Veja os pontos "Em breve" no mapa.</li>
                @endforelse
            </ul>
        </fieldset>

        <div class="mt-10 grid gap-5 sm:grid-cols-2">
            <div>
                <label for="data" class="rotulo">Dia</label>
                <input id="data" type="date" name="data" value="{{ old('data') }}" min="{{ date('Y-m-d') }}" required class="campo">
            </div>
            <div>
                <label for="hora" class="rotulo">Horário</label>
                <input id="hora" type="time" name="hora" value="{{ old('hora') }}" required class="campo">
            </div>
        </div>

        <div class="mt-8 flex flex-wrap items-center gap-x-5 gap-y-3">
            <button type="submit" class="botao-sangue">Agendar doação</button>
            <a href="{{ route('dashboard') }}" class="botao-texto">Cancelar</a>
        </div>
    </form>
</div>
@endsection
