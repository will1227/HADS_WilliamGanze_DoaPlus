<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DoaPlus - Salve Vidas</title>

    <!-- Fontes e Tailwind CSS via Vite -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Leaflet CSS (Mapa) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    
    <style>
        /* O mapa ocupa toda a tela menos a altura da barra do topo (aprox 64px) */
        #map { height: calc(100vh - 64px); width: 100%; z-index: 10; }
    </style>
</head>
<body class="antialiased bg-gray-50 flex flex-col h-screen overflow-hidden">

    <!-- Top Navigation Bar -->
    <nav class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-6 shrink-0 shadow-sm relative z-20">
        <div class="flex items-center gap-2">
            <!-- Logo DoaPlus -->
            <svg class="w-8 h-8 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
            </svg>
            <span class="text-2xl font-bold text-gray-800 tracking-tight">Doa<span class="text-red-600">Plus</span></span>
        </div>

        <div class="flex items-center gap-4 text-sm font-medium">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="text-gray-700 hover:text-red-600 transition">Meu Painel</a>
                @else
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-red-600 transition">Entrar</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition shadow-sm">Criar Conta</a>
                    @endif
                @endauth
            @endif
        </div>
    </nav>

    <!-- Map Container -->
    <div id="map"></div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Inicializa o mapa centralizado no sul do Brasil (RS)
            const map = L.map('map').setView([-28.4489, -52.7306], 12);

            // 2. Carrega as texturas do OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // 3. Ícone customizado para o Usuário
            const userIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
            });

            // 4. Ícone customizado para Hemocentros (Vermelho)
            const hemoIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
            });

            // 5. Localização do usuário via GPS do navegador
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    map.setView([lat, lng], 13);
                    L.marker([lat, lng], {icon: userIcon})
                     .addTo(map)
                     .bindPopup("<b>Você está aqui</b>")
                     .openPopup();
                });
            }

            // 6. Recebe os Hemocentros do Banco de Dados e plota no mapa
            const hemocentros = @json($hemocentros);
            
            hemocentros.forEach(function(hemo) {
                if(hemo.latitude && hemo.longitude) {
                    L.marker([hemo.latitude, hemo.longitude], {icon: hemoIcon})
                     .addTo(map)
                     .bindPopup(`<b>${hemo.nome}</b><br>${hemo.endereco}<br><br><a href="/app/agendamentos/create" class="text-red-600 font-bold text-xs underline">Agendar Doação</a>`);
                }
            });
        });
    </script>
</body>
</html>
