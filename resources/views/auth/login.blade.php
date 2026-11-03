<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Entrar</h2>
        <p class="text-sm text-gray-500 mt-1">Acesse sua conta de doador, hemocentro ou administrador.</p>
    </div>

    <!-- Mensagem de Sucesso (Ex: "Conta criada com sucesso!") -->
    @if (session('status'))
        <div class="mb-5 font-medium text-sm text-green-700 bg-green-50 p-3 rounded-lg border border-green-200">
            {{ session('status') }}
        </div>
    @endif

    <!-- Exibição de Erros -->
    @if ($errors->any())
        <div class="mb-5 p-3 bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Campo Dinâmico: Identificação -->
        <div>
            <label for="identificacao" class="block text-sm font-semibold text-gray-700">E-mail, CPF ou CNPJ</label>
            <!-- Note o evento oninput chamando a função maskLogin -->
            <input id="identificacao" type="text" name="identificacao" value="{{ old('identificacao') }}" required autofocus
                   placeholder="Digite seu e-mail ou documento" oninput="maskLogin(this)"
                   class="mt-1.5 block w-full border-gray-300 rounded-lg py-2.5 focus:ring-red-500 focus:border-red-500">
        </div>

        <!-- Senha -->
        <div>
            <div class="flex items-center justify-between">
                <label for="password" class="block text-sm font-semibold text-gray-700">Senha</label>
                @if (Route::has('password.request'))
                    <a class="text-xs font-medium text-red-600 hover:text-red-700 hover:underline" href="{{ route('password.request') }}">
                        Esqueceu sua senha?
                    </a>
                @endif
            </div>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   class="mt-1.5 block w-full border-gray-300 rounded-lg py-2.5 focus:ring-red-500 focus:border-red-500">
        </div>

        <!-- Lembrar de mim -->
        <label for="remember_me" class="inline-flex items-center">
            <input id="remember_me" type="checkbox" name="remember" class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500">
            <span class="ml-2 text-sm text-gray-600">Lembrar de mim</span>
        </label>

        <button type="submit" class="w-full bg-red-600 text-white py-2.5 rounded-lg font-bold shadow-sm hover:bg-red-700 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
            Entrar
        </button>

        <div class="text-center text-sm text-gray-600 border-t border-gray-100 pt-5">
            Ainda não tem conta?
            <a href="{{ route('register') }}" class="text-red-600 font-semibold hover:underline">
                Criar conta
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
