@props(['value'])

<label {{ $attributes->merge(['class' => 'rotulo']) }}>
    {{ $value ?? $slot }}
</label>
