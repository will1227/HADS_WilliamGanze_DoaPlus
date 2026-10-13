<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Painel - DoaPlus</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 antialiased h-screen flex overflow-hidden">

    <!-- OVERLAY ESCURO PARA MOBILE -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden transition-opacity" onclick="toggleSidebar()"></div>

    <!-- BARRA LATERAL (SIDEBAR) RESPONSIVA -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 flex flex-col transform -translate-x-full md:relative md:translate-x-0 transition-transform duration-300 ease-in-out">
        
        <!-- Logo e Botão de Fechar (Mobile) -->
        <div class="h-16 flex items-center justify-between px-6 border-b border-gray-200">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-red-600 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                </svg>
                <span class="text-xl font-bold tracking-tight">Doa<span class="text-red-600">Plus</span></span>
            </div>
            <button onclick="toggleSidebar()" class="md:hidden text-gray-500 hover:text-red-600 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Menu de Navegação -->
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
            
            <!-- MENU DO ADMIN -->
            @if(Auth::guard('web')->check())
                <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Administração</p>
                <a href="#" class="flex items-center px-3 py-2 bg-blue-50 text-blue-700 rounded-md group font-medium">
                    <svg class="w-5 h-5 mr-3 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Pré-cadastrar Locais
                </a>
                <a href="#" class="flex items-center px-3 py-2 text-gray-600 hover:bg-gray-100 rounded-md group font-medium mt-1">
                    <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Aprovar Contas <span class="ml-auto text-[10px] bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full">Breve</span>
                </a>
                <a href="#" class="flex items-center px-3 py-2 text-gray-600 hover:bg-gray-100 rounded-md group font-medium mt-1">
                    <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    Usuários <span class="ml-auto text-[10px] bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full">Breve</span>
                </a>

            <!-- MENU DO DOADOR -->
            @elseif(Auth::guard('doador')->check())
                <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Meu Painel</p>
                <a href="#" class="flex items-center px-3 py-2 bg-red-50 text-red-700 rounded-md group font-medium">
                    <svg class="w-5 h-5 mr-3 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Resumo
                </a>

            <!-- MENU DO HEMOCENTRO -->
            @elseif(Auth::guard('hemocentro')->check())
                <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Institucional</p>
                <a href="#" class="flex items-center px-3 py-2 bg-green-50 text-green-700 rounded-md group font-medium">
                    <svg class="w-5 h-5 mr-3 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    Visão Geral
                </a>
            @endif
        </nav>
    </aside>

    <!-- CONTEÚDO PRINCIPAL -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden w-full">
        
        <!-- Navbar Superior (Cabeçalho) -->
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 md:px-8 shadow-sm">
            <div class="flex items-center gap-3">
                <!-- Botão Menu Hambúrguer (Mobile) -->
                <button onclick="toggleSidebar()" class="md:hidden text-gray-600 hover:text-red-600 focus:outline-none">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <h1 class="text-lg md:text-xl font-semibold text-gray-800 truncate max-w-[150px] md:max-w-none">
                    @if(Auth::guard('web')->check()) Administração
                    @elseif(Auth::guard('doador')->check()) Área do Doador
                    @elseif(Auth::guard('hemocentro')->check()) Institucional
                    @endif
                </h1>
            </div>

            <div class="flex items-center gap-4 md:gap-6">
                <!-- Esconde o texto no mobile, mostra apenas no Desktop -->
                <a href="{{ url('/') }}" class="text-sm font-medium text-gray-500 hover:text-red-600 transition flex items-center gap-1" title="Voltar ao Mapa">
                    <svg class="w-5 h-5 md:w-4 md:h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span class="hidden md:inline">Voltar ao Mapa</span>
                </a>
                
                <div class="border-l border-gray-300 h-6"></div>

                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold uppercase shrink-0">
                        @if(Auth::guard('web')->check()) {{ substr(Auth::guard('web')->user()->name, 0, 1) }}
                        @elseif(Auth::guard('doador')->check()) {{ substr(Auth::guard('doador')->user()->nome, 0, 1) }}
                        @elseif(Auth::guard('hemocentro')->check()) {{ substr(Auth::guard('hemocentro')->user()->nome, 0, 1) }}
                        @endif
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-800 transition">Sair</button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Área de Conteúdo Rolável -->
        <!-- Reduzido o padding lateral no mobile (p-4) e aumentado no desktop (md:p-8) -->
        <main class="flex-1 overflow-y-auto p-4 md:p-8 bg-gray-50">
            
            <!-- CONTEÚDO: ADMIN -->
            @if(Auth::guard('web')->check())
                <div class="max-w-3xl mx-auto md:mx-0">
                    <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-6">Adicionar Novo Hemocentro ao Mapa</h2>
                    
                    @if (session('status'))
                        <div class="mb-6 font-medium text-sm text-green-700 bg-green-50 p-4 rounded-lg border border-green-200 flex items-center gap-2">
                            <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-4 md:px-6 py-4 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row md:justify-between md:items-center gap-2">
                            <h3 class="text-lg font-semibold text-gray-800">Dados do Pré-cadastro</h3>
                            <span class="text-xs bg-blue-100 text-blue-800 font-bold px-2 py-1 rounded uppercase w-fit">Público no Mapa</span>
                        </div>
                        
                        <form method="POST" action="{{ route('admin.precadastro') }}" class="p-4 md:p-6">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome da Instituição</label>
                                    <input type="text" name="nome" required placeholder="Ex: Hemocentro Regional..." class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm text-sm">
                                </div>
                                
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Endereço Completo</label>
                                    <input type="text" name="endereco" required placeholder="Ex: Av. Sete de Setembro, 1055..." class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm text-sm">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefone de Contato</label>
                                    <input type="text" name="telefone" required placeholder="(00) 0000-0000" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm text-sm">
                                </div>

                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="md:col-span-2">
                                        <h4 class="text-sm font-semibold text-gray-700">Coordenadas Geográficas</h4>
                                        <p class="text-xs text-gray-500 mb-1">Necessárias para posicionar o pino vermelho no mapa.</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
                                        <input type="text" name="latitude" required placeholder="Ex: -28.261298" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
                                        <input type="text" name="longitude" required placeholder="Ex: -52.408332" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm text-sm">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 md:mt-8 flex justify-end">
                                <button type="submit" class="w-full md:w-auto bg-blue-600 text-white px-6 py-2.5 rounded-lg font-medium hover:bg-blue-700 shadow-sm transition-colors flex justify-center items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                                    Publicar no Mapa
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            <!-- CONTEÚDO: DOADOR -->
            @elseif(Auth::guard('doador')->check())
                <div class="bg-white p-6 md:p-8 rounded-xl shadow-sm border border-gray-200 text-center max-w-lg mx-auto mt-6 md:mt-10">
                    <h2 class="text-lg md:text-xl font-bold text-gray-800">Olá, {{ Auth::guard('doador')->user()->nome }}!</h2>
                    <p class="text-sm md:text-base text-gray-500 mt-2">A interface do doador será construída aqui.</p>
                </div>

            <!-- CONTEÚDO: HEMOCENTRO -->
            @elseif(Auth::guard('hemocentro')->check())
                <div class="bg-white p-6 md:p-8 rounded-xl shadow-sm border border-gray-200 text-center max-w-lg mx-auto mt-6 md:mt-10">
                    <h2 class="text-lg md:text-xl font-bold text-gray-800">{{ Auth::guard('hemocentro')->user()->nome }}</h2>
                    <p class="text-sm md:text-base text-gray-500 mt-2">Status da conta: <strong>{{ Auth::guard('hemocentro')->user()->status }}</strong></p>
                </div>
            @endif

        </main>
    </div>

    <!-- Script para gerenciar a Sidebar no Mobile -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            // Alterna a classe de translação para mostrar/esconder a sidebar
            sidebar.classList.toggle('-translate-x-full');
            // Alterna a visibilidade do overlay escuro
            overlay.classList.toggle('hidden');
        }
    </script>
</body>
</html>