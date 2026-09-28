{{-- Chip aspek perkembangan (ikon + label, warna per aspek). Kelas ditulis utuh agar terbaca Tailwind. --}}
@props(['aspek'])

@php
    $warna = [
        'motorik_kasar' => 'bg-aspek-kasar text-aspek-kasar-teks',
        'motorik_halus' => 'bg-aspek-halus text-aspek-halus-teks',
        'bicara_bahasa' => 'bg-aspek-bicara text-aspek-bicara-teks',
        'sosial_emosional' => 'bg-aspek-sosial text-aspek-sosial-teks',
    ][$aspek] ?? 'bg-krem-tua text-ink';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-2 rounded-full px-3.5 py-1.5 text-sm font-bold $warna"]) }}>
    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        @switch($aspek)
            @case('motorik_kasar')
                <circle cx="12" cy="5" r="1" /><path d="m9 20 3-6 3 6" /><path d="m6 8 6 2 6-2" /><path d="M12 10v4" />
                @break
            @case('motorik_halus')
                <path d="M18 11V6a2 2 0 0 0-4 0" /><path d="M14 10V4a2 2 0 0 0-4 0v2" /><path d="M10 10.5V6a2 2 0 0 0-4 0v8" /><path d="M18 8a2 2 0 1 1 4 0v6a8 8 0 0 1-8 8h-2c-2.8 0-4.5-.86-5.99-2.34l-3.6-3.6a2 2 0 0 1 2.83-2.82L7 15" />
                @break
            @case('bicara_bahasa')
                <path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z" />
                @break
            @default
                <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" />
        @endswitch
    </svg>
    {{ \App\Models\Materi::ASPEK[$aspek] ?? $aspek }}
</span>
