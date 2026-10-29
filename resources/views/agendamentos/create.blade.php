@extends('layouts.painel')

@section('titulo', 'Agendar Doação')

@section('conteudo')
<div class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow-sm border border-gray-200">
    <div class="mb-6 border-b border-gray-100 pb-4">
        <h2 class="text-2xl font-bold text-gray-800">Novo Agendamento</h2>
        <p class="text-gray-500 mt-1">Selecione o hemocentro, a data e o horário da sua próxima doação.</p>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $erro)
                    <li>{{ $erro }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('agendamentos.store') }}" method="POST">
        @csrf
        <div class="space-y-6">
            <!-- Seleção de Hemocentro -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Hemocentro Disponível</label>
                <select name="fk_hemocentro_id" required class="block w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                    <option value="" disabled selected>Selecione um local...</option>
                    @foreach($hemocentros as $local)
                        <option value="{{ $local->id }}">{{ $local->nome }} ({{ $local->endereco }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Data -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data da Doação</label>
                    <input type="date" name="data" min="{{ date('Y-m-d') }}" required class="block w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                </div>
                
                <!-- Hora -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Horário Preferencial</label>
                    <input type="time" name="hora" required class="block w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                </div>
            </div>
        </div>

        <div class="mt-8 flex justify-end gap-4">
            <a href="{{ route('dashboard') }}" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition">Cancelar</a>
            <button type="submit" class="px-5 py-2.5 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition">Confirmar Agendamento</button>
        </div>
    </form>
</div>
@endsection