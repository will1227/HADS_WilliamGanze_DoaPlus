<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>DoaPlus - Salve Vidas</title>
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-50">
        <!-- Barra do topo (mesmo padrão da tela do mapa) -->
        <nav class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-6 shadow-sm">
            <a href="/" class="flex items-center gap-2">
                <x-application-logo class="w-8 h-8 text-red-600" />
                <span class="text-2xl font-bold text-gray-800 tracking-tight">Doa<span class="text-red-600">Plus</span></span>
            </a>
            <a href="/" class="text-sm font-medium text-gray-700 hover:text-red-600 transition flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Voltar ao mapa
            </a>
        </nav>

        <main class="min-h-[calc(100dvh-4rem)] flex items-center justify-center px-4 py-10">
            <div class="w-full sm:max-w-md bg-white rounded-xl border border-gray-200 px-6 sm:px-8 py-8">
                {{ $slot }}
            </div>
        </main>
    </body>
</html>
