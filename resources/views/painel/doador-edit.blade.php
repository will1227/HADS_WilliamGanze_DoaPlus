@extends('layouts.painel')

@section('titulo', 'Editar Meu Perfil')

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
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            <h3 class="text-lg font-semibold text-gray-800">Informações Pessoais</h3>
        </div>
        
        <form method="POST" action="{{ route('doador.perfil.update') }}" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- NOME (Editável) -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome Completo</label>
                    <input type="text" name="nome" value="{{ old('nome', $doador->nome) }}" required class="block w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 shadow-sm">
                    @error('nome') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>
                
                <!-- EMAIL (Editável) -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                    <input type="email" name="email" value="{{ old('email', $doador->email) }}" required class="block w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 shadow-sm">
                    @error('email') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- PESO (Editável) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Peso Atual (kg)</label>
                    <input type="number" step="0.1" name="peso" value="{{ old('peso', $doador->peso) }}" required min="50" class="block w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 shadow-sm">
                    <p class="text-xs text-gray-500 mt-1">O peso mínimo para doar é 50kg.</p>
                    @error('peso') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- SENHA (Opcional) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nova Senha</label>
                    <input type="password" name="senha" placeholder="Deixe em branco para não alterar" class="block w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 shadow-sm">
                    @error('senha') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- DADOS BLOQUEADOS (Segurança) -->
                <div class="md:col-span-2 pt-4 mt-2 border-t border-gray-100">
                    <h4 class="text-sm font-semibold text-gray-600 mb-4">Dados de Registro (Inalteráveis)</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">CPF</label>
                            <input type="text" value="{{ $doador->cpf }}" disabled class="block w-full border-gray-200 bg-gray-50 text-gray-500 rounded-lg shadow-sm cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Data de Nascimento</label>
                            <input type="date" value="{{ $doador->data_nascimento }}" disabled class="block w-full border-gray-200 bg-gray-50 text-gray-500 rounded-lg shadow-sm cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Tipo Sanguíneo</label>
                            <input type="text" value="{{ $doador->tipo_sanguineo }}" disabled class="block w-full border-gray-200 bg-gray-50 text-gray-500 rounded-lg shadow-sm cursor-not-allowed text-center font-bold">
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Para alterar seu CPF, data de nascimento ou tipo sanguíneo, por favor, entre em contato com o suporte técnico.</p>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('dashboard') }}" class="px-6 py-2.5 rounded-lg font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="bg-red-600 text-white px-6 py-2.5 rounded-lg font-medium hover:bg-red-700 shadow-sm transition-colors flex items-center gap-2">
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>
@endsection