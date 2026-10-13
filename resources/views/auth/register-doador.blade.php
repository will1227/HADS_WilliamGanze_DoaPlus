<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Cadastro de Doador</h2>
        <p class="text-sm text-gray-600 mt-1">Preencha seus dados reais. O peso mínimo é 50kg e a idade permitida é de 16 a 69 anos.</p>
    </div>

    <!-- Exibição de erros corrigida -->
    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-600 text-sm rounded-md">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('doadores.store') }}" id="formDoador">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label for="nome" class="block text-sm font-medium text-gray-700">Nome Completo</label>
                <input id="nome" type="text" name="nome" value="{{ old('nome') }}" required class="mt-1 block w-full border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
            </div>

            <div>
                <label for="cpf" class="block text-sm font-medium text-gray-700">CPF</label>
                <input id="cpf" type="text" name="cpf" value="{{ old('cpf') }}" required maxlength="14" placeholder="000.000.000-00" oninput="maskCpf(this)" class="mt-1 block w-full border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
            </div>

            <div>
                <label for="data_nascimento" class="block text-sm font-medium text-gray-700">Data de Nascimento</label>
                <input id="data_nascimento" type="date" name="data_nascimento" value="{{ old('data_nascimento') }}" required 
                       min="{{ now()->subYears(69)->format('Y-m-d') }}" 
                       max="{{ now()->subYears(16)->format('Y-m-d') }}" 
                       class="mt-1 block w-full border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
            </div>

            <div>
                <label for="sexo" class="block text-sm font-medium text-gray-700">Sexo</label>
                <select id="sexo" name="sexo" class="mt-1 block w-full border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                    <option value="">Selecione...</option>
                    <option value="Masculino">Masculino</option>
                    <option value="Feminino">Feminino</option>
                </select>
            </div>

            <div>
                <label for="peso" class="block text-sm font-medium text-gray-700">Peso (kg)</label>
                <input id="peso" type="number" step="0.1" min="50" name="peso" value="{{ old('peso') }}" required class="mt-1 block w-full border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
            </div>

            <div class="md:col-span-2">
                <label for="tipo_sanguineo" class="block text-sm font-medium text-gray-700">Tipo Sanguíneo</label>
                <select id="tipo_sanguineo" name="tipo_sanguineo" required class="mt-1 block w-full border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                    <option value="">Selecione...</option>
                    <option value="A+">A+</option><option value="A-">A-</option>
                    <option value="B+">B+</option><option value="B-">B-</option>
                    <option value="AB+">AB+</option><option value="AB-">AB-</option>
                    <option value="O+">O+</option><option value="O-">O-</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required class="mt-1 block w-full border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
            </div>

            <div class="md:col-span-2">
                <label for="senha" class="block text-sm font-medium text-gray-700">Senha (mínimo 6 caracteres)</label>
                <input id="senha" type="password" name="senha" required minlength="6" class="mt-1 block w-full border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
            </div>
        </div>

        <div class="flex items-center justify-between mt-6">
            <a href="{{ route('register') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">Voltar</a>
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                Finalizar Cadastro
            </button>
        </div>
    </form>

    <script>
        function maskCpf(input) {
            let value = input.value.replace(/\D/g, ''); 
            value = value.replace(/(\d{3})(\d)/, '$1.$2'); 
            value = value.replace(/(\d{3})(\d)/, '$1.$2'); 
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2'); 
            input.value = value;
        }

        document.getElementById('formDoador').addEventListener('submit', function(e) {
            let cpfInput = document.getElementById('cpf').value.replace(/\D/g, '');
            if(cpfInput.length !== 11) {
                e.preventDefault();
                alert('Por favor, informe um CPF válido completo.');
            }
        });
    </script>
</x-guest-layout>
