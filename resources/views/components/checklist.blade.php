@props(['items' => [], 'judul' => null])

{{-- Daftar poin bertanda centang (tampilan saja, bukan form penilaian). --}}
<div {{ $attributes->merge(['class' => 'my-4 text-lg leading-relaxed text-ink']) }}>
    @if ($judul)
        <p class="mb-2 font-extrabold">{{ $judul }}</p>
    @endif
    <ul class="space-y-2">
        @foreach ($items as $item)
            <li class="flex gap-3">
                <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-sukses-badge font-bold text-sukses" aria-hidden="true">✓</span>
                <span>{{ $item }}</span>
            </li>
        @endforeach
    </ul>
</div>
