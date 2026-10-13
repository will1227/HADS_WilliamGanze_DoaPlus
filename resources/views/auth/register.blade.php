<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-800">Crie sua conta no DoaPlus</h2>
        <p class="text-gray-600 mt-2">Como você deseja se cadastrar em nossa plataforma?</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Cartão: Sou Doador -->
        <a href="{{ url('/register/doador') }}" class="group block bg-white border border-gray-200 rounded-xl p-6 text-center hover:border-red-500 hover:shadow-lg transition-all duration-300">
            <div class="mx-auto flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-4 group-hover:bg-red-600 transition-colors duration-300">
                <!-- Ícone de Coração/Gota -->
                <svg class="w-8 h-8 text-red-600 group-hover:text-white transition-colors duration-300" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Sou Doador</h3>
            <p class="text-sm text-gray-500">Quero me cadastrar para doar sangue, agendar doações e ganhar medalhas.</p>
            <div class="mt-4 text-red-600 font-semibold text-sm group-hover:underline">
                Criar conta de Doador &rarr;
            </div>
        </a>

        <!-- Cartão: Sou Hemocentro -->
        <a href="{{ url('/register/hemocentro') }}" class="group block bg-white border border-gray-200 rounded-xl p-6 text-center hover:border-red-500 hover:shadow-lg transition-all duration-300">
            <div class="mx-auto flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4 group-hover:bg-red-600 transition-colors duration-300">
                <!-- Ícone de Hospital/Prédio -->
                <svg class="w-8 h-8 text-gray-600 group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Sou Hemocentro</h3>
            <p class="text-sm text-gray-500">Represento uma instituição e quero gerenciar agendamentos e alertas.</p>
            <div class="mt-4 text-gray-600 font-semibold text-sm group-hover:text-red-600 group-hover:underline transition-colors duration-300">
                Criar conta Institucional &rarr;
            </div>
        </a>
    </div>

    <div class="mt-8 text-center text-sm text-gray-600">
        Já possui uma conta? 
        <a href="{{ route('login') }}" class="text-red-600 font-semibold hover:underline">
            Faça login aqui
        </a>
    </div>
</x-guest-layout>
