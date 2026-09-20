{{-- Menu Pre-test/Post-test yang berganti sendiri (PRD §4 alur orang tua). --}}
@props(['responsive' => false])

@php
    $u = Auth::user();
    $link = $responsive ? 'responsive-nav-link' : 'nav-link';
    $kunci = $responsive
        ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-400'
        : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-400';
@endphp

@if (! $u->sudahPretest())
    <x-dynamic-component :component="$link" :href="route('pretest')" :active="request()->routeIs('pretest')">
        Pre-test
    </x-dynamic-component>
@elseif ($u->sudahKuesioner('post'))
    <span class="{{ $kunci }}">Post-test ✓</span>
@elseif ($u->semuaMateriSelesai())
    <x-dynamic-component :component="$link" :href="route('posttest')" :active="request()->routeIs('posttest')">
        Post-test
    </x-dynamic-component>
@else
    <span class="{{ $kunci }}" title="Selesaikan semua materi kelompok usia anak dulu">Post-test 🔒</span>
@endif
