@extends('layouts.painel')

@section('titulo', 'Painel Institucional')

@section('conteudo')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="bg-white p-6 md:p-8 rounded-xl shadow-sm border border-gray-200 flex flex-col md:flex-row items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center font-bold text-2xl uppercase shrink-0">
                {{ substr(Auth::guard('hemocentro')->user()->nome, 0, 1) }}
            </div>
            <div class="text-center md:text-left">
                <h2 class="text-xl font-bold text-gray-900">{{ Auth::guard('hemocentro')->user()->nome }}</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Status da conta: 
                    @if(Auth::guard('hemocentro')->user()->status === 'pendente')
                        <span class="inline-block uppercase font-bold text-xs bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded ml-1">Aguardando Aprovação</span>
                    @else
                        <span class="inline-block uppercase font-bold text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded ml-1">Ativa</span>
                    @endif
                </p>
            </div>
        </div>
        <div>
            <a href="#" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition shadow-sm w-full md:w-auto block text-center">
                Editar Dados da Instituição
            </a>
        </div>
    </div>
</div>
@endsection