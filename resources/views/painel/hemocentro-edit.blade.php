@extends('layouts.painel')

@section('titulo', 'Editar Dados Institucionais')

@section('conteudo')
<div class="max-w-3xl mx-auto">
    
    @if (session('status'))
        <div class="mb-6 font-medium text-sm text-green-700 bg-green-50 p-4 rounded-lg border border-green-200 flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            {{ session('status') }}
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center gap-3">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            <h3 class="text-lg font-semibold text-gray-800">Informações da Instituição</h3>
        </div>
        
        <form method="POST" action="{{ route('hemocentro.perfil.update') }}" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- NOME -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Hemocentro</label>
                    <input type="text" name="nome" value="{{ old('nome', $hemocentro->nome) }}" required class="block w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 shadow-sm">
                </div>
                
                <!-- EMAIL -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">E-mail Corporativo</label>
                    <input type="email" name="email" value="{{ old('email', $hemocentro->email) }}" required class="block w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 shadow-sm">
                </div>

                <!-- TELEFONE -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefone (WhatsApp)</label>
                    <input type="text" name="telefone" value="{{ old('telefone', $hemocentro->telefone) }}" required class="block w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 shadow-sm">
                </div>

                <!-- ENDEREÇO -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Endereço Físico</label>
                    <input type="text" name="endereco" value="{{ old('endereco', $hemocentro->endereco) }}" required class="block w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 shadow-sm">
                </div>

                <!-- COORDENADAS PARA O MAPA -->
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <h4 class="text-sm font-semibold text-gray-700">Posição no Mapa (Pino)</h4>
                        <p class="text-xs text-gray-500">Atualize estas coordenadas caso a instituição mude de localização física.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
                        <input type="text" name="latitude" value="{{ old('latitude', $hemocentro->latitude) }}" required class="block w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
                        <input type="text" name="longitude" value="{{ old('longitude', $hemocentro->longitude) }}" required class="block w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 shadow-sm text-sm">
                    </div>
                </div>

                <!-- SENHA -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nova Senha</label>
                    <input type="password" name="senha" placeholder="Deixe em branco para manter a senha atual" class="block w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 shadow-sm">
                </div>

                <!-- DADOS BLOQUEADOS -->
                <div class="md:col-span-2 pt-4 mt-2 border-t border-gray-100">
                    <h4 class="text-sm font-semibold text-gray-600 mb-4">Dados Restritos</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">CNPJ</label>
                            <input type="text" value="{{ $hemocentro->cnpj }}" disabled class="block w-full border-gray-200 bg-gray-50 text-gray-500 rounded-lg shadow-sm cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Status da Conta</label>
                            <input type="text" value="{{ strtoupper($hemocentro->status) }}" disabled class="block w-full border-gray-200 bg-gray-50 text-gray-500 rounded-lg shadow-sm cursor-not-allowed font-bold">
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Alterações de CNPJ exigem aprovação do administrador do sistema (DoaPlus).</p>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('dashboard') }}" class="px-6 py-2.5 rounded-lg font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="bg-green-600 text-white px-6 py-2.5 rounded-lg font-medium hover:bg-green-700 shadow-sm transition-colors flex items-center gap-2">
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>
@endsection