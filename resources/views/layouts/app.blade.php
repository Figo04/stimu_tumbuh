<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=nunito:400,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-krem font-sans text-ink antialiased">
        @php($anak = Auth::user()->anak)

        {{-- Header: anak yang dipantau + tombol konsultasi (design/mockup/orang-tua). --}}
        <header class="sticky top-0 z-20 border-b border-krem-garis bg-white">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
                <div class="min-w-0">
                    <p class="text-sm text-ink-muted">Anak yang dipantau</p>
                    <p class="truncate text-lg font-bold">
                        @if ($anak)
                            {{ $anak->nama_inisial }} — {{ \App\Services\UsiaAnakService::usiaBulan($anak->tanggal_lahir) }} bulan
                        @else
                            {{ Auth::user()->nama }}
                        @endif
                    </p>
                </div>
                <x-nav-konsultasi />
            </div>
        </header>

        @isset($header)
            <div class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        @endisset

        {{-- pb-24: ruang agar konten terakhir tidak tertutup tab bar. --}}
        <main class="pb-24">
            {{ $slot }}
        </main>

        {{-- Tab bar bawah — satu-satunya navigasi orang tua. --}}
        <nav class="fixed inset-x-0 bottom-0 z-20 border-t border-krem-garis bg-white pb-[env(safe-area-inset-bottom)]" aria-label="Menu utama">
            <div class="mx-auto grid max-w-2xl grid-cols-5">
                <x-tab-ortu :href="route('materi.index')" :active="request()->routeIs('materi.*')" label="Materi">
                    <path d="M12 7v14" /><path d="M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3z" />
                </x-tab-ortu>
                <x-tab-ortu :href="route('aktivitas.index')" :active="request()->routeIs('aktivitas.*')" label="Kalender">
                    <path d="M8 2v4" /><path d="M16 2v4" /><rect width="18" height="18" x="3" y="4" rx="2" /><path d="M3 10h18" /><path d="M8 14h.01" /><path d="M12 14h.01" /><path d="M16 14h.01" /><path d="M8 18h.01" /><path d="M12 18h.01" /><path d="M16 18h.01" />
                </x-tab-ortu>
                <x-tab-ortu :href="route('perkembangan.index')" :active="request()->routeIs('perkembangan.*')" label="Perkembangan">
                    <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" /><path d="M3.22 12H9.5l.5-1 2 4.5 2-7 1.5 3.5h5.27" />
                </x-tab-ortu>
                <x-nav-kuesioner />
                <x-tab-ortu :href="route('profile.edit')" :active="request()->routeIs('profile.*')" label="Profil">
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" /><circle cx="12" cy="7" r="4" />
                </x-tab-ortu>
            </div>
        </nav>
    </body>
</html>
