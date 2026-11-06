@extends('layouts.painel')

@section('titulo', 'Aprovações')

@section('conteudo')
<section class="max-w-3xl">
    @if ($pedidos->isNotEmpty())
        <h1 class="manchete"><span class="numero">{{ $pedidos->count() }}</span> {{ $pedidos->count() === 1 ? 'instituição para aprovar' : 'instituições para aprovar' }}</h1>
    @else
        <h1 class="manchete">Nenhuma instituição para aprovar.</h1>
    @endif
    <p class="mt-4 max-w-[46ch] text-[1.0625rem] leading-normal text-cinza">Confira o CNPJ antes de aprovar. Só instituições aprovadas recebem agendamentos.</p>
</section>

<div class="mt-12 grid gap-12 lg:grid-cols-[1fr_420px]">
    <section>
        <div class="secao-cab">
            <h2>Fila de aprovação</h2>
            <span>{{ $pedidos->count() }}</span>
        </div>
        <ul class="linhas">
            @forelse ($pedidos as $pedido)
                <li class="py-5">
                    <div class="grid grid-cols-[14px_1fr] gap-x-3.5">
                        <x-application-logo class="mt-1 h-[15px] w-3 text-plasma" />
                        <div>
                            <p class="font-bold leading-snug">{{ $pedido->nome }}</p>
                            <p class="text-sm leading-snug text-cinza">CNPJ {{ $pedido->cnpj }}</p>
                            <p class="text-sm leading-snug text-cinza">{{ $pedido->email }}, telefone {{ $pedido->telefone }}</p>

                            <p class="mt-3">
                                @if ($pedido->localReivindicado)
                                    Quer assumir o ponto <strong>{{ $pedido->localReivindicado->nome }}</strong>.
                                @else
                                    Local novo em <strong>{{ $pedido->endereco }}</strong>.
                                    @if (is_null($pedido->latitude) || is_null($pedido->longitude))
                                        <span class="block text-sm text-cinza">Sem coordenadas: só aparece no mapa depois de informá-las no perfil.</span>
                                    @endif
                                @endif
                            </p>

                            <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2">
                                <form method="POST" action="{{ route('admin.aprovar', $pedido->id) }}">
                                    @csrf
                                    <button type="submit" class="botao-sangue botao-pequeno">{{ $pedido->localReivindicado ? 'Aprovar e unir ao ponto' : 'Aprovar' }}</button>
                                </form>
                                @if ($pedido->localReivindicado)
                                    <form method="POST" action="{{ route('admin.rejeitar', $pedido->id) }}">
                                        @csrf
                                        <button type="submit" class="botao-texto text-sm">Rejeitar pedido</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </li>
            @empty
                <li class="py-6 text-cinza">Quando uma instituição se cadastrar, ela aparece aqui.</li>
            @endforelse
        </ul>
    </section>

    <section>
        <div class="secao-cab">
            <h2>Pré-cadastrar hemocentro</h2>
        </div>
        <p class="mt-4 text-cinza">O ponto aparece no mapa como "Em breve" até a instituição assumi-lo.</p>

        <form method="POST" action="{{ route('admin.precadastro') }}" class="mt-6 grid grid-cols-2 gap-5">
            @csrf

            <div class="col-span-2">
                <label for="nome" class="rotulo">Nome da instituição</label>
                <input id="nome" type="text" name="nome" value="{{ old('nome') }}" required class="campo">
            </div>

            <div class="col-span-2">
                <label for="endereco" class="rotulo">Endereço completo</label>
                <input id="endereco" type="text" name="endereco" value="{{ old('endereco') }}" required class="campo">
            </div>

            <div class="col-span-2">
                <label for="telefone" class="rotulo">Telefone</label>
                <input id="telefone" type="tel" name="telefone" value="{{ old('telefone') }}" required class="campo">
            </div>

            <div>
                <label for="latitude" class="rotulo">Latitude</label>
                <input id="latitude" type="text" name="latitude" value="{{ old('latitude') }}" required inputmode="decimal" placeholder="-28.2612" class="campo">
            </div>
            <div>
                <label for="longitude" class="rotulo">Longitude</label>
                <input id="longitude" type="text" name="longitude" value="{{ old('longitude') }}" required inputmode="decimal" placeholder="-52.4083" class="campo">
            </div>
            <p class="ajuda col-span-2 -mt-2">No Google Maps, clique com o botão direito no local para copiar as coordenadas.</p>

            <x-erros class="col-span-2" />

            <div class="col-span-2 mt-1">
                <button type="submit" class="botao-sangue">Publicar no mapa</button>
            </div>
        </form>
    </section>
</div>
@endsection
