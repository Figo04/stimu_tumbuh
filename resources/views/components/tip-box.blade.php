@props(['judul' => 'Tips'])

<aside {{ $attributes->merge(['class' => 'my-5 rounded-2xl border border-brand/20 bg-brand-soft p-5 text-lg leading-relaxed text-ink']) }}>
    <p class="mb-1 font-extrabold">💡 {{ $judul }}</p>
    <div>{{ $slot }}</div>
</aside>
