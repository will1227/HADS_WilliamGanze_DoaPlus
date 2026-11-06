<x-guest-layout>
    <h1 class="manchete">Entrar</h1>
    <p class="mt-4 max-w-[36ch] text-[1.0625rem] leading-normal text-cinza">Use a conta de doador, de hemocentro ou de administrador.</p>

    <x-erros class="mt-6" />

    <form method="POST" action="{{ route('login') }}" class="mt-8 grid max-w-sm gap-5">
        @csrf

        <div>
            <label for="identificacao" class="rotulo">E-mail, CPF ou CNPJ</label>
            <input id="identificacao" type="text" name="identificacao" value="{{ old('identificacao') }}" required autofocus
                   autocomplete="username" oninput="maskLogin(this)" class="campo">
        </div>

        <div>
            <div class="flex items-baseline justify-between gap-4">
                <label for="password" class="rotulo">Senha</label>
                @if (Route::has('password.request'))
                    <a class="text-sm text-cinza underline underline-offset-4 hover:text-sangue" href="{{ route('password.request') }}">Esqueci a senha</a>
                @endif
            </div>
            <input id="password" type="password" name="password" required autocomplete="current-password" class="campo">
        </div>

        <label for="remember_me" class="inline-flex items-center gap-2">
            <input id="remember_me" type="checkbox" name="remember" class="rounded border-borda text-sangue focus:ring-sangue">
            Continuar conectado
        </label>

        <div class="mt-2 flex flex-wrap items-center gap-x-5 gap-y-3">
            <button type="submit" class="botao-sangue">Entrar</button>
            <a href="{{ route('register') }}" class="botao-texto">Criar conta</a>
        </div>
    </form>

    <script>
        // Formata como CPF ou CNPJ conforme a quantidade de números; se houver letra ou @, é e-mail
        function maskLogin(input) {
            let value = input.value;

            if (/[a-zA-Z@]/.test(value)) {
                return;
            }

            let numbers = value.replace(/\D/g, '');

            if (numbers.length <= 11) {
                numbers = numbers.replace(/(\d{3})(\d)/, '$1.$2');
                numbers = numbers.replace(/(\d{3})(\d)/, '$1.$2');
                numbers = numbers.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                input.value = numbers;
            } else {
                numbers = numbers.replace(/^(\d{2})(\d)/, '$1.$2');
                numbers = numbers.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
                numbers = numbers.replace(/\.(\d{3})(\d)/, '.$1/$2');
                numbers = numbers.replace(/(\d{4})(\d)/, '$1-$2');
                input.value = numbers.substring(0, 18);
            }
        }
    </script>
</x-guest-layout>
