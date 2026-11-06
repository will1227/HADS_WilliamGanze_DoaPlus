@props(['status'])

@if ($status)
    <p {{ $attributes->merge(['class' => 'aviso-ok']) }} role="status">
        {{ $status }}
    </p>
@endif
