@extends('layouts.painel')

@section('titulo', 'Meu painel')

@section('conteudo')
@php
    $doacoes = $doador->doacoes()->with('hemocentro')->orderByDesc('data_coleta')->get();
    $agendamento = $doador->agendamentosEmAberto()->with('hemocentro')->orderBy('data_hora')->first();
    $proximaDoacao = \Carbon\Carbon::parse($doador->data_proxima_doacao)->locale('pt_BR');
    $diasRestantes = (int) round(today()->diffInDays($proximaDoacao->copy()->startOfDay()));
@endphp

<div class="max-w-3xl">
    {{-- Alertas de urgência de hemocentros próximos (HU10) --}}
    @php($alertasNovos = $doador->alertasNovos())
    @if ($alertasNovos->isNotEmpty())
        <section id="alertas" class="mb-12 rounded-lg bg-sangue-claro px-5 py-5 sm:px-6" aria-labelledby="alertasTitulo">
            <div class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2">
                <h2 id="alertasTitulo" class="font-titulo text-[1.0625rem] font-bold text-sangue-escuro">
                    {{ $alertasNovos->count() === 1 ? 'Um hemocentro perto de você precisa do seu sangue' : 'Hemocentros perto de você precisam do seu sangue' }}
                </h2>
                <form method="POST" action="{{ route('alertas.lidos') }}">
                    @csrf
                    <button type="submit" class="botao-texto text-sm">Marcar como vistos</button>
                </form>
            </div>
            <ul class="mt-3 divide-y divide-sangue/15">
                @foreach ($alertasNovos as $notificacao)
                    <li class="grid grid-cols-[3.5rem_1fr] gap-x-4 py-4">
                        <span class="font-titulo text-3xl font-bold leading-none tracking-[-0.03em] text-sangue">{{ $notificacao->data['tipo_sanguineo'] }}</span>
                        <div>
                            <p class="font-bold leading-snug">{{ $notificacao->data['hemocentro'] }}</p>
                            <p class="mt-1 leading-snug">{{ $notificacao->data['mensagem'] }}</p>
                            <p class="mt-1 text-sm text-cinza">Vale até {{ \Illuminate\Support\Carbon::parse($notificacao->data['data_expiracao'])->format('d/m') }}.</p>
                            @if ($doador->pode_doar && ! $agendamento)
                                <a href="{{ route('agendamentos.create', ['hemocentro' => $notificacao->data['hemocentro_id']]) }}" class="botao-sangue botao-pequeno mt-3">Agendar neste hemocentro</a>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    {{-- A frase principal muda conforme a situação do doador, como o "km" da tela inicial --}}
    <section>
        <p class="text-cinza">Olá, {{ explode(' ', $doador->nome)[0] }}.</p>

        @if ($agendamento)
            @php($dataHora = $agendamento->data_hora->locale('pt_BR'))
            <h1 class="manchete mt-2">Sua doação é <span class="numero">{{ $dataHora->translatedFormat('j \d\e M') }}</span></h1>
            <p class="mt-4 max-w-[44ch] text-[1.0625rem] leading-normal text-cinza">
                Às <strong class="text-tinta">{{ $dataHora->format('H:i') }}</strong>, no {{ $agendamento->hemocentro->nome }}.
                {{ $agendamento->status === 'pendente' ? 'O hemocentro ainda vai confirmar o horário.' : 'O hemocentro já confirmou o horário.' }}
            </p>
            <div class="mt-5">
                <span @class(['selo-breve' => $agendamento->status === 'pendente', 'selo-ok' => $agendamento->status !== 'pendente'])>
                    {{ $agendamento->status === 'pendente' ? 'Aguardando confirmação' : 'Confirmado' }}
                </span>
            </div>
        @elseif ($doador->pode_doar)
            <h1 class="manchete mt-2">Você já pode doar.</h1>
            <p class="mt-4 max-w-[40ch] text-[1.0625rem] leading-normal text-cinza">Escolha um hemocentro e o melhor horário para você.</p>
            <div class="mt-7 flex flex-wrap items-center gap-x-5 gap-y-3">
                <a href="{{ route('agendamentos.create') }}" class="botao-sangue">Agendar doação</a>
                <a href="{{ route('home') }}" class="botao-texto">Ver hemocentros no mapa</a>
            </div>
        @elseif (! $doador->dentro_da_faixa_etaria)
            <h1 class="manchete mt-2">Doação indisponível pela idade.</h1>
            <p class="mt-4 max-w-[40ch] text-[1.0625rem] leading-normal text-cinza">A doação de sangue é permitida dos 16 aos 69 anos.</p>
        @else
            <h1 class="manchete mt-2">Faltam <span class="numero">{{ $diasRestantes }} {{ $diasRestantes === 1 ? 'dia' : 'dias' }}</span> para doar de novo.</h1>
            <p class="mt-4 max-w-[44ch] text-[1.0625rem] leading-normal text-cinza">
                Você pode agendar a partir de <strong class="text-tinta">{{ $proximaDoacao->translatedFormat('j \d\e F') }}</strong>.
                O intervalo entre doações dá tempo para o corpo repor o sangue.
            </p>
        @endif
    </section>

    @unless ($doador->temLocalizacao())
        <p class="aviso-ok mt-10 max-w-[60ch]">
            Quer saber quando um hemocentro perto de você precisar do seu tipo sanguíneo?
            <a href="{{ route('doador.perfil') }}#localizacao" class="font-bold underline underline-offset-4 hover:text-sangue">Informe sua localização</a>.
        </p>
    @endunless

    {{-- Números do doador --}}
    <dl class="mt-12 grid grid-cols-2 gap-x-6 border-b border-linha sm:grid-cols-4">
        <div class="border-t border-linha py-5">
            <dt class="text-sm text-cinza">Doações</dt>
            <dd class="mt-1 font-titulo text-3xl font-bold tabular-nums">{{ $doacoes->count() }}</dd>
        </div>
        <div class="border-t border-linha py-5">
            <dt class="text-sm text-cinza">Pessoas ajudadas</dt>
            <dd class="mt-1 font-titulo text-3xl font-bold tabular-nums">até {{ $doacoes->count() * 4 }}</dd>
        </div>
        <div class="border-t border-linha py-5">
            <dt class="text-sm text-cinza">Tipo sanguíneo</dt>
            <dd class="mt-1 font-titulo text-3xl font-bold">{{ $doador->tipo_sanguineo }}</dd>
        </div>
        <div class="border-t border-linha py-5">
            <dt class="text-sm text-cinza">Patente</dt>
            <dd class="mt-2"><span class="selo {{ $doador->patente['bg'] }} {{ $doador->patente['text'] }} font-bold">{{ $doador->patente['nome'] }}</span></dd>
        </div>
    </dl>

    {{-- Histórico, no mesmo desenho da lista "Onde doar" --}}
    <section class="mt-12">
        <div class="secao-cab">
            <h2>Suas doações</h2>
            @if ($doacoes->isNotEmpty())
                <span>{{ $doacoes->count() }} no total</span>
            @endif
        </div>

        <ul class="linhas">
            @forelse ($doacoes as $doacao)
                <li class="grid grid-cols-[14px_1fr_auto] items-start gap-x-3.5 py-4">
                    <x-application-logo class="mt-1 h-[15px] w-3 text-arterial" />
                    <span class="font-bold leading-snug">{{ $doacao->hemocentro->nome ?? 'Hemocentro' }}</span>
                    <span class="font-titulo font-bold tabular-nums">{{ $doacao->data_coleta->format('d/m/Y') }}</span>
                </li>
            @empty
                <li class="py-6 text-cinza">
                    Sua primeira doação aparece aqui depois que o hemocentro registrar a coleta.
                </li>
            @endforelse
        </ul>
    </section>
</div>
@endsection
