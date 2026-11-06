<x-guest-layout>
    <h1 class="manchete">Cadastro de doador</h1>
    <p class="mt-4 max-w-[38ch] text-[1.0625rem] leading-normal text-cinza">Pode doar quem tem entre 16 e 69 anos e pesa 50 kg ou mais.</p>

    <x-erros class="mt-6" />

    <form method="POST" action="{{ route('doadores.store') }}" id="formDoador" class="mt-8 grid gap-5 sm:grid-cols-2">
        @csrf

        <div class="sm:col-span-2">
            <label for="nome" class="rotulo">Nome completo</label>
            <input id="nome" type="text" name="nome" value="{{ old('nome') }}" required autocomplete="name" class="campo">
        </div>

        <div>
            <label for="cpf" class="rotulo">CPF</label>
            <input id="cpf" type="text" name="cpf" value="{{ old('cpf') }}" required maxlength="14" inputmode="numeric" placeholder="000.000.000-00" oninput="maskCpf(this)" class="campo">
        </div>

        <div>
            <label for="data_nascimento" class="rotulo">Data de nascimento</label>
            <input id="data_nascimento" type="date" name="data_nascimento" value="{{ old('data_nascimento') }}" required
                   min="{{ now()->subYears(70)->addDay()->format('Y-m-d') }}"
                   max="{{ now()->subYears(16)->format('Y-m-d') }}"
                   class="campo">
        </div>

        <div>
            <label for="sexo" class="rotulo">Sexo</label>
            <select id="sexo" name="sexo" required aria-describedby="sexoAjuda" class="campo">
                <option value="">Selecione</option>
                <option value="Masculino" @selected(old('sexo') === 'Masculino')>Masculino</option>
                <option value="Feminino" @selected(old('sexo') === 'Feminino')>Feminino</option>
            </select>
            <p id="sexoAjuda" class="ajuda">Define o intervalo entre as doações.</p>
        </div>

        <div>
            <label for="peso" class="rotulo">Peso (kg)</label>
            <input id="peso" type="number" step="0.1" min="50" name="peso" value="{{ old('peso') }}" required inputmode="decimal" class="campo">
        </div>

        <div class="sm:col-span-2">
            <label for="tipo_sanguineo" class="rotulo">Tipo sanguíneo</label>
            <select id="tipo_sanguineo" name="tipo_sanguineo" required class="campo">
                <option value="">Selecione</option>
                @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $tipo)
                    <option value="{{ $tipo }}" @selected(old('tipo_sanguineo') === $tipo)>{{ $tipo }}</option>
                @endforeach
            </select>
        </div>

        <div class="sm:col-span-2">
            <label for="email" class="rotulo">E-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="campo">
        </div>

        <div class="sm:col-span-2">
            <label for="senha" class="rotulo">Senha</label>
            <input id="senha" type="password" name="senha" required minlength="6" autocomplete="new-password" aria-describedby="senhaAjuda" class="campo">
            <p id="senhaAjuda" class="ajuda">Pelo menos 6 caracteres.</p>
        </div>

        <div class="mt-2 flex flex-wrap items-center gap-x-5 gap-y-3 sm:col-span-2">
            <button type="submit" class="botao-sangue">Criar conta de doador</button>
            <a href="{{ route('register') }}" class="botao-texto">Trocar tipo de conta</a>
        </div>
    </form>

    <p class="mt-8 text-cinza">Já tem conta? <a href="{{ route('login') }}" class="text-tinta underline underline-offset-4 hover:text-sangue">Entrar</a></p>

    <script>
        function maskCpf(input) {
            let value = input.value.replace(/\D/g, '');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            input.value = value;
        }

        document.getElementById('formDoador').addEventListener('submit', function (e) {
            const cpf = document.getElementById('cpf');
            if (cpf.value.replace(/\D/g, '').length !== 11) {
                e.preventDefault();
                cpf.setCustomValidity('Informe os 11 números do CPF.');
                cpf.reportValidity();
            }
        });
        document.getElementById('cpf').addEventListener('input', e => e.target.setCustomValidity(''));
    </script>
</x-guest-layout>
