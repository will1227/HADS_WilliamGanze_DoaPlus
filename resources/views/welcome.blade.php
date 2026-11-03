<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DoaPlus - Salve Vidas</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

    <!-- Fontes e Tailwind CSS via Vite -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Leaflet CSS (Mapa) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    
    <style>
        /* Pino dos hemocentros e ponto do usuário (sem imagens externas) */
        .pin-icon { background: none; border: none; }
        .user-dot { width: 18px; height: 18px; border-radius: 9999px; background: #2563eb; border: 3px solid #fff; box-shadow: 0 0 0 0 rgba(37, 99, 235, .6); animation: pulse 2s infinite; }
        @keyframes pulse { 70% { box-shadow: 0 0 0 14px rgba(37, 99, 235, 0); } 100% { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0); } }

        /* O Leaflet pinta todo link de azul; os botões do popup precisam manter o texto branco */
        .leaflet-popup-content { margin: 14px 16px; min-width: 200px; text-align: center; }
        .leaflet-container a.popup-btn { color: #fff; }
    </style>
</head>
<body class="antialiased bg-gray-50 flex flex-col h-[100dvh] overflow-hidden relative">

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
    <div id="map" class="flex-1 min-h-0 z-10"></div>

    <!-- Botão para centralizar na localização do usuário -->
    <button id="btnLocalizar" type="button" title="Minha localização"
            class="fixed bottom-10 right-8 z-[999] bg-white border border-gray-200 w-12 h-12 rounded-full shadow-lg flex items-center justify-center text-gray-700 hover:text-blue-600 hover:scale-105 transition-all">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path stroke-linecap="round" d="M12 2v3m0 14v3M2 12h3m14 0h3"/><circle cx="12" cy="12" r="7"/></svg>
    </button>

    <!-- Aviso quando a localização falha -->
    <div id="avisoLocal" class="hidden fixed top-20 left-1/2 -translate-x-1/2 z-[999] bg-gray-900/90 text-white text-sm px-4 py-2 rounded-lg shadow-lg text-center max-w-[90vw]"></div>

    <!-- ========================================== -->
    <!-- INÍCIO DA HU08: BOTÃO E MODAL DO FAQ       -->
    <!-- ========================================== -->

    <!-- Botão Flutuante do FAQ (Canto Inferior Esquerdo) -->
    <button onclick="document.getElementById('faqModal').classList.remove('hidden')" 
            class="fixed bottom-8 left-8 z-[999] bg-white border border-gray-200 p-3 pr-5 rounded-full shadow-lg flex items-center gap-3 hover:scale-105 transition-all group">
        <div class="bg-red-50 text-red-600 p-2 rounded-full group-hover:bg-red-100 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div class="text-left">
            <p class="text-[10px] font-bold text-red-600 uppercase tracking-wider">Dúvidas?</p>
            <p class="text-sm font-bold text-gray-800">Guia do Doador</p>
        </div>
    </button>

    <!-- Modal do FAQ / Guia do Doador -->
    <div id="faqModal" onclick="if (event.target === this) fecharFaq()"
         class="hidden fixed inset-0 z-[1000] bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl flex flex-col max-h-[90vh]" role="dialog" aria-modal="true" aria-labelledby="faqTitulo">

            <!-- Cabeçalho -->
            <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="bg-red-100 text-red-600 p-2 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <h2 id="faqTitulo" class="text-xl font-bold text-gray-900">Guia do Doador</h2>
                </div>
                <button onclick="fecharFaq()" aria-label="Fechar" class="text-gray-400 hover:text-gray-600 bg-gray-50 hover:bg-gray-100 p-2 rounded-full transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="overflow-y-auto">
                <!-- Destaque: incentivo -->
                <div class="bg-gradient-to-br from-red-600 to-red-700 text-white px-6 py-6 text-center">
                    <p class="text-lg font-bold">Uma única doação pode salvar até 4 vidas.</p>
                    <p class="text-sm text-red-100 mt-1">Sangue não pode ser fabricado: ele só existe porque alguém decidiu doar. Leva cerca de 1 hora, é seguro e você sai de lá sabendo que fez a diferença.</p>
                    <div class="grid grid-cols-3 gap-3 mt-5">
                        <div class="bg-white/10 rounded-xl py-3">
                            <p class="text-2xl font-extrabold">~1h</p>
                            <p class="text-[11px] uppercase tracking-wide text-red-100">do início ao fim</p>
                        </div>
                        <div class="bg-white/10 rounded-xl py-3">
                            <p class="text-2xl font-extrabold">450 ml</p>
                            <p class="text-[11px] uppercase tracking-wide text-red-100">coletados</p>
                        </div>
                        <div class="bg-white/10 rounded-xl py-3">
                            <p class="text-2xl font-extrabold">4 vidas</p>
                            <p class="text-[11px] uppercase tracking-wide text-red-100">ajudadas</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-8">

                    <!-- Seção: Quem pode doar -->
                    <section>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-red-600 mb-3">Quem pode doar</h3>
                        <ul class="grid sm:grid-cols-2 gap-3 text-sm">
                            <li class="flex gap-3 items-start bg-gray-50 rounded-xl p-3"><span class="text-red-600 font-bold">✓</span><span class="text-gray-700"><strong>Entre 16 e 69 anos.</strong> Menores de 18 precisam de autorização dos responsáveis; a primeira doação deve ser feita até os 60 anos.</span></li>
                            <li class="flex gap-3 items-start bg-gray-50 rounded-xl p-3"><span class="text-red-600 font-bold">✓</span><span class="text-gray-700"><strong>Pesar 50 kg ou mais</strong> e estar em boas condições de saúde.</span></li>
                            <li class="flex gap-3 items-start bg-gray-50 rounded-xl p-3"><span class="text-red-600 font-bold">✓</span><span class="text-gray-700"><strong>Ter dormido pelo menos 6 horas</strong> nas últimas 24 horas.</span></li>
                            <li class="flex gap-3 items-start bg-gray-50 rounded-xl p-3"><span class="text-red-600 font-bold">✓</span><span class="text-gray-700"><strong>Levar documento oficial com foto</strong> (RG, CNH, Passaporte ou Carteira de Trabalho).</span></li>
                        </ul>
                    </section>

                    <!-- Seção: Perguntas frequentes -->
                    <section>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-red-600 mb-3">Perguntas frequentes</h3>
                        <div class="divide-y divide-gray-100 border border-gray-100 rounded-xl">

                            <details class="group p-4">
                                <summary class="flex justify-between items-center cursor-pointer list-none font-semibold text-gray-900">Dói? É seguro?<span class="text-red-500 transition group-open:rotate-45 text-xl leading-none">+</span></summary>
                                <p class="text-sm text-gray-600 mt-3">Você sente só uma picadinha, igual a um exame de sangue. Todo o material é <strong>estéril e descartável</strong>, usado uma única vez: não existe nenhum risco de pegar doenças doando. Uma equipe de saúde acompanha você o tempo todo.</p>
                            </details>

                            <details class="group p-4">
                                <summary class="flex justify-between items-center cursor-pointer list-none font-semibold text-gray-900">Como funciona a doação, passo a passo?<span class="text-red-500 transition group-open:rotate-45 text-xl leading-none">+</span></summary>
                                <ol class="text-sm text-gray-600 mt-3 space-y-2 list-decimal list-inside">
                                    <li><strong>Cadastro</strong> com seu documento.</li>
                                    <li><strong>Triagem:</strong> verificação de pressão, temperatura, peso e um teste rápido de anemia.</li>
                                    <li><strong>Entrevista</strong> individual e sigilosa sobre sua saúde e hábitos.</li>
                                    <li><strong>Coleta:</strong> dura de 8 a 12 minutos.</li>
                                    <li><strong>Lanche</strong> e um descanso de 15 minutos antes de ir embora.</li>
                                </ol>
                            </details>

                            <details class="group p-4">
                                <summary class="flex justify-between items-center cursor-pointer list-none font-semibold text-gray-900">Preciso estar em jejum?<span class="text-red-500 transition group-open:rotate-45 text-xl leading-none">+</span></summary>
                                <p class="text-sm text-gray-600 mt-3"><strong>Não! Pelo contrário: venha alimentado.</strong> Evite apenas alimentos gordurosos nas 3 horas antes da doação. Se for depois do almoço, espere 2 horas. Beba bastante água no dia.</p>
                            </details>

                            <details class="group p-4">
                                <summary class="flex justify-between items-center cursor-pointer list-none font-semibold text-gray-900">De quanto em quanto tempo posso doar?<span class="text-red-500 transition group-open:rotate-45 text-xl leading-none">+</span></summary>
                                <p class="text-sm text-gray-600 mt-3"><strong>Homens:</strong> a cada 60 dias, no máximo 4 vezes por ano.<br><strong>Mulheres:</strong> a cada 90 dias, no máximo 3 vezes por ano.<br>O DoaPlus calcula essa data para você e avisa quando você já pode doar de novo.</p>
                            </details>

                            <details class="group p-4">
                                <summary class="flex justify-between items-center cursor-pointer list-none font-semibold text-gray-900">Vou ficar fraco depois?<span class="text-red-500 transition group-open:rotate-45 text-xl leading-none">+</span></summary>
                                <p class="text-sm text-gray-600 mt-3">Não. O volume coletado é menos de 10% do sangue do corpo, e o organismo repõe o líquido em poucas horas. No resto do dia, beba bastante líquido, evite esforço físico pesado e bebidas alcoólicas e mantenha o curativo por algumas horas. Você pode voltar à rotina normalmente.</p>
                            </details>

                            <details class="group p-4">
                                <summary class="flex justify-between items-center cursor-pointer list-none font-semibold text-gray-900">Tenho tatuagem ou piercing. Posso doar?<span class="text-red-500 transition group-open:rotate-45 text-xl leading-none">+</span></summary>
                                <p class="text-sm text-gray-600 mt-3"><strong>Pode!</strong> Basta ter feito há mais de <strong>12 meses</strong>. Para piercing na boca ou na região genital, a contagem começa a partir da retirada.</p>
                            </details>

                            <details class="group p-4">
                                <summary class="flex justify-between items-center cursor-pointer list-none font-semibold text-gray-900">O que impede a doação temporariamente?<span class="text-red-500 transition group-open:rotate-45 text-xl leading-none">+</span></summary>
                                <ul class="text-sm text-gray-600 mt-3 space-y-1.5">
                                    <li>• <strong>Gripe, resfriado ou febre:</strong> aguarde 7 dias após o fim dos sintomas.</li>
                                    <li>• <strong>Bebida alcoólica:</strong> não beba nas 12 horas anteriores.</li>
                                    <li>• <strong>Cigarro:</strong> evite fumar por pelo menos 2 horas antes.</li>
                                    <li>• <strong>Gravidez:</strong> após o parto, aguarde 90 dias (parto normal) ou 180 dias (cesárea). Quem está amamentando aguarda o bebê completar 12 meses.</li>
                                    <li>• <strong>Vacinas, cirurgias e tratamento dentário:</strong> o prazo varia. Na dúvida, ligue para o hemocentro.</li>
                                </ul>
                                <p class="text-xs text-gray-500 mt-3">Essas restrições existem para proteger você e quem vai receber o sangue. A triagem do hemocentro dá a palavra final.</p>
                            </details>

                            <details class="group p-4">
                                <summary class="flex justify-between items-center cursor-pointer list-none font-semibold text-gray-900">Tenho direito a folga no trabalho?<span class="text-red-500 transition group-open:rotate-45 text-xl leading-none">+</span></summary>
                                <p class="text-sm text-gray-600 mt-3"><strong>Sim.</strong> Pela CLT (art. 473), você pode faltar <strong>1 dia a cada 12 meses</strong> para doar sangue, sem desconto no salário. Peça a declaração de comparecimento no hemocentro.</p>
                            </details>

                            <details class="group p-4">
                                <summary class="flex justify-between items-center cursor-pointer list-none font-semibold text-gray-900">Qual tipo sanguíneo é mais necessário?<span class="text-red-500 transition group-open:rotate-45 text-xl leading-none">+</span></summary>
                                <p class="text-sm text-gray-600 mt-3"><strong>Todos!</strong> Os estoques de todos os tipos precisam ser repostos sempre. O tipo <strong>O negativo</strong> é o doador universal e é usado em emergências, quando não dá tempo de saber o tipo do paciente. Não sabe o seu tipo? Ele é identificado na sua primeira doação.</p>
                            </details>

                        </div>
                    </section>

                    <!-- Mensagem de incentivo -->
                    <section class="bg-red-50 border border-red-100 rounded-xl p-5 text-center">
                        <p class="font-bold text-gray-900">Ainda com dúvida? Vá mesmo assim.</p>
                        <p class="text-sm text-gray-600 mt-1">A equipe do hemocentro conversa com você antes de tudo e responde qualquer pergunta. Na pior das hipóteses, você descobre o que precisa para doar na próxima vez.</p>
                    </section>
                </div>
            </div>

            <!-- Rodapé com chamadas para ação -->
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-2xl shrink-0 flex flex-col sm:flex-row gap-3 sm:justify-center">
                <button onclick="fecharFaq()" class="bg-red-600 hover:bg-red-700 text-white font-bold text-sm px-5 py-2.5 rounded-lg shadow-sm transition">
                    Encontrar um hemocentro no mapa
                </button>
                @guest('web')
                    @guest('doador')
                        @guest('hemocentro')
                            <a href="{{ url('/register/doador') }}" class="text-center bg-white border border-red-200 text-red-600 hover:bg-red-50 font-bold text-sm px-5 py-2.5 rounded-lg transition">
                                Quero ser doador
                            </a>
                        @endguest
                    @endguest
                @endguest
            </div>
        </div>
    </div>
    
    <!-- ========================================== -->
    <!-- FIM DA HU08                                -->
    <!-- ========================================== -->

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    <script>
        function fecharFaq() {
            document.getElementById('faqModal').classList.add('hidden');
        }
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') fecharFaq();
        });

        document.addEventListener('DOMContentLoaded', function () {
            // 1. Mapa começa no RS; depois é ajustado aos hemocentros ou à localização do usuário
            const map = L.map('map').setView([-28.4489, -52.7306], 12);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // 2. Ícones em SVG: gota de sangue no pino (vermelho = ativo, âmbar = em breve)
            function pinIcon(cor) {
                return L.divIcon({
                    className: 'pin-icon',
                    html: `<svg width="34" height="44" viewBox="0 0 34 44" style="filter: drop-shadow(0 2px 2px rgba(0,0,0,.35))">
                               <path d="M17 1C8.2 1 1 8.1 1 16.9 1 28.6 17 43 17 43s16-14.4 16-26.1C33 8.1 25.8 1 17 1z" fill="${cor}" stroke="#fff" stroke-width="2"/>
                               <path d="M17 8.5s-6 6.6-6 10.6a6 6 0 0 0 12 0c0-4-6-10.6-6-10.6z" fill="#fff"/>
                           </svg>`,
                    iconSize: [34, 44],
                    iconAnchor: [17, 43],
                    popupAnchor: [0, -38],
                });
            }
            const hemoIcon = pinIcon('#dc2626');
            const emBreveIcon = pinIcon('#f59e0b');
            const userIcon = L.divIcon({ className: 'pin-icon', html: '<div class="user-dot"></div>', iconSize: [18, 18], iconAnchor: [9, 9] });

            // 3. Hemocentros do banco
            const hemocentros = @json($hemocentros);
            const marcadores = [];

            hemocentros.forEach(function(hemo) {
                if (!hemo.latitude || !hemo.longitude) return;

                const emBreve = hemo.email && hemo.email.includes('precadastro_');

                let btnZap = '';
                if (hemo.telefone) {
                    const numeroZap = hemo.telefone.replace(/\D/g, '');
                    btnZap = `<a href="https://api.whatsapp.com/send?phone=55${numeroZap}" target="_blank" rel="noopener" class="popup-btn mt-2 block w-full bg-green-500 hover:bg-green-600 font-bold py-1.5 px-2 rounded text-xs no-underline">Chamar no WhatsApp</a>`;
                }

                const marcador = L.marker([hemo.latitude, hemo.longitude], { icon: emBreve ? emBreveIcon : hemoIcon })
                    .addTo(map)
                    .bindPopup(`
                        <div class="font-sans">
                            <p class="font-bold text-gray-900 text-sm !m-0">${hemo.nome}</p>
                            ${emBreve ? '<span class="inline-block mt-1 bg-amber-100 text-amber-700 text-[10px] font-bold uppercase px-2 py-0.5 rounded-full">Em breve</span>' : ''}
                            <p class="text-gray-600 text-xs !mt-1 !mb-0">${hemo.endereco}</p>
                            ${btnZap}
                            <a href="/agendar" class="popup-btn mt-2 block w-full bg-red-600 hover:bg-red-700 font-bold py-1.5 px-2 rounded text-xs no-underline">Agendar Doação</a>
                        </div>
                    `);
                marcadores.push(marcador);
            });

            // Sem localização do usuário, enquadra todos os hemocentros na tela
            function enquadrarHemocentros() {
                if (marcadores.length) {
                    map.fitBounds(L.featureGroup(marcadores).getBounds(), { padding: [60, 60], maxZoom: 14 });
                }
            }
            enquadrarHemocentros();

            // 4. Localização do usuário
            const aviso = document.getElementById('avisoLocal');
            let marcadorUsuario = null;

            function mostrarAviso(texto) {
                aviso.textContent = texto;
                aviso.classList.remove('hidden');
                clearTimeout(aviso._timer);
                aviso._timer = setTimeout(() => aviso.classList.add('hidden'), 5000);
            }

            function localizar() {
                if (!('geolocation' in navigator)) {
                    mostrarAviso('Seu navegador não suporta localização.');
                    return;
                }
                if (!window.isSecureContext) {
                    mostrarAviso('A localização só funciona em HTTPS ou em localhost.');
                    return;
                }

                navigator.geolocation.getCurrentPosition(function(position) {
                    const latlng = [position.coords.latitude, position.coords.longitude];

                    if (marcadorUsuario) {
                        marcadorUsuario.setLatLng(latlng);
                    } else {
                        marcadorUsuario = L.marker(latlng, { icon: userIcon, zIndexOffset: 1000 })
                            .addTo(map)
                            .bindPopup('<b>Você está aqui</b>');
                    }
                    map.flyTo(latlng, 14);
                }, function(erro) {
                    const mensagens = {
                        1: 'Permissão de localização negada. Libere nas configurações do navegador.',
                        2: 'Não foi possível obter sua localização.',
                        3: 'A localização demorou demais para responder.',
                    };
                    mostrarAviso(mensagens[erro.code] || 'Erro ao obter localização.');
                }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 });
            }

            document.getElementById('btnLocalizar').addEventListener('click', localizar);
            localizar();
        });
    </script>
</body>
</html>