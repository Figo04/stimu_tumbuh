@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-bold text-ink']) }}>
    {{ $value ?? $slot }}
</label>
