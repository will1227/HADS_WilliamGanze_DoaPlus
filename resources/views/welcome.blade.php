<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DoaPlus - Encontre onde doar sangue</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

    <!-- Familjen Grotesk nos títulos; Atkinson Hyperlegible (feita para baixa visão) no texto -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=familjen-grotesk:500,700|atkinson-hyperlegible:400,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Leaflet CSS (Mapa) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    <style>
        /* Paleta: sangue venoso, arterial e plasma sobre o branco frio de ambiente clínico */
        :root {
            --sangue: #9B1027;
            --arterial: #D21F3C;
            --plasma: #E9B949;
            --gaze: #F3F5F6;
            --tinta: #1D2433;
            --cinza: #5B6474;
            --linha: #DDE2E8;
            --titulo: 'Familjen Grotesk', system-ui, sans-serif;
            --texto: 'Atkinson Hyperlegible', system-ui, sans-serif;
        }

        body { font-family: var(--texto); color: var(--tinta); background: #fff; }

        /* Layout: no celular fica topo, mapa e depois o resto do painel; no desktop, painel à esquerda */
        .tela { display: flex; flex-direction: column; min-height: 100dvh; }
        .mapa { position: relative; }
        @media (max-width: 899px) {
            .painel { display: contents; }
            .topo { order: 0; }
            .mapa { order: 1; height: 46dvh; }
            .painel > :not(.topo) { order: 2; }
        }
        @media (min-width: 900px) {
            .tela { display: grid; }
            .painel { display: flex; flex-direction: column; }
            body { overflow: hidden; }
            .tela { grid-template-columns: 420px 1fr; grid-template-rows: 100dvh; }
            .painel { grid-column: 1; grid-row: 1; overflow-y: auto; border-right: 1px solid var(--linha); }
            .mapa { grid-column: 2; grid-row: 1; }
        }
        #map { position: absolute; inset: 0; background: var(--gaze); }
        #map .leaflet-tile-pane { filter: grayscale(1) brightness(1.04) contrast(.92); }

        /* Topo */
        .topo { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 1.25rem 1.75rem; }
        .marca { display: flex; align-items: center; gap: .5rem; font-family: var(--titulo); font-weight: 700; font-size: 1.375rem; letter-spacing: -.02em; color: var(--tinta); text-decoration: none; }
        .marca svg { width: 22px; height: 28px; color: var(--arterial); }
        .acesso { display: flex; align-items: center; gap: 1.25rem; font-size: .9375rem; }
        .acesso a { color: var(--tinta); text-decoration: none; }
        .acesso a:hover { color: var(--sangue); }
        .acesso .criar { color: var(--sangue); font-weight: 700; }

        /* Destaque: a frase muda conforme a localização da pessoa */
        .destaque { padding: 2.5rem 1.75rem 2rem; }
        .destaque h1 { font-family: var(--titulo); font-weight: 700; font-size: clamp(2.25rem, 6vw, 3.25rem); line-height: 1.02; letter-spacing: -.035em; margin: 0; max-width: 11ch; text-wrap: balance; }
        .destaque h1 .km { display: block; color: var(--sangue); font-size: 1.55em; letter-spacing: -.05em; font-variant-numeric: tabular-nums; }
        .destaque p { margin: 1rem 0 0; color: var(--cinza); font-size: 1.0625rem; line-height: 1.5; max-width: 36ch; }
        .destaque p strong { color: var(--tinta); }
        .acoes { display: flex; flex-wrap: wrap; align-items: center; gap: .75rem 1.25rem; margin-top: 1.75rem; }
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: .5rem; font-family: var(--titulo); font-weight: 700; font-size: 1rem; padding: .875rem 1.375rem; border-radius: 999px; border: 0; cursor: pointer; text-decoration: none; transition: background-color .15s; }
        .btn-sangue { background: var(--sangue); color: #fff; }
        .btn-sangue:hover { background: #7E0C1F; }
        .btn-texto { background: none; padding: .5rem 0; color: var(--tinta); text-decoration: underline; text-underline-offset: .25em; text-decoration-thickness: 1px; }
        .btn-texto:hover { color: var(--sangue); }
        .anima { animation: surgir .45s ease-out; }
        @keyframes surgir { from { opacity: 0; transform: translateY(6px); } }

        /* Lista de hemocentros, ordenada pela distância */
        .lista-cab { display: flex; justify-content: space-between; align-items: baseline; padding: 0 1.75rem .75rem; border-bottom: 1px solid var(--linha); }
        .lista-cab h2 { font-family: var(--titulo); font-weight: 700; font-size: 1.0625rem; margin: 0; }
        .lista-cab span { font-size: .875rem; color: var(--cinza); }
        .lista { list-style: none; margin: 0; padding: 0; flex: 1; }
        .lista li { border-bottom: 1px solid var(--linha); }
        .local { display: grid; grid-template-columns: 14px 1fr auto; gap: .25rem .875rem; align-items: start; width: 100%; padding: 1rem 1.75rem; background: none; border: 0; text-align: left; cursor: pointer; font: inherit; color: inherit; }
        .local:hover { background: var(--gaze); }
        .local[aria-current="true"] { background: #FBEFF1; box-shadow: inset 3px 0 0 var(--sangue); }
        .local .gota { width: 12px; height: 15px; margin-top: .3rem; }
        .local .nome { font-weight: 700; line-height: 1.3; }
        .local .end { grid-column: 2; font-size: .875rem; color: var(--cinza); line-height: 1.4; }
        .local .dist { font-family: var(--titulo); font-weight: 700; font-variant-numeric: tabular-nums; white-space: nowrap; }
        .local .breve { grid-column: 2; justify-self: start; font-size: .8125rem; color: #6B4E00; background: #FBF0D2; padding: .125rem .5rem; border-radius: 4px; margin-top: .25rem; }
        .vazio { padding: 2rem 1.75rem; color: var(--cinza); }

        /* Mural de urgências (HU09): o tipo sanguíneo em falta é o que salta aos olhos */
        .urgencias { margin-bottom: 2rem; }
        .tipos { display: flex; flex-wrap: wrap; gap: .5rem; padding: 1rem 1.75rem .25rem; }
        .tipo { font-family: var(--titulo); font-weight: 700; font-size: .9375rem; min-width: 3rem; padding: .375rem .75rem; border-radius: 999px; border: 1px solid #878F9D; background: #fff; color: var(--tinta); cursor: pointer; }
        .tipo:hover { border-color: var(--sangue); }
        .tipo[aria-pressed="true"] { background: var(--sangue); border-color: var(--sangue); color: #fff; }
        .urg-lista { list-style: none; margin: .75rem 0 0; padding: 0; }
        .urg-lista li { border-bottom: 1px solid var(--linha); }
        .urg-lista li:first-child { border-top: 1px solid var(--linha); }
        .urg { display: grid; grid-template-columns: 3.25rem 1fr auto; gap: .125rem .75rem; align-items: start; width: 100%; padding: 1rem 1.75rem; background: none; border: 0; text-align: left; cursor: pointer; font: inherit; color: inherit; }
        .urg:hover { background: #FBEFF1; }
        .urg .tipo-grande { grid-row: span 3; font-family: var(--titulo); font-weight: 700; font-size: 1.875rem; line-height: 1; letter-spacing: -.03em; color: var(--sangue); }
        .urg .nome { font-weight: 700; line-height: 1.3; }
        .urg .dist { font-family: var(--titulo); font-weight: 700; font-variant-numeric: tabular-nums; white-space: nowrap; }
        .urg .msg { grid-column: 2 / 4; font-size: .9375rem; line-height: 1.45; color: #3A4252; }
        .urg .ate { grid-column: 2 / 4; font-size: .8125rem; color: var(--cinza); }
        .urg-vazio { padding: 1rem 1.75rem 0; color: var(--cinza); }
        .urg-vazio button { font: inherit; color: var(--tinta); background: none; border: 0; padding: 0; text-decoration: underline; text-underline-offset: .25em; cursor: pointer; }
        .pop-urg { margin: 0 0 .75rem !important; font-size: .8125rem; font-weight: 700; color: var(--sangue); }

        .rodape { padding: 1.5rem 1.75rem 2rem; font-size: .9375rem; color: var(--cinza); }
        .rodape a { color: var(--tinta); }

        /* Controles sobre o mapa */
        .localizar { position: absolute; right: 1rem; bottom: 1.5rem; z-index: 500; width: 48px; height: 48px; border-radius: 999px; border: 1px solid var(--linha); background: #fff; color: var(--tinta); display: grid; place-items: center; cursor: pointer; box-shadow: 0 2px 8px rgba(29, 36, 51, .18); }
        .localizar:hover { color: var(--sangue); }
        .localizar svg { width: 22px; height: 22px; }
        .legenda { position: absolute; left: 1rem; bottom: 1.5rem; z-index: 500; display: flex; gap: 1rem; background: rgba(255,255,255,.94); border-radius: 8px; padding: .5rem .75rem; font-size: .8125rem; box-shadow: 0 1px 4px rgba(29, 36, 51, .15); }
        .legenda span { display: inline-flex; align-items: center; gap: .375rem; }
        .legenda i { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }

        /* Marcadores */
        .pin-icon { background: none; border: none; }
        .user-dot { width: 18px; height: 18px; border-radius: 50%; background: #2563eb; border: 3px solid #fff; box-shadow: 0 0 0 0 rgba(37, 99, 235, .6); animation: pulse 2s infinite; }
        @keyframes pulse { 70% { box-shadow: 0 0 0 14px rgba(37, 99, 235, 0); } 100% { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0); } }

        /* Popup do Leaflet com a mesma tipografia da página */
        .leaflet-container { font-family: var(--texto); }
        .leaflet-popup-content-wrapper { border-radius: 10px; }
        .leaflet-popup-content { margin: 1rem 1.125rem; min-width: 210px; }
        .pop-nome { font-family: var(--titulo); font-weight: 700; font-size: 1rem; line-height: 1.25; margin: 0; color: var(--tinta); }
        .pop-end { margin: .25rem 0 .875rem !important; color: var(--cinza); font-size: .8125rem; line-height: 1.4; }
        .pop-acoes { display: flex; flex-direction: column; gap: .5rem; }
        .leaflet-container a.pop-btn { display: block; text-align: center; font-weight: 700; font-size: .875rem; padding: .5rem .75rem; border-radius: 999px; text-decoration: none; }
        .leaflet-container a.pop-agendar { background: var(--sangue); color: #fff; }
        .leaflet-container a.pop-zap { border: 1px solid var(--linha); color: var(--tinta); }
        .pop-breve { font-size: .8125rem; color: #6B4E00; background: #FBF0D2; padding: .375rem .5rem; border-radius: 4px; margin: 0; }

        /* Guia do doador (dialog nativo: foco, Esc e fundo ficam por conta do navegador) */
        .guia { width: min(680px, calc(100vw - 2rem)); max-height: calc(100dvh - 2rem); padding: 0; border: 0; border-radius: 14px; color: var(--tinta); }
        .guia::backdrop { background: rgba(29, 36, 51, .55); }
        .guia-cab { position: sticky; top: 0; display: flex; justify-content: space-between; align-items: center; padding: 1.25rem 1.75rem; background: #fff; border-bottom: 1px solid var(--linha); }
        .guia-cab h2 { font-family: var(--titulo); font-weight: 700; font-size: 1.375rem; margin: 0; }
        .fechar { width: 40px; height: 40px; border-radius: 999px; border: 0; background: var(--gaze); color: var(--tinta); display: grid; place-items: center; cursor: pointer; }
        .fechar:hover { background: var(--linha); }
        .guia-corpo { padding: 1.75rem; line-height: 1.55; }
        .guia-corpo > p.abre { font-family: var(--titulo); font-size: 1.25rem; line-height: 1.35; margin: 0 0 2rem; max-width: 46ch; }
        .guia-corpo h3 { font-family: var(--titulo); font-weight: 700; font-size: 1.125rem; margin: 0 0 .75rem; }
        .guia-corpo section + section { margin-top: 2.25rem; }
        .requisitos { list-style: none; padding: 0; margin: 0; display: grid; gap: .625rem; }
        .requisitos li { padding-left: 1.5rem; position: relative; }
        .requisitos li::before { content: ''; position: absolute; left: 0; top: .45em; width: 9px; height: 11px; background: var(--arterial); clip-path: path('M4.5 0C4.5 0 0 4.8 0 7.2a4.5 4.5 0 0 0 9 0C9 4.8 4.5 0 4.5 0z'); }
        .faq details { border-top: 1px solid var(--linha); }
        .faq details:last-child { border-bottom: 1px solid var(--linha); }
        .faq summary { display: flex; justify-content: space-between; align-items: center; gap: 1rem; padding: 1rem 0; cursor: pointer; list-style: none; font-weight: 700; }
        .faq summary::-webkit-details-marker { display: none; }
        .faq summary::after { content: '+'; font-family: var(--titulo); font-size: 1.5rem; line-height: 1; color: var(--sangue); transition: transform .15s; }
        .faq details[open] summary::after { transform: rotate(45deg); }
        .faq details > :not(summary) { margin: 0 0 1rem; color: #3A4252; font-size: .9375rem; }
        .faq ol, .faq ul { padding-left: 1.25rem; }
        .faq li + li { margin-top: .375rem; }
        .faq ol { list-style: decimal; }
        .faq ul { list-style: disc; }
        .nota { font-size: .8125rem !important; color: var(--cinza) !important; }
        .guia-pe { position: sticky; bottom: 0; display: flex; flex-wrap: wrap; gap: .75rem 1.25rem; align-items: center; padding: 1rem 1.75rem; background: #fff; border-top: 1px solid var(--linha); }

        :focus-visible { outline: 3px solid var(--arterial); outline-offset: 2px; }
        .local:focus-visible { outline-offset: -3px; }

        @media (prefers-reduced-motion: reduce) {
            .anima, .user-dot { animation: none; }
            .faq summary::after { transition: none; }
        }
    </style>
</head>
<body class="antialiased">
    @php($logado = auth('web')->check() || auth('doador')->check() || auth('hemocentro')->check())

    <div class="tela">
        <main class="painel">
            <header class="topo">
                <a href="{{ route('home') }}" class="marca">
                    <svg viewBox="0 0 22 28" fill="currentColor" aria-hidden="true"><path d="M11 0S0 11.7 0 17.6a11 11 0 0 0 22 0C22 11.7 11 0 11 0z"/></svg>
                    DoaPlus
                </a>
                <nav class="acesso" aria-label="Conta">
                    @if ($logado)
                        <a href="{{ url('/dashboard') }}">Meu painel</a>
                    @else
                        <a href="{{ route('login') }}">Entrar</a>
                        <a href="{{ url('/register/doador') }}" class="criar">Criar conta</a>
                    @endif
                </nav>
            </header>

            <section class="destaque" aria-live="polite">
                <div id="destaque">
                    {{-- Conteúdo inicial; o script troca pela distância até o hemocentro mais próximo --}}
                    <h1>Doe sangue perto de você.</h1>
                    <p>Procurando sua localização para mostrar o hemocentro mais próximo.</p>
                </div>
                <div class="acoes">
                    <a id="btnAgendar" href="{{ url('/agendar') }}" class="btn btn-sangue">Agendar doação</a>
                    <button type="button" class="btn btn-texto" onclick="document.getElementById('guia').showModal()">Primeira vez? Veja como funciona</button>
                </div>
            </section>

            {{-- Mural de urgências (HU09): alertas publicados pelos hemocentros --}}
            @if ($urgencias->isNotEmpty())
                @php($tiposNoMural = collect(\App\Models\Alerta::TIPOS_SANGUINEOS)->filter(fn ($t) => $urgencias->contains('tipo_sanguineo', $t) || $t === $tipoDoDoador))
                <section class="urgencias" aria-labelledby="urgTitulo">
                    <div class="lista-cab">
                        <h2 id="urgTitulo">Precisam de sangue agora</h2>
                        <span id="urgInfo">{{ $urgencias->count() }} {{ $urgencias->count() === 1 ? 'alerta' : 'alertas' }}</span>
                    </div>
                    <div class="tipos" role="group" aria-label="Filtrar por tipo sanguíneo">
                        <button type="button" class="tipo" data-tipo="" aria-pressed="true">Todos</button>
                        @foreach ($tiposNoMural as $tipo)
                            <button type="button" class="tipo" data-tipo="{{ $tipo }}" aria-pressed="false">{{ $tipo }}</button>
                        @endforeach
                    </div>
                    <ul id="urgLista" class="urg-lista">
                        @foreach ($urgencias as $urgencia)
                            <li data-tipo="{{ $urgencia->tipo_sanguineo }}" data-hemocentro="{{ $urgencia->hemocentro->id }}"
                                data-lat="{{ $urgencia->hemocentro->latitude }}" data-lng="{{ $urgencia->hemocentro->longitude }}">
                                <button type="button" class="urg">
                                    <span class="tipo-grande">{{ $urgencia->tipo_sanguineo }}</span>
                                    <span class="nome">{{ $urgencia->hemocentro->nome }}</span>
                                    <span class="dist"></span>
                                    <span class="msg">{{ $urgencia->mensagem }}</span>
                                    <span class="ate">Até {{ $urgencia->data_expiracao->format('d/m') }}</span>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                    <p id="urgVazio" class="urg-vazio" hidden></p>
                </section>
            @endif

            <div class="lista-cab">
                <h2>Onde doar</h2>
                <span id="listaInfo"></span>
            </div>
            <ul id="lista" class="lista"></ul>

            <p class="rodape">
                Representa um hemocentro? <a href="{{ url('/register/hemocentro') }}">Cadastre sua instituição</a>.
            </p>
        </main>

        <div class="mapa">
            <div id="map" aria-label="Mapa dos hemocentros"></div>
            <div class="legenda" aria-hidden="true">
                <span><i style="background: var(--arterial)"></i>Agenda aberta</span>
                <span><i style="background: var(--plasma)"></i>Em breve</span>
            </div>
            <button id="btnLocalizar" type="button" class="localizar" aria-label="Centralizar na minha localização" title="Minha localização">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path stroke-linecap="round" d="M12 2v3m0 14v3M2 12h3m14 0h3"/><circle cx="12" cy="12" r="7"/></svg>
            </button>
        </div>
    </div>

    <!-- Guia do doador (HU08) -->
    <dialog id="guia" class="guia" aria-labelledby="guiaTitulo">
        <div class="guia-cab">
            <h2 id="guiaTitulo">Guia do doador</h2>
            <button type="button" class="fechar" onclick="this.closest('dialog').close()" aria-label="Fechar">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="guia-corpo">
            <p class="abre">Uma doação leva cerca de uma hora, coleta 450 ml e pode ajudar até quatro pessoas. Sangue não se fabrica: ele só existe porque alguém decidiu doar.</p>

            <section>
                <h3>Quem pode doar</h3>
                <ul class="requisitos">
                    <li><strong>Quem tem entre 16 e 69 anos.</strong> Menores de 18 precisam de autorização dos responsáveis; a primeira doação deve ser feita até os 60 anos.</li>
                    <li><strong>Quem pesa 50 kg ou mais</strong> e está bem de saúde.</li>
                    <li><strong>Quem dormiu pelo menos 6 horas</strong> nas últimas 24 horas.</li>
                    <li><strong>Quem leva documento oficial com foto</strong> (RG, CNH, passaporte ou carteira de trabalho).</li>
                </ul>
            </section>

            <section class="faq">
                <h3>Perguntas frequentes</h3>

                <details>
                    <summary>Dói? É seguro?</summary>
                    <p>Você sente só uma picada, igual a um exame de sangue. Todo o material é <strong>estéril e descartável</strong>, usado uma única vez: não existe risco de pegar doenças doando. Uma equipe de saúde acompanha você o tempo todo.</p>
                </details>

                <details>
                    <summary>Como funciona a doação, passo a passo?</summary>
                    <ol>
                        <li><strong>Cadastro</strong> com seu documento.</li>
                        <li><strong>Triagem:</strong> pressão, temperatura, peso e um teste rápido de anemia.</li>
                        <li><strong>Entrevista</strong> individual e sigilosa sobre sua saúde e hábitos.</li>
                        <li><strong>Coleta:</strong> dura de 8 a 12 minutos.</li>
                        <li><strong>Lanche</strong> e 15 minutos de descanso antes de ir embora.</li>
                    </ol>
                </details>

                <details>
                    <summary>Preciso estar em jejum?</summary>
                    <p><strong>Não. Venha alimentado.</strong> Evite só alimentos gordurosos nas 3 horas antes da doação. Se for depois do almoço, espere 2 horas. Beba bastante água no dia.</p>
                </details>

                <details>
                    <summary>De quanto em quanto tempo posso doar?</summary>
                    <p><strong>Homens:</strong> a cada 60 dias, no máximo 4 vezes por ano.<br><strong>Mulheres:</strong> a cada 90 dias, no máximo 3 vezes por ano.<br>O DoaPlus calcula essa data e avisa quando você já pode doar de novo.</p>
                </details>

                <details>
                    <summary>Vou ficar fraco depois?</summary>
                    <p>Não. O volume coletado é menos de 10% do sangue do corpo, e o organismo repõe o líquido em poucas horas. No resto do dia, beba bastante líquido, evite esforço físico pesado e álcool e mantenha o curativo por algumas horas.</p>
                </details>

                <details>
                    <summary>Tenho tatuagem ou piercing. Posso doar?</summary>
                    <p><strong>Pode,</strong> se tiver feito há mais de <strong>12 meses</strong>. Para piercing na boca ou na região genital, a contagem começa a partir da retirada.</p>
                </details>

                <details>
                    <summary>O que impede a doação temporariamente?</summary>
                    <ul>
                        <li><strong>Gripe, resfriado ou febre:</strong> aguarde 7 dias após o fim dos sintomas.</li>
                        <li><strong>Bebida alcoólica:</strong> não beba nas 12 horas anteriores.</li>
                        <li><strong>Cigarro:</strong> evite fumar por pelo menos 2 horas antes.</li>
                        <li><strong>Gravidez:</strong> após o parto, aguarde 90 dias (parto normal) ou 180 dias (cesárea). Quem está amamentando aguarda o bebê completar 12 meses.</li>
                        <li><strong>Vacinas, cirurgias e tratamento dentário:</strong> o prazo varia. Na dúvida, ligue para o hemocentro.</li>
                    </ul>
                    <p class="nota">Essas restrições protegem você e quem vai receber o sangue. A triagem do hemocentro dá a palavra final.</p>
                </details>

                <details>
                    <summary>Tenho direito a folga no trabalho?</summary>
                    <p><strong>Sim.</strong> Pela CLT (art. 473), você pode faltar <strong>1 dia a cada 12 meses</strong> para doar sangue, sem desconto no salário. Peça a declaração de comparecimento no hemocentro.</p>
                </details>

                <details>
                    <summary>Qual tipo sanguíneo é mais necessário?</summary>
                    <p><strong>Todos.</strong> Os estoques precisam ser repostos sempre. O <strong>O negativo</strong> é o doador universal, usado em emergências quando não dá tempo de saber o tipo do paciente. Não sabe o seu? Ele é identificado na primeira doação.</p>
                </details>
            </section>

            <section>
                <h3>Ainda com dúvida? Vá mesmo assim.</h3>
                <p style="margin:0">A equipe do hemocentro conversa com você antes de tudo e responde qualquer pergunta. No pior caso, você descobre o que precisa para doar na próxima vez.</p>
            </section>
        </div>

        <div class="guia-pe">
            <button type="button" class="btn btn-sangue" onclick="this.closest('dialog').close()">Ver hemocentros no mapa</button>
            @unless ($logado)
                <a href="{{ url('/register/doador') }}" class="btn btn-texto">Criar conta de doador</a>
            @endunless
        </div>
    </dialog>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        // Clique no fundo escuro fecha o guia
        document.getElementById('guia').addEventListener('click', function (e) {
            if (e.target === this) this.close();
        });

        document.addEventListener('DOMContentLoaded', function () {
            const URL_AGENDAR = @json(url('/agendar'));
            const hemocentros = @json($hemocentros);

            // Mural de urgências: os itens já vêm prontos do servidor; aqui só filtra e ordena
            const RAIO_KM = @json(\App\Models\Alerta::RAIO_KM);
            const urgencias = [...document.querySelectorAll('#urgLista > li')].map(li => ({
                li,
                tipo: li.dataset.tipo,
                hemocentro: Number(li.dataset.hemocentro),
                lat: Number(li.dataset.lat),
                lng: Number(li.dataset.lng),
                distancia: null,
            }));
            const tiposPorHemocentro = {};
            urgencias.forEach(u => (tiposPorHemocentro[u.hemocentro] ??= new Set()).add(u.tipo));

            // Textos vindos do banco entram no HTML do popup: escapa antes
            function esc(texto) {
                const div = document.createElement('div');
                div.textContent = texto ?? '';
                return div.innerHTML;
            }

            // 1. Mapa começa no RS; depois é ajustado aos hemocentros ou à localização do usuário
            const map = L.map('map', { zoomControl: false }).setView([-28.4489, -52.7306], 12);
            L.control.zoom({ position: 'topright' }).addTo(map);

            // Base do OpenStreetMap; o CSS tira a cor dos tiles para os pinos vermelhos se destacarem
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            // 2. Pinos em forma de gota: vermelho = agenda aberta, plasma = em breve
            function gotaSvg(cor, w, h) {
                return `<svg width="${w}" height="${h}" viewBox="0 0 22 28" aria-hidden="true"><path d="M11 1S1 11.9 1 17.4a10 10 0 0 0 20 0C21 11.9 11 1 11 1z" fill="${cor}" stroke="#fff" stroke-width="2"/></svg>`;
            }
            function pinIcon(cor) {
                return L.divIcon({
                    className: 'pin-icon',
                    html: `<div style="filter: drop-shadow(0 2px 2px rgba(29,36,51,.4))">${gotaSvg(cor, 28, 36)}</div>`,
                    iconSize: [28, 36],
                    iconAnchor: [14, 35],
                    popupAnchor: [0, -30],
                });
            }
            const COR_ATIVO = '#D21F3C', COR_BREVE = '#E9B949';
            const iconeAtivo = pinIcon(COR_ATIVO);
            const iconeBreve = pinIcon(COR_BREVE);
            const userIcon = L.divIcon({ className: 'pin-icon', html: '<div class="user-dot"></div>', iconSize: [18, 18], iconAnchor: [9, 9] });

            function popupHtml(h) {
                let acoes = '';
                if (h.agendavel) {
                    acoes += `<a class="pop-btn pop-agendar" href="${URL_AGENDAR}?hemocentro=${h.id}">Agendar doação aqui</a>`;
                } else {
                    acoes += '<p class="pop-breve">Em breve: este local ainda não abriu a agenda no DoaPlus.</p>';
                }
                if (h.telefone) {
                    const numero = h.telefone.replace(/\D/g, '');
                    acoes += `<a class="pop-btn pop-zap" href="https://api.whatsapp.com/send?phone=55${numero}" target="_blank" rel="noopener">Conversar no WhatsApp</a>`;
                }
                const urgencia = tiposPorHemocentro[h.id]
                    ? `<p class="pop-urg">Precisa de sangue ${[...tiposPorHemocentro[h.id]].join(', ')}</p>`
                    : '';
                return `<p class="pop-nome">${esc(h.nome)}</p><p class="pop-end">${esc(h.endereco)}</p>${urgencia}<div class="pop-acoes">${acoes}</div>`;
            }

            // 3. Marcadores e lista lateral compartilham o mesmo objeto
            const locais = hemocentros.map(function (h) {
                const marcador = L.marker([h.latitude, h.longitude], { icon: h.agendavel ? iconeAtivo : iconeBreve, title: h.nome })
                    .addTo(map)
                    .bindPopup(popupHtml(h));
                const local = { ...h, marcador, distancia: null, botao: null };
                marcador.on('click', () => marcarSelecionado(local));
                return local;
            });

            const lista = document.getElementById('lista');
            const listaInfo = document.getElementById('listaInfo');

            function formatarKm(km) {
                return (km < 10 ? km.toFixed(1) : Math.round(km).toString()).replace('.', ',') + ' km';
            }

            function marcarSelecionado(local) {
                locais.forEach(l => l.botao && l.botao.setAttribute('aria-current', l === local ? 'true' : 'false'));
            }

            // Leva o mapa até o hemocentro e abre o popup dele
            function focarLocal(local) {
                marcarSelecionado(local);
                map.flyTo(local.marcador.getLatLng(), Math.max(map.getZoom(), 15));
                local.marcador.openPopup();
                // No celular o mapa fica acima da lista
                if (window.matchMedia('(max-width: 899px)').matches) {
                    document.querySelector('.mapa').scrollIntoView({ behavior: 'smooth' });
                }
            }

            function desenharLista() {
                lista.innerHTML = '';
                if (!locais.length) {
                    lista.innerHTML = '<li class="vazio">Nenhum hemocentro cadastrado no mapa ainda.</li>';
                    listaInfo.textContent = '';
                    return;
                }
                listaInfo.textContent = locais.some(l => l.distancia !== null) ? 'Mais perto primeiro' : `${locais.length} ${locais.length === 1 ? 'local' : 'locais'}`;

                locais.forEach(function (local) {
                    const li = document.createElement('li');
                    const botao = document.createElement('button');
                    botao.type = 'button';
                    botao.className = 'local';
                    botao.setAttribute('aria-current', 'false');
                    botao.innerHTML = `
                        <span class="gota">${gotaSvg(local.agendavel ? COR_ATIVO : COR_BREVE, 12, 15)}</span>
                        <span class="nome">${esc(local.nome)}</span>
                        <span class="dist">${local.distancia !== null ? formatarKm(local.distancia) : ''}</span>
                        <span class="end">${esc(local.endereco)}</span>
                        ${local.agendavel ? '' : '<span class="breve">Em breve</span>'}`;
                    botao.addEventListener('click', () => focarLocal(local));
                    local.botao = botao;
                    li.appendChild(botao);
                    lista.appendChild(li);
                });
            }

            // 4. Destaque: troca a frase conforme a localização
            const destaque = document.getElementById('destaque');
            const btnAgendar = document.getElementById('btnAgendar');
            const ativos = locais.filter(l => l.agendavel);

            function mostrarDestaque(html) {
                destaque.innerHTML = html;
                destaque.classList.remove('anima');
                void destaque.offsetWidth;
                destaque.classList.add('anima');
            }

            function destaqueSemLocal(motivo) {
                const qtd = ativos.length;
                if (!qtd) {
                    mostrarDestaque('<h1>Doe sangue perto de você.</h1><p>Os hemocentros da região ainda estão abrindo a agenda no DoaPlus. Veja no mapa os locais que chegam em breve.</p>');
                    return;
                }
                const texto = qtd === 1 ? '<strong>1 hemocentro</strong> com agenda aberta' : `<strong>${qtd} hemocentros</strong> com agenda aberta`;
                mostrarDestaque(`<h1>Doe sangue perto de você.</h1><p>${texto} no mapa. ${motivo}</p>`);
            }

            function destaqueComLocal() {
                const proximo = ativos[0];
                if (!proximo) { destaqueSemLocal(''); return; }
                btnAgendar.href = `${URL_AGENDAR}?hemocentro=${proximo.id}`;
                mostrarDestaque(`<h1>O mais perto fica a <span class="km">${formatarKm(proximo.distancia)}</span></h1><p><strong>${esc(proximo.nome)}</strong>, ${esc(proximo.endereco)}.</p>`);
            }

            // Distância em linha reta (fórmula de Haversine)
            function distanciaKm(a, b) {
                const rad = x => x * Math.PI / 180;
                const dLat = rad(b.lat - a.lat), dLng = rad(b.lng - a.lng);
                const s = Math.sin(dLat / 2) ** 2 + Math.cos(rad(a.lat)) * Math.cos(rad(b.lat)) * Math.sin(dLng / 2) ** 2;
                return 6371 * 2 * Math.asin(Math.sqrt(s));
            }

            // Filtro do mural: por tipo sanguíneo e, com a localização, só o que está a até RAIO_KM
            let tipoAtivo = '';
            let soPerto = false;
            const urgInfo = document.getElementById('urgInfo');
            const urgVazio = document.getElementById('urgVazio');
            const botoesTipo = document.querySelectorAll('.tipo');

            function filtrarUrgencias() {
                if (!urgencias.length) return;
                let visiveis = 0;
                urgencias.forEach(u => {
                    const mostra = (!tipoAtivo || u.tipo === tipoAtivo) && (!soPerto || u.distancia <= RAIO_KM);
                    u.li.hidden = !mostra;
                    if (mostra) visiveis++;
                });
                botoesTipo.forEach(b => b.setAttribute('aria-pressed', b.dataset.tipo === tipoAtivo ? 'true' : 'false'));

                const regiao = soPerto ? ` a até ${RAIO_KM} km` : '';
                urgInfo.textContent = `${visiveis} ${visiveis === 1 ? 'alerta' : 'alertas'}${regiao}`;

                urgVazio.hidden = visiveis > 0;
                if (!visiveis) {
                    const deTipo = tipoAtivo ? ` de ${tipoAtivo}` : '';
                    urgVazio.innerHTML = soPerto
                        ? `Nenhuma urgência${deTipo} a até ${RAIO_KM} km de você. <button type="button" id="urgTodas">Ver as de toda a região</button>`
                        : `Nenhuma urgência${deTipo} no momento.`;
                    document.getElementById('urgTodas')?.addEventListener('click', () => { soPerto = false; filtrarUrgencias(); });
                }
            }

            botoesTipo.forEach(b => b.addEventListener('click', () => { tipoAtivo = b.dataset.tipo; filtrarUrgencias(); }));
            urgencias.forEach(u => u.li.querySelector('.urg').addEventListener('click', () => {
                const local = locais.find(l => l.id === u.hemocentro);
                if (local) focarLocal(local);
            }));

            // Doador logado começa vendo as urgências do próprio tipo
            const tipoDoDoador = @json($tipoDoDoador);
            if (tipoDoDoador && [...botoesTipo].some(b => b.dataset.tipo === tipoDoDoador)) {
                tipoAtivo = tipoDoDoador;
            }
            filtrarUrgencias();

            desenharLista();
            if (locais.length) {
                map.fitBounds(L.featureGroup(locais.map(l => l.marcador)).getBounds(), { padding: [60, 60], maxZoom: 14 });
            }

            // 5. Localização do usuário
            let marcadorUsuario = null;

            function localizar() {
                if (!('geolocation' in navigator)) {
                    destaqueSemLocal('Seu navegador não informa a localização; escolha um local na lista.');
                    return;
                }
                if (!window.isSecureContext) {
                    destaqueSemLocal('A localização só funciona em HTTPS ou em localhost; escolha um local na lista.');
                    return;
                }

                navigator.geolocation.getCurrentPosition(function (pos) {
                    const aqui = L.latLng(pos.coords.latitude, pos.coords.longitude);

                    if (marcadorUsuario) {
                        marcadorUsuario.setLatLng(aqui);
                    } else {
                        marcadorUsuario = L.marker(aqui, { icon: userIcon, zIndexOffset: 1000, keyboard: false })
                            .addTo(map)
                            .bindPopup('<b>Você está aqui</b>');
                    }

                    locais.forEach(l => l.distancia = distanciaKm(aqui, l.marcador.getLatLng()));
                    locais.sort((a, b) => a.distancia - b.distancia);
                    ativos.sort((a, b) => a.distancia - b.distancia);
                    desenharLista();
                    destaqueComLocal();

                    // Urgências: mais perto primeiro e só da região de quem está vendo
                    urgencias.forEach(u => {
                        u.distancia = distanciaKm(aqui, { lat: u.lat, lng: u.lng });
                        u.li.querySelector('.dist').textContent = formatarKm(u.distancia);
                    });
                    urgencias.sort((a, b) => a.distancia - b.distancia).forEach(u => u.li.parentNode.appendChild(u.li));
                    soPerto = true;
                    filtrarUrgencias();

                    // Enquadra você e o hemocentro mais próximo juntos
                    const alvo = ativos[0] || locais[0];
                    if (alvo) {
                        map.flyToBounds(L.latLngBounds([aqui, alvo.marcador.getLatLng()]), { padding: [80, 80], maxZoom: 15 });
                    } else {
                        map.flyTo(aqui, 14);
                    }
                }, function (erro) {
                    const motivos = {
                        1: 'Para ver o mais próximo, libere a localização nas configurações do navegador.',
                        2: 'Não conseguimos achar sua localização; escolha um local na lista.',
                        3: 'A localização demorou para responder. Toque no botão de mira no mapa para tentar de novo.',
                    };
                    destaqueSemLocal(motivos[erro.code] || 'Escolha um local na lista.');
                }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 });
            }

            document.getElementById('btnLocalizar').addEventListener('click', localizar);
            localizar();
        });
    </script>
</body>
</html>
