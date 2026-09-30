@props(['judul' => 'Dashboard'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $judul }} — Admin {{ config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=nunito:400,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-krem font-sans text-ink antialiased">
        <div x-data="{ sidebar: false }" class="min-h-screen">
            {{-- Sidebar: selalu tampak di layar lebar, geser masuk di layar sempit. --}}
            <aside :class="sidebar ? 'translate-x-0' : '-translate-x-full'"
                   class="fixed inset-y-0 left-0 z-30 flex w-72 -translate-x-full flex-col overflow-y-auto border-r border-krem-garis bg-white px-6 py-6 transition-transform lg:translate-x-0">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-brand-soft text-lg font-extrabold text-brand">ST</span>
                    <span class="text-xl font-extrabold text-brand">{{ config('app.name') }}</span>
                </a>

                <nav class="mt-10 space-y-8">
                    <div>
                        <p class="px-4 pb-3 text-xs font-extrabold uppercase tracking-wide text-ink-muted">Data</p>
                        <div class="space-y-1">
                            <x-admin-nav-link route="admin.dashboard" label="Dashboard">
                                <rect width="7" height="9" x="3" y="3" rx="1" /><rect width="7" height="5" x="14" y="3" rx="1" /><rect width="7" height="9" x="14" y="12" rx="1" /><rect width="7" height="5" x="3" y="16" rx="1" />
                            </x-admin-nav-link>
                            <x-admin-nav-link route="admin.responden.index" label="Responden">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M22 21v-2a4 4 0 0 0-3-3.87" /><path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </x-admin-nav-link>
                            <x-admin-nav-link route="admin.hasil-test.index" label="Hasil Test">
                                <rect width="8" height="4" x="8" y="2" rx="1" /><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" /><path d="m9 14 2 2 4-4" />
                            </x-admin-nav-link>
                            <x-admin-nav-link route="admin.aktivitas.index" label="Aktivitas Stimulasi">
                                <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                            </x-admin-nav-link>
                            <x-admin-nav-link route="admin.perkembangan.index" label="Perkembangan">
                                <path d="M16 7h6v6" /><path d="m22 7-8.5 8.5-5-5L2 17" />
                            </x-admin-nav-link>
                        </div>
                    </div>

                    <div>
                        <p class="px-4 pb-3 text-xs font-extrabold uppercase tracking-wide text-ink-muted">Konten</p>
                        <div class="space-y-1">
                            <x-admin-nav-link route="admin.soal.index" label="Kelola Soal">
                                <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z" /><path d="M10 10.3c.2-.4.5-.8.9-1a2.1 2.1 0 0 1 2.6.4c.3.4.5.8.5 1.3 0 1.3-2 2-2 2" /><path d="M12 17h.01" />
                            </x-admin-nav-link>
                            <x-admin-nav-link route="admin.materi.index" label="Kelola Materi">
                                <path d="M12 7v14" /><path d="M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3z" />
                            </x-admin-nav-link>
                        </div>
                    </div>
                </nav>

                {{-- Mockup menampilkan peran ("Peneliti utama"); tabel admins tidak punya kolom peran (PRD §5), jadi email. --}}
                <div class="mt-auto border-t border-krem-garis pt-6">
                    <p class="truncate font-extrabold">{{ auth('admin')->user()->nama }}</p>
                    <p class="truncate text-sm text-ink-muted">{{ auth('admin')->user()->email }}</p>
                    <form method="POST" action="{{ route('admin.logout') }}" class="mt-4">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-3 rounded-2xl px-4 py-3 font-bold text-ink hover:bg-krem">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" /><path d="m16 17 5-5-5-5" /><path d="M21 12H9" /></svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </aside>

            {{-- Latar penutup saat sidebar terbuka di layar sempit. --}}
            <div x-show="sidebar" @click="sidebar = false" class="fixed inset-0 z-20 bg-ink/40 lg:hidden" style="display: none"></div>

            <div class="lg:pl-72">
                <header class="sticky top-0 z-10 flex flex-wrap items-center gap-4 border-b border-krem-garis bg-white px-4 py-4 sm:px-8">
                    <button type="button" @click="sidebar = ! sidebar" aria-label="Buka menu" class="rounded-xl p-2 text-ink-muted hover:bg-krem lg:hidden">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </button>

                    <div class="min-w-0">
                        <p class="text-sm text-ink-muted">Area Peneliti</p>
                        <h1 class="truncate text-2xl font-extrabold">{{ $judul }}</h1>
                    </div>

                    @isset($aksi)
                        <div class="ms-auto flex flex-wrap justify-end gap-2">{{ $aksi }}</div>
                    @endisset
                </header>

                <main class="p-4 sm:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
