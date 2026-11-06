{{-- Mensagens de retorno das ações (session 'status' e 'erro'), iguais em todas as telas --}}
@if (session('status'))
    <p {{ $attributes->merge(['class' => 'aviso-ok']) }} role="status">{{ session('status') }}</p>
@endif
@if (session('erro'))
    <p {{ $attributes->merge(['class' => 'aviso-erro']) }} role="alert">{{ session('erro') }}</p>
@endif
