<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Bem-vindo de volta</h2>
        <p class="text-sm text-gray-600 mt-1">Acesse sua conta no DoaPlus</p>
    </div>

    <!-- Mensagem de Sucesso (Ex: "Conta criada com sucesso!") -->
    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600 bg-green-50 p-3 rounded-md border border-green-200">
            {{ session('status') }}
        </div>
    @endif

    <!-- Exibição de Erros -->
    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-600 text-sm rounded-md">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Campo Dinâmico: Identificação -->
        <div>
            <label for="identificacao" class="block text-sm font-medium text-gray-700">E-mail, CPF ou CNPJ</label>
            <!-- Note o evento oninput chamando a função maskLogin -->
            <input id="identificacao" type="text" name="identificacao" value="{{ old('identificacao') }}" required autofocus
                   placeholder="Digite seu e-mail ou documento" oninput="maskLogin(this)"
                   class="mt-1 block w-full border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
        </div>

        <!-- Senha -->
        <div class="mt-4">
            <label for="password" class="block text-sm font-medium text-gray-700">Senha</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   class="mt-1 block w-full border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
        </div>

        <!-- Lembrar de mim -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" name="remember" class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500">
                <span class="ml-2 text-sm text-gray-600">Lembrar de mim</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-6">
            @if (Route::has('password.request'))
                <a class="text-sm text-gray-600 hover:text-gray-900 underline" href="{{ route('password.request') }}">
                    Esqueceu sua senha?
                </a>
            @endif

            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                Entrar
            </button>
        </div>
        
        <div class="mt-6 text-center text-sm text-gray-600 border-t pt-4">
            Ainda não tem conta? <br>
            <a href="{{ route('register') }}" class="text-red-600 font-semibold hover:underline">
                Cadastre-se aqui
            </a>
        </div>
    </form>

    <!-- Script de Inteligência da Máscara -->
    <script>
        function maskLogin(input) {
            let value = input.value;
            
            // Se o usuário digitar qualquer letra ou '@', assumimos que é e-mail. Não aplica formatação.
            if (/[a-zA-Z@]/.test(value)) {
                return; 
            }

            // Se for número, remove tudo que não for dígito para recalcular a máscara
            let numbers = value.replace(/\D/g, '');

            if (numbers.length <= 11) {
                // Formata como CPF enquanto tiver até 11 números
                numbers = numbers.replace(/(\d{3})(\d)/, '$1.$2');
                numbers = numbers.replace(/(\d{3})(\d)/, '$1.$2');
                numbers = numbers.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                input.value = numbers;
            } else {
                // Se passar de 11 números, formata como CNPJ
                numbers = numbers.replace(/^(\d{2})(\d)/, '$1.$2');
                numbers = numbers.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
                numbers = numbers.replace(/\.(\d{3})(\d)/, '.$1/$2');
                numbers = numbers.replace(/(\d{4})(\d)/, '$1-$2');
                input.value = numbers.substring(0, 18); // Trava no tamanho máximo do CNPJ formatado
            }
        }
    </script>
</x-guest-layout>
