@extends('layouts.painel')

@section('titulo', 'Agenda')

@section('conteudo')
<p class="text-cinza">{{ $hemocentro->nome }}</p>

@if ($hemocentro->status === 'pendente')

    @if ($hemocentro->fk_reivindicacao_id)
        <section class="max-w-3xl">
            <h1 class="manchete mt-2">Sua conta está em análise.</h1>
            <p class="mt-4 max-w-[46ch] text-[1.0625rem] leading-normal text-cinza">
                Você pediu para assumir o ponto <strong class="text-tinta">{{ $hemocentro->localReivindicado->nome ?? 'no mapa' }}</strong>.
                A equipe DoaPlus confere o CNPJ e, quando aprovar, a agenda da sua unidade é liberada.
            </p>
        </section>
    @else
        <section class="max-w-3xl">
            <h1 class="manchete mt-2">Encontre sua unidade no mapa.</h1>
            <p class="mt-4 max-w-[46ch] text-[1.0625rem] leading-normal text-cinza">
                Se ela já foi pré-cadastrada pela equipe DoaPlus, assuma o ponto abaixo. A agenda é liberada depois da aprovação.
            </p>

            <div class="secao-cab mt-12">
                <h2>Pontos pré-cadastrados</h2>
                <span>{{ $locaisProvisorios->count() }} {{ $locaisProvisorios->count() === 1 ? 'ponto' : 'pontos' }}</span>
            </div>
            <ul class="linhas">
                @forelse ($locaisProvisorios as $local)
                    <li class="grid grid-cols-[14px_1fr] gap-x-3.5 gap-y-3 py-4 sm:grid-cols-[14px_1fr_auto] sm:items-center">
                        <x-application-logo class="mt-1 h-[15px] w-3 self-start text-plasma" />
                        <div>
                            <p class="font-bold leading-snug">{{ $local->nome }}</p>
                            <p class="text-sm leading-snug text-cinza">{{ $local->endereco }}</p>
                        </div>
                        <form action="{{ route('hemocentro.reivindicar') }}" method="POST" class="col-start-2 sm:col-start-3">
                            @csrf
                            <input type="hidden" name="fk_reivindicacao_id" value="{{ $local->id }}">
                            <button type="submit" class="botao-contorno botao-pequeno">Assumir este ponto</button>
                        </form>
                    </li>
                @empty
                    <li class="py-6 text-cinza">Nenhum ponto pré-cadastrado no momento.</li>
                @endforelse
            </ul>

            <p class="mt-6 max-w-[60ch] text-cinza">
                Sua unidade não está na lista? Ela já está na fila de aprovação como um local novo.
                Informe a localização em <a href="{{ route('hemocentro.perfil') }}" class="text-tinta underline underline-offset-4 hover:text-sangue">Dados da instituição</a> para ela aparecer no mapa depois da aprovação.
            </p>
        </section>
    @endif

