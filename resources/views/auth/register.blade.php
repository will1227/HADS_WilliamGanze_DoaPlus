<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Criar conta</h2>
        <p class="text-sm text-gray-500 mt-1">Escolha o tipo de conta.</p>
    </div>

    <div class="space-y-3">
        <a href="{{ url('/register/doador') }}" class="flex items-center gap-4 border border-gray-200 rounded-lg p-4 hover:border-red-500 hover:bg-red-50/40 transition">
            <svg class="w-7 h-7 text-red-600 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
            </svg>
            <div class="flex-1">
                <p class="font-semibold text-gray-900">Sou doador</p>
                <p class="text-sm text-gray-500">Agendar doações, acompanhar quando posso doar de novo e ganhar medalhas.</p>
            </div>
            <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </a>

        <a href="{{ url('/register/hemocentro') }}" class="flex items-center gap-4 border border-gray-200 rounded-lg p-4 hover:border-red-500 hover:bg-red-50/40 transition">
            <svg class="w-7 h-7 text-gray-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <div class="flex-1">
                <p class="font-semibold text-gray-900">Sou hemocentro</p>
                <p class="text-sm text-gray-500">Gerenciar agendamentos e registrar doações da instituição.</p>
            </div>
            <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    <div class="mt-6 text-center text-sm text-gray-600 border-t border-gray-100 pt-5">
        Já tem conta?
        <a href="{{ route('login') }}" class="text-red-600 font-semibold hover:underline">Entrar</a>
    </div>
</x-guest-layout>
