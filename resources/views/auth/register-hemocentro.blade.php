<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Cadastro de Instituição</h2>
        <p class="text-sm text-gray-600 mt-1">Preencha os dados do Hemocentro. O status inicial será pendente de aprovação.</p>
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

    <form method="POST" action="{{ route('hemocentros.store') }}" id="formHemo">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label for="nome" class="block text-sm font-medium text-gray-700">Nome da Instituição</label>
                <input id="nome" type="text" name="nome" value="{{ old('nome') }}" required class="mt-1 block w-full border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
            </div>

            <div>
                <label for="cnpj" class="block text-sm font-medium text-gray-700">CNPJ</label>
                <input id="cnpj" type="text" name="cnpj" value="{{ old('cnpj') }}" required maxlength="18" placeholder="00.000.000/0000-00" oninput="maskCnpj(this)" class="mt-1 block w-full border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
            </div>

            <div>
                <label for="telefone" class="block text-sm font-medium text-gray-700">Telefone</label>
                <input id="telefone" type="text" name="telefone" value="{{ old('telefone') }}" required maxlength="15" placeholder="(00) 00000-0000" oninput="maskPhone(this)" class="mt-1 block w-full border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
            </div>

            <div class="md:col-span-2">
                <label for="endereco" class="block text-sm font-medium text-gray-700">Endereço Completo</label>
                <input id="endereco" type="text" name="endereco" value="{{ old('endereco') }}" required placeholder="Ex: Rua X, 123 - Centro, Cidade - RS" class="mt-1 block w-full border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
            </div>

            <div class="md:col-span-2">
                <label for="email" class="block text-sm font-medium text-gray-700">E-mail Institucional</label>
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
        function maskCnpj(input) {
            let value = input.value.replace(/\D/g, '');
            value = value.replace(/^(\d{2})(\d)/, '$1.$2');
            value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
            value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
            value = value.replace(/(\d{4})(\d)/, '$1-$2');
            input.value = value;
        }

        function maskPhone(input) {
            let value = input.value.replace(/\D/g, '');
            value = value.replace(/^(\d{2})(\d)/g, '($1) $2');
            value = value.replace(/(\d)(\d{4})$/, '$1-$2');
            input.value = value;
        }

        document.getElementById('formHemo').addEventListener('submit', function(e) {
            let cnpjInput = document.getElementById('cnpj').value.replace(/\D/g, '');
            if(cnpjInput.length !== 14) {
                e.preventDefault();
                alert('Por favor, informe um CNPJ válido completo.');
            }
        });
    </script>
</x-guest-layout>
