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
            <!-- Botão para a futura tela de edição -->
            <a href="#" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition shadow-sm">
                Editar Meus Dados
            </a>
        </div>
    </div>

    <!-- Seção de Gamificação (Resumo) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex items-center gap-4 border-l-4 border-l-red-500">
            <div class="p-3 bg-red-50 text-red-600 rounded-lg">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Vidas Salvas</p>
                <p class="text-2xl font-bold text-gray-900">0</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex items-center gap-4 border-l-4 border-l-yellow-400">
            <div class="p-3 bg-yellow-50 text-yellow-600 rounded-lg">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Medalhas</p>
                <p class="text-2xl font-bold text-gray-900">Iniciante</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex items-center gap-4 border-l-4 border-l-blue-500">
            <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Próxima Doação</p>
                <p class="text-lg font-bold text-gray-900">Disponível</p>
            </div>
        </div>
    </div>

    <!-- Histórico de Doações -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Últimas Doações</h3>
            <a href="#" class="text-sm text-red-600 hover:underline font-medium">Ver Histórico Completo</a>
        </div>
        
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
    </div>

</div>
@endsection