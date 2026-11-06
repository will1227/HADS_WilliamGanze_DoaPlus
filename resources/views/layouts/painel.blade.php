<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo') - DoaPlus</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

    <!-- Mesmas fontes da tela do mapa -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=familjen-grotesk:500,700|atkinson-hyperlegible:400,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @php
        // Links de cada tipo de conta (cada perfil tem poucas telas, então não precisa de menu lateral)
        if (Auth::guard('web')->check()) {
            $links = ['dashboard' => 'Aprovações'];
        } elseif (Auth::guard('doador')->check()) {
            $links = ['dashboard' => 'Meu painel', 'doador.perfil' => 'Meus dados'];
            $alertasNovos = Auth::guard('doador')->user()->alertasNovos()->count();
        } else {
            $links = ['dashboard' => 'Agenda', 'hemocentro.perfil' => 'Dados da instituição'];
        }
    @endphp

    <header class="border-b border-linha">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-x-6 gap-y-3 px-7 py-5">
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-titulo text-[1.375rem] font-bold tracking-[-0.02em]">
                <x-application-logo class="h-7 w-[22px] text-arterial" />
                DoaPlus
            </a>

            <nav aria-label="Painel" class="flex flex-wrap items-center gap-x-5 gap-y-2">
                @foreach ($links as $rota => $nome)
                    <a href="{{ route($rota) }}"
                       @class(['font-bold text-sangue' => request()->routeIs($rota), 'hover:text-sangue'])
                       @if (request()->routeIs($rota)) aria-current="page" @endif>{{ $nome }}</a>
                @endforeach
                @if (! empty($alertasNovos))
                    <a href="{{ route('dashboard') }}#alertas" class="inline-flex items-center gap-2 font-bold text-sangue">
                        Alertas
                        <span class="rounded-full bg-sangue px-2 py-0.5 text-sm leading-none text-white tabular-nums">{{ $alertasNovos }}<span class="sr-only"> novos</span></span>
                    </a>
                @endif
                <a href="{{ route('home') }}" class="hover:text-sangue">Ver o mapa</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-cinza hover:text-sangue">Sair</button>
                </form>
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-7 pb-16 pt-10">
        <x-avisos class="mb-8 max-w-2xl" />
        @yield('conteudo')
    </main>
</body>
</html>
