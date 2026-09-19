@props(['langkah' => [], 'judul' => 'Langkah-langkah'])

{{-- Langkah bernomor: isi lewat :langkah (array teks), atau slot berisi <li> untuk teks yang lebih kaya. --}}
<div {{ $attributes->merge(['class' => 'my-4 text-base leading-relaxed text-gray-800']) }}>
    @if ($judul)
        <p class="mb-2 font-semibold">{{ $judul }}</p>
    @endif
    <ol class="list-decimal space-y-2 rounded-lg bg-gray-50 py-4 pl-10 pr-4 marker:font-bold marker:text-indigo-600">
        @foreach ($langkah as $teks)
            <li class="pl-1">{{ $teks }}</li>
        @endforeach
        {{ $slot }}
    </ol>
</div>
