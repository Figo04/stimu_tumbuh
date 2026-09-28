@props(['langkah' => [], 'judul' => 'Langkah-langkah'])

{{-- Langkah bernomor: isi lewat :langkah (array teks), atau slot berisi <li> untuk teks yang lebih kaya. --}}
<div {{ $attributes->merge(['class' => 'my-4 text-lg leading-relaxed text-ink']) }}>
    @if ($judul)
        <p class="mb-2 font-extrabold">{{ $judul }}</p>
    @endif
    <ol class="list-decimal space-y-2 rounded-2xl bg-krem py-4 pl-10 pr-4 marker:font-bold marker:text-brand">
        @foreach ($langkah as $teks)
            <li class="pl-1">{{ $teks }}</li>
        @endforeach
        {{ $slot }}
    </ol>
</div>
