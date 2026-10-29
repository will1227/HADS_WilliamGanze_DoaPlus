@extends('layouts.painel')

@section('titulo', 'Painel Institucional')

@section('conteudo')
<div class="max-w-5xl mx-auto space-y-6">

    @if (session('status'))
        <div class="font-medium text-sm text-green-700 bg-green-50 p-4 rounded-lg border border-green-200">
            {{ session('status') }}
        </div>
    @endif

    <!-- Cabeçalho do Hemocentro -->
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
        <!-- =================================================================== -->
        <!-- DASHBOARD ATIVO: GESTÃO DE AGENDAMENTOS                             -->
        <!-- =================================================================== -->
        
        @php
            // Filtra as collections enviadas pelo Controller
            $pendentes = $agendamentos->where('status', 'pendente');
            $confirmados = $agendamentos->where('status', 'confirmado');
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
            
            <!-- COLUNA 1: NOVOS PEDIDOS (PENDENTES) -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden flex flex-col">
                <div class="px-6 py-4 border-b border-gray-200 bg-yellow-50 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-yellow-900 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Novos Pedidos
                    </h3>
                    <span class="bg-yellow-200 text-yellow-800 text-xs font-bold px-2.5 py-1 rounded-full">{{ $pendentes->count() }}</span>
                </div>
                
                <div class="p-0 flex-1 bg-gray-50/50">
                    @forelse($pendentes as $agendamento)
                        <div class="p-6 border-b border-gray-200 last:border-0 bg-white m-4 rounded-lg shadow-sm">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h4 class="font-bold text-gray-900">{{ $agendamento->doador->nome }}</h4>
                                    <p class="text-sm text-gray-500 mt-0.5">Tipo Sanguíneo: <span class="font-bold text-red-600">{{ $agendamento->doador->tipo_sanguineo }}</span></p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($agendamento->data_hora)->format('d/m/Y') }}</p>
                                    <p class="text-sm text-gray-500 font-medium">{{ \Carbon\Carbon::parse($agendamento->data_hora)->format('H:i') }}</p>
                                </div>
                            </div>
                            
                            <div class="flex gap-2 mt-4">
                                <form action="{{ route('agendamentos.status', $agendamento->id) }}" method="POST" class="flex-1">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="cancelado">
                                    <button type="submit" class="w-full px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-50 transition">Recusar</button>
                                </form>
                                <form action="{{ route('agendamentos.status', $agendamento->id) }}" method="POST" class="flex-1">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="confirmado">
                                    <button type="submit" class="w-full px-3 py-2 bg-yellow-500 text-white rounded-md text-sm font-medium hover:bg-yellow-600 transition shadow-sm">Confirmar Horário</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500">
                            <p>Nenhum pedido de agendamento pendente.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- COLUNA 2: AGUARDANDO COMPARECIMENTO (CONFIRMADOS) -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden flex flex-col">
                <div class="px-6 py-4 border-b border-gray-200 bg-blue-50 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-blue-900 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Agendamentos Confirmados
                    </h3>
                    <span class="bg-blue-200 text-blue-800 text-xs font-bold px-2.5 py-1 rounded-full">{{ $confirmados->count() }}</span>
                </div>
                
                <div class="p-0 flex-1 bg-gray-50/50">
                    @forelse($confirmados as $agendamento)
                        <div class="p-6 border-b border-gray-200 last:border-0 bg-white m-4 rounded-lg shadow-sm border-l-4 border-l-blue-500">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h4 class="font-bold text-gray-900">{{ $agendamento->doador->nome }}</h4>
                                    <p class="text-sm text-gray-500 mt-0.5">Tipo: <span class="font-bold text-red-600">{{ $agendamento->doador->tipo_sanguineo }}</span> | Sexo: {{ $agendamento->doador->sexo }}</p>
                                </div>
                                <div class="text-right bg-blue-50 px-3 py-1.5 rounded-md">
                                    <p class="font-bold text-blue-900">{{ \Carbon\Carbon::parse($agendamento->data_hora)->format('d/m/Y') }}</p>
                                    <p class="text-sm text-blue-700 font-bold">{{ \Carbon\Carbon::parse($agendamento->data_hora)->format('H:i') }}</p>
                                </div>
                            </div>
                            
                            <div class="flex gap-2 mt-4 pt-4 border-t border-gray-100">
                                <form action="{{ route('agendamentos.status', $agendamento->id) }}" method="POST" class="w-1/3">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="cancelado">
                                    <button type="submit" class="w-full px-3 py-2 bg-white border border-red-200 text-red-600 rounded-md text-xs font-medium hover:bg-red-50 transition" title="Faltou ou Cancelou">
                                        Faltou
                                    </button>
                                </form>
                                <form action="{{ route('agendamentos.status', $agendamento->id) }}" method="POST" class="w-2/3">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="concluido">
                                    <button type="submit" class="w-full px-3 py-2 bg-green-600 text-white rounded-md text-sm font-medium hover:bg-green-700 transition shadow-sm flex justify-center items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                        Concluir Doação
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500">
                            <p>Nenhum agendamento confirmado para os próximos dias.</p>
                        </div>
                    @endforelse
                </div>
            </div>
            
        </div>
    @endif

</div>
@endsection