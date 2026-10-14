@extends('layouts.painel')

@section('titulo', 'Área do Doador')

@section('conteudo')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Cabeçalho do Doador -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex flex-col md:flex-row items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center font-bold text-2xl uppercase">
                {{ substr($doador->nome, 0, 1) }}
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900">Olá, {{ explode(' ', $doador->nome)[0] }}!</h2>
                <p class="text-sm text-gray-500">Tipo Sanguíneo: <span class="font-bold text-red-600">{{ $doador->tipo_sanguineo }}</span></p>
            </div>
        </div>
        <div>
            <a href="{{ route('doador.perfil') }}" class="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition shadow-sm">Editar Perfil</a>
        </div>
    </div>

    <!-- Seção de Gamificação Dinâmica -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Vidas Salvas (1 doação = até 4 vidas) -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex items-center gap-4">
            <div class="p-3 bg-red-50 text-red-600 rounded-lg">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Vidas Salvas</p>
                <p class="text-2xl font-bold text-gray-900">{{ $doador->doacoes->count() * 4 }}</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex items-center gap-4">
            <div class="p-3 bg-yellow-50 text-yellow-600 rounded-lg">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Medalhas</p>
                <p class="text-2xl font-bold text-gray-900">{{ $doador->doacoes->count() > 0 ? 'Doador Ativo' : 'Iniciante' }}</p>
            </div>
        </div>

        <!-- Algoritmo de Aptidão em Ação -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex items-center gap-4">
            <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Próxima Doação</p>
                @if($doador->pode_doar)
                    <p class="text-lg font-bold text-green-600">Apto para Doar!</p>
                @else
                    <p class="text-lg font-bold text-gray-900">{{ \Carbon\Carbon::parse($doador->data_proxima_doacao)->format('d/m/Y') }}</p>
                    <p class="text-xs text-red-500">Em intervalo de descanso</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Histórico de Doações Dinâmico -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Últimas Doações</h3>
        </div>
        
        <div class="p-0">
            @forelse($doador->doacoes()->with('hemocentro')->orderBy('data_coleta', 'desc')->take(5)->get() as $doacao)
                <div class="flex items-center justify-between p-6 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-red-100 text-red-600 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                        </div>
                        <div>
                            <p class="font-bold text-gray-800">{{ $doacao->hemocentro->nome ?? 'Hemocentro Parceiro' }}</p>
                            <p class="text-sm text-gray-500">Doação de Sangue Tradicional</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($doacao->data_coleta)->format('d/m/Y') }}</p>
                        <span class="inline-block px-2 py-1 bg-green-100 text-green-800 text-xs font-bold rounded-full mt-1">Concluída</span>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center flex flex-col items-center justify-center">
                    <div class="w-16 h-16 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900">Nenhuma doação registrada ainda</h4>
                    <p class="text-sm text-gray-500 mt-1 max-w-sm">Quando você realizar uma doação em um hemocentro parceiro, ela aparecerá aqui e você ganhará pontos!</p>
                    <a href="{{ url('/') }}" class="mt-4 px-5 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition">
                        Encontrar um Hemocentro
                    </a>
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection