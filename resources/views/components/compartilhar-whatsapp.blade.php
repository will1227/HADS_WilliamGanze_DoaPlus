{{-- Abre o WhatsApp com a mensagem pronta e o link da página pública (RF13/HU13) --}}
@props(['texto'])

<a href="https://wa.me/?text={{ rawurlencode($texto.' '.route('home')) }}" target="_blank" rel="noopener" {{ $attributes }}>
    {{ $slot->isEmpty() ? 'Compartilhar no WhatsApp' : $slot }}
</a>
