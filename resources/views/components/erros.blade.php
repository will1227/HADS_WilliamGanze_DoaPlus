{{-- Resumo dos erros de validação do formulário --}}
@if ($errors->any())
    <div {{ $attributes->merge(['class' => 'aviso-erro']) }} role="alert">
        <p class="font-bold">Corrija os campos abaixo para continuar:</p>
        <ul class="mt-1 list-disc pl-5">
            @foreach ($errors->all() as $erro)
                <li>{{ $erro }}</li>
            @endforeach
        </ul>
    </div>
@endif
