<x-guest-layout>
    <h1 class="manchete">Criar conta</h1>
    <p class="mt-4 max-w-[36ch] text-[1.0625rem] leading-normal text-cinza">Você vai doar ou representa uma instituição?</p>

    {{-- Mesmo desenho da lista "Onde doar" da tela inicial --}}
    <ul class="linhas mt-8 border-t border-linha">
        <li>
            <a href="{{ url('/register/doador') }}" class="grid grid-cols-[14px_1fr] gap-x-3.5 gap-y-1 px-1 py-4 hover:bg-gaze">
                <x-application-logo class="mt-1 h-[15px] w-3 text-arterial" />
                <span class="font-bold">Sou doador</span>
                <span class="col-start-2 text-sm leading-snug text-cinza">Agende doações e saiba quando pode doar de novo.</span>
            </a>
        </li>
        <li>
            <a href="{{ url('/register/hemocentro') }}" class="grid grid-cols-[14px_1fr] gap-x-3.5 gap-y-1 px-1 py-4 hover:bg-gaze">
                <x-application-logo class="mt-1 h-[15px] w-3 text-plasma" />
                <span class="font-bold">Represento um hemocentro</span>
                <span class="col-start-2 text-sm leading-snug text-cinza">Receba agendamentos e registre as doações da sua unidade.</span>
            </a>
        </li>
    </ul>

    <p class="mt-6 text-cinza">Já tem conta? <a href="{{ route('login') }}" class="text-tinta underline underline-offset-4 hover:text-sangue">Entrar</a></p>
</x-guest-layout>
