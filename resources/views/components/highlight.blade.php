@props(['varian' => 'info', 'judul' => null])

@php
    // Kelas ditulis utuh agar terbaca oleh scanner Tailwind.
    $kelas = [
        'info' => 'border-aspek-bicara-teks bg-aspek-bicara text-ink',
        'penting' => 'border-hangat bg-hangat-bg text-ink',
    ][$varian] ?? 'border-aspek-bicara-teks bg-aspek-bicara text-ink';
@endphp

<div {{ $attributes->merge(['class' => "my-4 rounded-r-2xl border-l-4 p-5 text-lg leading-relaxed $kelas"]) }}>
    @if ($judul)
        <p class="mb-1 font-extrabold">{{ $judul }}</p>
    @endif
    <div>{{ $slot }}</div>
</div>
