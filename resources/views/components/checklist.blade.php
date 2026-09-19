@props(['items' => [], 'judul' => null])

{{-- Daftar poin bertanda centang (tampilan saja, bukan form penilaian). --}}
<div {{ $attributes->merge(['class' => 'my-4 text-base leading-relaxed text-gray-800']) }}>
    @if ($judul)
        <p class="mb-2 font-semibold">{{ $judul }}</p>
    @endif
    <ul class="space-y-2">
        @foreach ($items as $item)
            <li class="flex gap-3">
                <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 font-bold text-emerald-700" aria-hidden="true">✓</span>
                <span>{{ $item }}</span>
            </li>
        @endforeach
    </ul>
</div>
