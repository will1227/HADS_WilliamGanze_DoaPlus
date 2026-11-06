<x-guest-layout>
    <h1 class="manchete">Cadastro de instituição</h1>
    <p class="mt-4 max-w-[38ch] text-[1.0625rem] leading-normal text-cinza">Depois do cadastro, a equipe DoaPlus confere os dados e libera a agenda da sua unidade.</p>

    <x-erros class="mt-6" />

    <form method="POST" action="{{ route('hemocentros.store') }}" id="formHemo" class="mt-8 grid gap-5 sm:grid-cols-2">
        @csrf

        <div class="sm:col-span-2">
            <label for="nome" class="rotulo">Nome da instituição</label>
            <input id="nome" type="text" name="nome" value="{{ old('nome') }}" required autocomplete="organization" class="campo">
        </div>

        <div>
            <label for="cnpj" class="rotulo">CNPJ</label>
            <input id="cnpj" type="text" name="cnpj" value="{{ old('cnpj') }}" required maxlength="18" inputmode="numeric" placeholder="00.000.000/0000-00" oninput="maskCnpj(this)" class="campo">
        </div>

        <div>
            <label for="telefone" class="rotulo">Telefone (WhatsApp)</label>
            <input id="telefone" type="tel" name="telefone" value="{{ old('telefone') }}" required maxlength="15" placeholder="(00) 00000-0000" oninput="maskPhone(this)" class="campo">
        </div>

        <div class="sm:col-span-2">
            <label for="endereco" class="rotulo">Endereço completo</label>
            <input id="endereco" type="text" name="endereco" value="{{ old('endereco') }}" required placeholder="Rua, número, bairro, cidade" autocomplete="street-address" class="campo">
        </div>

        <div class="sm:col-span-2">
            <label for="email" class="rotulo">E-mail institucional</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="campo">
        </div>

        <div class="sm:col-span-2">
            <label for="senha" class="rotulo">Senha</label>
            <input id="senha" type="password" name="senha" required minlength="6" autocomplete="new-password" aria-describedby="senhaAjuda" class="campo">
            <p id="senhaAjuda" class="ajuda">Pelo menos 6 caracteres.</p>
        </div>

        <div class="mt-2 flex flex-wrap items-center gap-x-5 gap-y-3 sm:col-span-2">
            <button type="submit" class="botao-sangue">Cadastrar instituição</button>
            <a href="{{ route('register') }}" class="botao-texto">Trocar tipo de conta</a>
        </div>
    </form>

    <p class="mt-8 text-cinza">Já tem conta? <a href="{{ route('login') }}" class="text-tinta underline underline-offset-4 hover:text-sangue">Entrar</a></p>

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

        document.getElementById('formHemo').addEventListener('submit', function (e) {
            const cnpj = document.getElementById('cnpj');
            if (cnpj.value.replace(/\D/g, '').length !== 14) {
                e.preventDefault();
                cnpj.setCustomValidity('Informe os 14 números do CNPJ.');
                cnpj.reportValidity();
            }
        });
        document.getElementById('cnpj').addEventListener('input', e => e.target.setCustomValidity(''));
    </script>
</x-guest-layout>
