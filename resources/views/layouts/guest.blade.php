<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>DoaPlus - Doe sangue perto de você</title>
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

        <!-- Mesmas fontes da tela do mapa -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=familjen-grotesk:500,700|atkinson-hyperlegible:400,700&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        {{-- Mesmo desenho da tela inicial: painel à esquerda e o mapa ao fundo, à direita --}}
        <div class="min-h-dvh lg:grid lg:grid-cols-[480px_1fr]">
            <div class="flex min-h-dvh flex-col lg:border-r lg:border-linha">
                <header class="flex items-center justify-between gap-4 px-7 py-5">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 font-titulo text-[1.375rem] font-bold tracking-[-0.02em]">
                        <x-application-logo class="h-7 w-[22px] text-arterial" />
                        DoaPlus
                    </a>
                    <a href="{{ route('home') }}" class="hover:text-sangue">Ver o mapa</a>
                </header>

                <main class="flex-1 px-7 pb-12 pt-6">
                    <x-avisos class="mb-6" />
                    {{ $slot }}
                </main>
            </div>

            <div class="relative hidden bg-gaze lg:block">
                <div id="mapaFundo" class="absolute inset-0"></div>
            </div>
        </div>

        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script>
            // Só decoração: o mapa da tela inicial, parado e em tons de cinza (não carrega no celular)
            if (window.matchMedia('(min-width: 1024px)').matches && window.L) {
                const mapa = L.map('mapaFundo', { zoomControl: false, interactive: false, keyboard: false })
                    .setView([-28.2628, -52.4087], 14);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                }).addTo(mapa);
            }
        </script>
        <style>
            #mapaFundo .leaflet-tile-pane { filter: grayscale(1) brightness(1.04) contrast(.92); }
        </style>
    </body>
</html>
