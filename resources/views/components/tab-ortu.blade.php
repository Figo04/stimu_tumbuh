{{-- Satu item tab bar bawah orang tua. Tanpa href = tidak bisa diklik (terkunci). Slot = isi <svg> (path ikon). --}}
@props(['href' => null, 'active' => false, 'label'])

@php
    $kelas = 'relative flex min-w-0 flex-col items-center gap-1 px-1 py-2.5 text-xs font-bold '
        .($active ? 'text-brand' : ($href ? 'text-ink-muted hover:text-ink' : 'text-ink-muted/50'));
@endphp

<{{ $href ? 'a' : 'span' }} @if ($href) href="{{ $href }}" @else aria-disabled="true" @endif
    @if ($active) aria-current="page" @endif {{ $attributes->merge(['class' => $kelas]) }}>
    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        {{ $slot }}
    </svg>
    <span class="max-w-full truncate">{{ $label }}</span>
    {{ $extra ?? '' }}
</{{ $href ? 'a' : 'span' }}>
