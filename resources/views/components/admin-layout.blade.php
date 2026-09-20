@props(['judul' => 'Dashboard'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $judul }} — Admin {{ config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div x-data="{ sidebar: false }" class="min-h-screen bg-gray-100">
            {{-- Sidebar: selalu tampak di layar lebar, geser masuk di layar sempit. --}}
            <aside :class="sidebar ? 'translate-x-0' : '-translate-x-full'"
                   class="fixed inset-y-0 left-0 z-30 flex w-64 -translate-x-full flex-col overflow-y-auto bg-slate-800 px-4 py-6 transition-transform lg:translate-x-0">
                <a href="{{ route('admin.dashboard') }}" class="px-3 text-lg font-semibold text-white">
                    {{ config('app.name') }}
                </a>
                <p class="mt-1 px-3 text-xs uppercase tracking-wide text-slate-400">Panel Peneliti</p>

                <nav class="mt-8 space-y-6">
                    <div>
                        <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">Data</p>
                        <div class="space-y-1">
                            <x-admin-nav-link route="admin.dashboard">Dashboard</x-admin-nav-link>
                            <x-admin-nav-link route="admin.responden.index">Responden</x-admin-nav-link>
                            <x-admin-nav-link route="admin.hasil-test.index">Hasil Test</x-admin-nav-link>
                            <x-admin-nav-link route="admin.aktivitas.index">Aktivitas Stimulasi</x-admin-nav-link>
                            <x-admin-nav-link route="admin.perkembangan.index">Perkembangan</x-admin-nav-link>
                        </div>
                    </div>

                    <div>
                        <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">Konten</p>
                        <div class="space-y-1">
                            <x-admin-nav-link route="admin.soal.index">Kelola Soal</x-admin-nav-link>
                            <x-admin-nav-link route="admin.materi.index">Kelola Materi</x-admin-nav-link>
                        </div>
                    </div>
                </nav>
            </aside>

            {{-- Latar penutup saat sidebar terbuka di layar sempit. --}}
            <div x-show="sidebar" @click="sidebar = false" class="fixed inset-0 z-20 bg-black/40 lg:hidden" style="display: none"></div>

            <div class="lg:pl-64">
                <header class="sticky top-0 z-10 flex h-16 items-center gap-4 border-b border-gray-200 bg-white px-4 sm:px-6">
                    <button type="button" @click="sidebar = ! sidebar" class="rounded-md p-2 text-gray-500 hover:bg-gray-100 lg:hidden">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <h1 class="text-lg font-semibold text-gray-800">{{ $judul }}</h1>

                    <div class="ms-auto flex items-center gap-4">
                        <span class="hidden text-sm text-gray-600 sm:block">{{ auth('admin')->user()->nama }}</span>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-gray-500 hover:text-gray-800">
                                Keluar
                            </button>
                        </form>
                    </div>
                </header>

                <main class="p-4 sm:p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
