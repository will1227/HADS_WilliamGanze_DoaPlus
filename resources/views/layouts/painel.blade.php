<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo') - DoaPlus</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 antialiased h-screen flex overflow-hidden">

    <!-- OVERLAY ESCURO PARA MOBILE -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden transition-opacity" onclick="toggleSidebar()"></div>

    <!-- BARRA LATERAL (SIDEBAR) RESPONSIVA -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 flex flex-col transform -translate-x-full md:relative md:translate-x-0 transition-transform duration-300 ease-in-out">
        <div class="h-16 flex items-center justify-between px-6 border-b border-gray-200">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-red-600 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                <span class="text-xl font-bold tracking-tight">Doa<span class="text-red-600">Plus</span></span>
            </div>
            <button onclick="toggleSidebar()" class="md:hidden text-gray-500 hover:text-red-600 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
            @if(Auth::guard('web')->check())
                <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Administração</p>
                <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 bg-blue-50 text-blue-700 rounded-md group font-medium">Pré-cadastrar Locais</a>
            @elseif(Auth::guard('doador')->check())
                <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Meu Painel</p>
                <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 bg-red-50 text-red-700 rounded-md group font-medium">Resumo e Doações</a>
                <a href="#" class="flex items-center px-3 py-2 text-gray-600 hover:bg-gray-100 rounded-md group font-medium mt-1">Editar Perfil</a>
            @elseif(Auth::guard('hemocentro')->check())
                <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Institucional</p>
                <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 bg-green-50 text-green-700 rounded-md group font-medium">Visão Geral</a>
            @endif
        </nav>
    </aside>

    <!-- CONTEÚDO PRINCIPAL -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden w-full">
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 md:px-8 shadow-sm">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="md:hidden text-gray-600 hover:text-red-600 focus:outline-none">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <h1 class="text-lg md:text-xl font-semibold text-gray-800 truncate">@yield('titulo')</h1>
            </div>

            <div class="flex items-center gap-4 md:gap-6">
                <a href="{{ url('/') }}" class="text-sm font-medium text-gray-500 hover:text-red-600 transition flex items-center gap-1">Voltar ao Mapa</a>
                <div class="border-l border-gray-300 h-6"></div>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-800 transition">Sair</button>
                </form>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-4 md:p-8 bg-gray-50">
            @yield('conteudo')
        </main>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('-translate-x-full');
            document.getElementById('sidebarOverlay').classList.toggle('hidden');
        }
    </script>
</body>
</html>