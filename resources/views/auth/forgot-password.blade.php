<x-guest-layout>
    <h1 class="manchete">Esqueci a senha</h1>
    <p class="mt-4 max-w-[38ch] text-[1.0625rem] leading-normal text-cinza">Informe o e-mail da conta. Vamos enviar um link para você criar uma senha nova.</p>

    <form method="POST" action="{{ route('password.email') }}" class="mt-8 grid max-w-sm gap-5">
        @csrf

        <div>
            <x-input-label for="email" value="E-mail" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-2 flex flex-wrap items-center gap-x-5 gap-y-3">
            <x-primary-button>Enviar link</x-primary-button>
            <a href="{{ route('login') }}" class="botao-texto">Voltar para o login</a>
        </div>
    </form>
</x-guest-layout>