@else
    @php
        $pendentes = $agendamentos->where('status', 'pendente');
        $confirmados = $agendamentos->where('status', 'confirmado');
    @endphp

    {{-- A frase principal mostra o que precisa de atenção agora --}}
    <section class="max-w-3xl">
        @if ($pendentes->isNotEmpty())
            <h1 class="manchete mt-2"><span class="numero">{{ $pendentes->count() }}</span> {{ $pendentes->count() === 1 ? 'pedido para confirmar' : 'pedidos para confirmar' }}</h1>
        @else
            <h1 class="manchete mt-2">Agenda em dia.</h1>
        @endif
        <p class="mt-4 max-w-[46ch] text-[1.0625rem] leading-normal text-cinza">
            @if ($confirmados->isEmpty())
                Nenhuma doação confirmada por enquanto.
            @else
                {{ $confirmados->count() }} {{ $confirmados->count() === 1 ? 'doação confirmada aguarda' : 'doações confirmadas aguardam' }} o doador no hemocentro.
            @endif
        </p>
    </section>

    <div class="mt-12 grid gap-12 lg:grid-cols-2">
        <section>
            <div class="secao-cab">
                <h2>Pedidos novos</h2>
                <span>{{ $pendentes->count() }}</span>
            </div>
            <ul class="linhas">
                @forelse ($pendentes as $agendamento)
                    <li class="py-4">
                        <div class="grid grid-cols-[14px_1fr_auto] items-start gap-x-3.5">
                            <x-application-logo class="mt-1 h-[15px] w-3 text-plasma" />
                            <div>
                                <p class="font-bold leading-snug">{{ $agendamento->doador->nome }}</p>
                                <p class="text-sm text-cinza">Tipo sanguíneo {{ $agendamento->doador->tipo_sanguineo }}</p>
                            </div>
                            <p class="text-right font-titulo font-bold tabular-nums leading-snug">
                                {{ $agendamento->data_hora->format('d/m') }}<br>
                                <span class="font-normal text-cinza">{{ $agendamento->data_hora->format('H:i') }}</span>
                            </p>
                        </div>
                        <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-2 pl-[1.75rem]">
                            <form action="{{ route('agendamentos.status', $agendamento->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="confirmado">
                                <button type="submit" class="botao-sangue botao-pequeno">Confirmar horário</button>
                            </form>
                            <form action="{{ route('agendamentos.status', $agendamento->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="cancelado">
                                <button type="submit" class="botao-texto text-sm">Recusar</button>
                            </form>
                        </div>
                    </li>
                @empty
                    <li class="py-6 text-cinza">Nenhum pedido novo. Eles aparecem aqui quando um doador agenda na sua unidade.</li>
                @endforelse
            </ul>
        </section>

        <section>
            <div class="secao-cab">
                <h2>Confirmados</h2>
                <span>{{ $confirmados->count() }}</span>
            </div>
            <ul class="linhas">
                @forelse ($confirmados as $agendamento)
                    <li class="py-4">
                        <div class="grid grid-cols-[14px_1fr_auto] items-start gap-x-3.5">
                            <x-application-logo class="mt-1 h-[15px] w-3 text-arterial" />
                            <div>
                                <p class="font-bold leading-snug">{{ $agendamento->doador->nome }}</p>
                                <p class="text-sm text-cinza">Tipo sanguíneo {{ $agendamento->doador->tipo_sanguineo }}, sexo {{ Str::lower($agendamento->doador->sexo ?? 'não informado') }}</p>
                            </div>
                            <p class="text-right font-titulo font-bold tabular-nums leading-snug">
                                {{ $agendamento->data_hora->format('d/m') }}<br>
                                <span class="font-normal text-cinza">{{ $agendamento->data_hora->format('H:i') }}</span>
                            </p>
                        </div>
                        <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-2 pl-[1.75rem]">
                            {{-- A coleta só pode ser registrada a partir do dia agendado --}}
                            @if ($agendamento->data_hora->isAfter(today()->endOfDay()))
                                <p class="text-sm text-cinza">Registro da doação liberado no dia {{ $agendamento->data_hora->format('d/m') }}.</p>
                            @else
                                <form action="{{ route('agendamentos.status', $agendamento->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="concluido">
                                    <button type="submit" class="botao-sangue botao-pequeno">Registrar doação</button>
                                </form>
                            @endif
                            <form action="{{ route('agendamentos.status', $agendamento->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="cancelado">
                                <button type="submit" class="botao-texto text-sm">Não compareceu</button>
                            </form>
                        </div>
                    </li>
                @empty
                    <li class="py-6 text-cinza">Nenhuma doação confirmada. Confirme um pedido novo para ele aparecer aqui.</li>
                @endforelse
            </ul>
        </section>
    </div>

    {{-- Mural de urgências (HU09): publicar e encerrar alertas de estoque --}}
    <div id="alertas" class="mt-16 grid gap-12 lg:grid-cols-2">
        <section>
            <div class="secao-cab">
                <h2>Publicar alerta de urgência</h2>
            </div>
            <p class="mt-4 max-w-[52ch] text-cinza">
                O alerta aparece no mural da tela inicial e avisa no painel os doadores do tipo escolhido que estão a até {{ \App\Models\Alerta::RAIO_KM }} km e já podem doar.
            </p>

            @if (is_null($hemocentro->latitude) || is_null($hemocentro->longitude))
                <p class="aviso-erro mt-4">
                    Sua unidade está sem localização, então nenhum doador será avisado.
                    Informe as coordenadas em <a href="{{ route('hemocentro.perfil') }}" class="underline underline-offset-4">Dados da instituição</a>.
                </p>
            @endif

            <form method="POST" action="{{ route('alertas.store') }}" class="mt-6 grid gap-5 sm:grid-cols-2">
                @csrf

                <fieldset class="sm:col-span-2">
                    <legend class="rotulo">Tipo sanguíneo em falta</legend>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach (\App\Models\Alerta::TIPOS_SANGUINEOS as $tipo)
                            <label class="cursor-pointer">
                                <input type="radio" name="tipo_sanguineo" value="{{ $tipo }}" required class="peer sr-only" @checked(old('tipo_sanguineo') === $tipo)>
                                <span class="inline-flex min-w-14 justify-center rounded-full border border-borda px-4 py-2.5 font-titulo font-bold peer-checked:border-sangue peer-checked:bg-sangue peer-checked:text-white peer-focus-visible:outline peer-focus-visible:outline-[3px] peer-focus-visible:outline-offset-2 peer-focus-visible:outline-arterial">{{ $tipo }}</span>
                            </label>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('tipo_sanguineo')" class="mt-2" />
                </fieldset>

                <div class="sm:col-span-2">
                    <label for="mensagem" class="rotulo">Mensagem para os doadores</label>
                    <textarea id="mensagem" name="mensagem" rows="3" maxlength="500" required aria-describedby="mensagemAjuda" class="campo">{{ old('mensagem') }}</textarea>
                    <p id="mensagemAjuda" class="ajuda">Diga o que está acontecendo e quando atender. Até 500 caracteres.</p>
                    <x-input-error :messages="$errors->get('mensagem')" class="mt-2" />
                </div>

                <div>
                    <label for="data_expiracao" class="rotulo">Vale até</label>
                    <input id="data_expiracao" type="date" name="data_expiracao" required
                           value="{{ old('data_expiracao', now()->addDays(7)->format('Y-m-d')) }}"
                           min="{{ now()->format('Y-m-d') }}" max="{{ now()->addDays(30)->format('Y-m-d') }}"
                           aria-describedby="validadeAjuda" class="campo">
                    <p id="validadeAjuda" class="ajuda">Sai do mural no fim desse dia. Até 30 dias.</p>
                    <x-input-error :messages="$errors->get('data_expiracao')" class="mt-2" />
                </div>

                <div class="flex items-end sm:col-span-2">
                    <button type="submit" class="botao-sangue">Publicar alerta</button>
                </div>
            </form>
        </section>

        <section>
            <div class="secao-cab">
                <h2>No mural agora</h2>
                <span>{{ $alertas->count() }}</span>
            </div>
            <ul class="linhas">
                @forelse ($alertas as $alerta)
                    <li class="grid grid-cols-[3.5rem_1fr] gap-x-4 py-4">
                        <span class="font-titulo text-3xl font-bold leading-none tracking-[-0.03em] text-sangue">{{ $alerta->tipo_sanguineo }}</span>
                        <div>
                            <p class="leading-snug">{{ $alerta->mensagem }}</p>
                            <p class="mt-1 text-sm text-cinza">Publicado em {{ $alerta->data_publicacao->format('d/m') }}, vale até {{ $alerta->data_expiracao->format('d/m') }}.</p>
                            <form method="POST" action="{{ route('alertas.encerrar', $alerta->id) }}" class="mt-2">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="botao-texto text-sm">Encerrar alerta</button>
                            </form>
                        </div>
                    </li>
                @empty
                    <li class="py-6 text-cinza">Nenhum alerta no mural. Quando faltar algum tipo sanguíneo, publique um alerta ao lado.</li>
                @endforelse
            </ul>
        </section>
    </div>
@endif
@endsection
