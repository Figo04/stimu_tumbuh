@props(['route'])

{{-- Menu mati sendiri selama rutenya belum didaftarkan (Sesi 28–35),
     jadi sidebar tidak perlu diedit tiap kali satu menu jadi. --}}
@php
    $ada = Route::has($route);
    $basis = Str::beforeLast($route, '.index');
    $aktif = $ada && (request()->routeIs($route) || request()->routeIs($basis.'.*'));
@endphp

@if ($ada)
    <a href="{{ route($route) }}"
       @class([
           'block rounded-md px-3 py-2 text-sm font-medium transition',
           'bg-emerald-600 text-white' => $aktif,
           'text-slate-300 hover:bg-slate-700 hover:text-white' => ! $aktif,
       ])
       @if ($aktif) aria-current="page" @endif>
        {{ $slot }}
    </a>
@else
    <span class="flex items-center justify-between rounded-md px-3 py-2 text-sm font-medium text-slate-500"
          title="Belum tersedia">
        {{ $slot }}
        <span class="text-[10px] uppercase tracking-wide text-slate-600">segera</span>
    </span>
@endif
