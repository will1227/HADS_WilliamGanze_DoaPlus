@extends('layouts.painel')

@section('titulo', 'Painel de Administração')

@section('conteudo')
<div class="max-w-5xl mx-auto space-y-8">
    
    <!-- Mensagens de Sucesso ou Erro -->
    @if (session('status'))
        <div class="font-medium text-sm text-green-700 bg-green-50 p-4 rounded-lg border border-green-200 flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            {{ session('status') }}
        </div>
    @endif
    @if (session('erro'))
        <div class="font-medium text-sm text-red-700 bg-red-50 p-4 rounded-lg border border-red-200 flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
            {{ session('erro') }}
        </div>
    @endif

    <!-- SEÇÃO 1: Fila de Aprovações -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Fila de Reivindicações</h3>
            <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-1 rounded-full">{{ $pedidos->count() }} pendente(s)</span>
        </div>
        
        <div class="divide-y divide-gray-100">
            @forelse($pedidos as $pedido)
                <div class="p-6 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 hover:bg-gray-50 transition">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-0.5 rounded uppercase tracking-wider">Aguardando Avaliação</span>
                            <span class="text-sm text-gray-500">CNPJ: {{ $pedido->cnpj }}</span>
                        </div>
                        <h4 class="text-lg font-bold text-gray-900 mt-2">Instituição: {{ $pedido->nome }}</h4>
                        <p class="text-gray-600 text-sm mt-1">E-mail: {{ $pedido->email }} | Tel: {{ $pedido->telefone }}</p>
                        
                        <div class="mt-3 p-3 bg-blue-50 border border-blue-100 rounded-lg flex items-center gap-3">
                            <svg class="w-5 h-5 text-blue-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <p class="text-sm text-blue-800">
                                Deseja assumir o ponto no mapa: <strong>{{ $pedido->localReivindicado->nome ?? 'Local não encontrado' }}</strong>
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex gap-2 w-full lg:w-auto">
                        <form method="POST" action="{{ route('admin.rejeitar', $pedido->id) }}" class="flex-1 lg:flex-none">
                            @csrf
                            <button type="submit" class="w-full bg-white text-red-600 border border-red-200 px-4 py-2 rounded-lg font-medium hover:bg-red-50 transition">Rejeitar</button>
                        </form>
                        <form method="POST" action="{{ route('admin.aprovar', $pedido->id) }}" class="flex-1 lg:flex-none">
                            @csrf
                            <button type="submit" class="w-full bg-green-600 text-white px-5 py-2 rounded-lg font-medium hover:bg-green-700 shadow-sm transition">Aprovar e Unir</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-10 text-center flex flex-col items-center">
                    <div class="w-16 h-16 bg-gray-50 text-gray-400 rounded-full flex items-center justify-center mb-4 border border-gray-100">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900">Tudo em dia!</h4>
                    <p class="text-sm text-gray-500 mt-1">Nenhuma solicitação de reivindicação pendente no momento.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- SEÇÃO 2: Formulário de Pré-Cadastro (Já existente) -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Adicionar Novo Hemocentro Provisório</h3>
            <span class="text-xs bg-gray-200 text-gray-700 font-bold px-2 py-1 rounded uppercase">Mapa Público</span>
        </div>
        
        <form method="POST" action="{{ route('admin.precadastro') }}" class="p-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome da Instituição</label>
                    <input type="text" name="nome" required placeholder="Ex: Hemocentro Regional de Passo Fundo" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm text-sm">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Endereço Completo</label>
                    <input type="text" name="endereco" required placeholder="Ex: Avenida Sete de Setembro, 1055, Passo Fundo - RS" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm text-sm">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefone de Contato</label>
                    <input type="text" name="telefone" required placeholder="(54) 3311-0000" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm text-sm">
                </div>

                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <h4 class="text-sm font-semibold text-gray-700">Coordenadas Geográficas</h4>
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

            <div class="mt-6 flex justify-end">
                <button type="submit" class="w-full md:w-auto bg-blue-600 text-white px-6 py-2.5 rounded-lg font-medium hover:bg-blue-700 shadow-sm transition flex justify-center items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                    Publicar no Mapa
                </button>
            </div>
        </form>
    </div>
</div>
@endsection