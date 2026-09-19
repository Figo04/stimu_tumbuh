@props(['varian' => 'info', 'judul' => null])

@php
    // Kelas ditulis utuh agar terbaca oleh scanner Tailwind.
    $kelas = [
        'info' => 'border-sky-500 bg-sky-50 text-sky-900',
        'penting' => 'border-amber-500 bg-amber-50 text-amber-900',
    ][$varian] ?? 'border-sky-500 bg-sky-50 text-sky-900';
@endphp

<div {{ $attributes->merge(['class' => "my-4 rounded-r-lg border-l-4 p-4 text-base leading-relaxed $kelas"]) }}>
    @if ($judul)
        <p class="mb-1 font-semibold">{{ $judul }}</p>
    @endif
    <div>{{ $slot }}</div>
</div>
