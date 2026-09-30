{{-- Satu menu sidebar admin. Slot = isi <svg> (path ikon). Aktif juga untuk halaman turunan (show/create/edit).
     Menu mati sendiri selama rutenya belum didaftarkan, jadi sidebar tidak perlu diedit tiap kali satu menu jadi. --}}
@props(['route', 'label'])

@php
    $ada = Route::has($route);
    $aktif = $ada && (request()->routeIs($route) || request()->routeIs(Str::beforeLast($route, '.index').'.*'));
@endphp

<{{ $ada ? 'a' : 'span' }} @if ($ada) href="{{ route($route) }}" @else title="Belum tersedia" @endif
   @class([
       'flex items-center gap-3 rounded-2xl px-4 py-3 font-bold transition',
       'bg-brand-aktif text-brand' => $aktif,
       'text-ink hover:bg-krem' => $ada && ! $aktif,
       'text-ink-muted/60' => ! $ada,
   ])
   @if ($aktif) aria-current="page" @endif>
    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        {{ $slot }}
    </svg>
    {{ $label }}
    @unless ($ada)
        <span class="ms-auto text-[10px] uppercase tracking-wide">segera</span>
    @endunless
</{{ $ada ? 'a' : 'span' }}>
