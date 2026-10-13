@extends('layouts.painel')

@section('titulo', 'Painel de Administração')

@section('conteudo')
<div class="max-w-4xl mx-auto">
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
                        <h4 class="text-sm font-semibold text-gray-700">Coordenadas Geográficas (Leaflet)</h4>
                        <p class="text-xs text-gray-500 mb-1">Necessárias para posicionar o pino vermelho no mapa da página inicial.</p>
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
@endsection