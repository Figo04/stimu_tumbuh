@props(['judul' => 'Tips'])

<aside {{ $attributes->merge(['class' => 'my-4 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-base leading-relaxed text-emerald-900']) }}>
    <p class="mb-1 font-semibold">💡 {{ $judul }}</p>
    <div>{{ $slot }}</div>
</aside>
