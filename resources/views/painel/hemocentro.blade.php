@extends('layouts.painel')

@section('titulo', 'Painel Institucional')

@section('conteudo')
<div class="max-w-5xl mx-auto space-y-6">

    @if (session('status'))
        <div class="font-medium text-sm text-green-700 bg-green-50 p-4 rounded-lg border border-green-200">
            {{ session('status') }}
        </div>
    @endif

    <div class="bg-white p-6 md:p-8 rounded-xl shadow-sm border border-gray-200 flex flex-col md:flex-row items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center font-bold text-2xl uppercase shrink-0">
                {{ substr($hemocentro->nome, 0, 1) }}
            </div>
            <div class="text-center md:text-left">
                <h2 class="text-xl font-bold text-gray-900">{{ $hemocentro->nome }}</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Status da conta: 
                    @if($hemocentro->status === 'pendente')
                        <span class="inline-block uppercase font-bold text-xs bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded ml-1">Aguardando Aprovação</span>
                    @else
                        <span class="inline-block uppercase font-bold text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded ml-1">Ativa</span>
                    @endif
                </p>
            </div>
        </div>
        <div>
            <a href="{{ route('hemocentro.perfil') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition shadow-sm w-full md:w-auto block text-center">
                Editar Dados da Instituição
            </a>
        </div>
    </div>

    <!-- Lógica de Exibição Baseada no Status -->
    @if($hemocentro->status === 'pendente')
        
        @if($hemocentro->fk_reivindicacao_id)
            <!-- Já pediu reivindicação -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-8 text-center mt-6">
                <svg class="w-16 h-16 text-blue-500 mx-auto mb-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h3 class="text-xl font-bold text-blue-900">Em Análise pelo Administrador</h3>
                <p class="text-blue-700 mt-2 text-lg">Você solicitou a reivindicação do ponto <strong>{{ $hemocentro->localReivindicado->nome ?? 'no mapa' }}</strong>.</p>
                <p class="text-sm text-blue-600 mt-2 max-w-lg mx-auto">Nossa equipa está verificando a correspondência do seu CNPJ com a instituição. Assim que aprovado, a sua conta assumirá o ponto no mapa e o painel será liberado.</p>
            </div>
        @else
            <!-- Precisa escolher um local no mapa -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mt-6">
                <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-800">Associe a sua Instituição a um Ponto no Mapa</h3>
                    <p class="text-sm text-gray-500 mt-1">Para ativar a sua conta, localize na lista abaixo o ponto provisório cadastrado pelo sistema que corresponde à sua unidade.</p>
                </div>
                
                <div class="divide-y divide-gray-100">
                    @forelse($locaisProvisorios as $local)
                        <div class="p-6 flex flex-col md:flex-row items-center justify-between gap-4 hover:bg-gray-50 transition">
                            <div>
                                <h4 class="font-bold text-gray-900 text-lg">{{ $local->nome }}</h4>
                                <p class="text-sm text-gray-500 mt-1 flex items-center gap-1">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    {{ $local->endereco }}
                                </p>
                            </div>
                            <form action="{{ route('hemocentro.reivindicar') }}" method="POST">
                                @csrf
                                <input type="hidden" name="fk_reivindicacao_id" value="{{ $local->id }}">
                                <button type="submit" class="bg-green-600 text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-green-700 transition shadow-sm w-full md:w-auto">
                                    Reivindicar este Local
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="p-8 text-center flex flex-col items-center">
                            <div class="w-12 h-12 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <p class="text-gray-600 font-medium">Nenhum ponto provisório disponível.</p>
                            <p class="text-sm text-gray-500 mt-1">Se a sua instituição não estiver no mapa, entre em contato com o suporte.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

    @else
        <!-- Dashboard Ativo (Para o futuro) -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-10 text-center mt-6">
            <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-5">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">Conta Ativa e Unificada!</h3>
            <p class="text-gray-500 mt-2 text-lg">A sua instituição já está visível e operante no mapa para todos os doadores.</p>
        </div>
    @endif

</div>
@endsection