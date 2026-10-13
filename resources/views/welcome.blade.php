<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth scroll-pt-20">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Program penelitian yang membantu Ibu memberikan stimulasi perkembangan anak usia 12–24 bulan lewat video singkat yang bisa dipraktikkan di rumah.">

        <title>{{ config('app.name') }} — Stimulasi Sederhana di Rumah untuk Anak 12–24 Bulan</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=nunito:400,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    {{-- Landing page publik (design/mockup/landing_page). Teks disesuaikan dengan cakupan penelitian:
         usia 12–24 bulan dan materi berupa video. --}}
    @php
        $hero = file_exists(public_path('images/hero.png')) ? asset('images/hero.png') : null;
        $menu = ['#tentang' => 'Tentang', '#manfaat' => 'Manfaat', '#cara-ikut' => 'Cara Ikut', '#tanya-jawab' => 'Tanya Jawab'];
        $ctaUrl = Auth::check() ? route('dashboard') : route('register');
        $ctaLabel = Auth::check() ? 'Buka Aplikasi' : 'Daftar Sekarang';
        $wa = config('services.wa_konsultasi');
    @endphp
    <body class="bg-krem font-sans text-ink antialiased">

        {{-- NAVBAR --}}
        <header x-data="{ scrolled: false, open: false }" x-init="scrolled = scrollY > 24" @scroll.window="scrolled = scrollY > 24"
                :class="scrolled || open ? 'bg-white shadow-lg shadow-ink/5' : 'bg-transparent'"
                class="fixed inset-x-0 top-0 z-50 transition-colors duration-300">
            <nav class="mx-auto flex h-20 max-w-7xl items-center justify-between px-5 sm:px-8">
                <a href="/" class="text-xl font-extrabold tracking-tight text-brand-tua">Tumbuh <span class="text-hangat">Ceria</span></a>

                <ul class="hidden items-center gap-8 lg:flex">
                    @foreach ($menu as $href => $label)
                        <li><a href="{{ $href }}" class="text-base font-semibold text-ink hover:text-brand">{{ $label }}</a></li>
                    @endforeach
                </ul>

                <div class="hidden items-center gap-4 lg:flex">
                    @guest
                        <a href="{{ route('login') }}" class="flex min-h-12 items-center px-2 text-base font-semibold text-brand-tua hover:text-brand">Masuk</a>
                    @endguest
                    <a href="{{ $ctaUrl }}" class="flex min-h-12 items-center rounded-xl bg-brand px-5 text-base font-bold text-white shadow-lg shadow-brand/30 hover:bg-brand-tua">{{ $ctaLabel }}</a>
                </div>

                <button type="button" @click="open = !open" :aria-expanded="open" aria-label="Menu"
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/80 text-brand-tua lg:hidden">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                        <path x-show="!open" d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="open" x-cloak d="M18 6 6 18M6 6l12 12" />
                    </svg>
                </button>
            </nav>

            <div x-show="open" x-cloak class="border-t border-krem-garis bg-white px-5 pb-6 lg:hidden">
                <ul class="flex flex-col py-2">
                    @foreach ($menu as $href => $label)
                        <li><a href="{{ $href }}" @click="open = false" class="flex min-h-12 items-center text-base font-semibold text-ink">{{ $label }}</a></li>
                    @endforeach
                </ul>
                <div class="flex flex-col gap-3">
                    @guest
                        <a href="{{ route('login') }}" class="flex min-h-12 items-center justify-center rounded-xl border border-brand text-base font-bold text-brand-tua">Masuk</a>
                    @endguest
                    <a href="{{ $ctaUrl }}" class="flex min-h-12 items-center justify-center rounded-xl bg-brand text-base font-bold text-white">{{ $ctaLabel }}</a>
                </div>
            </div>
        </header>

        {{-- HERO: desktop = foto sebagai latar dengan teks di kiri; mobile = teks lalu foto. --}}
        <section class="pt-20">
            <div class="mx-auto max-w-7xl lg:px-8">
                <div class="relative lg:flex lg:min-h-[640px] lg:items-center lg:overflow-hidden lg:rounded-b-[2.5rem] {{ $hero ? 'lg:bg-[image:var(--hero)] lg:bg-cover lg:bg-center' : 'lg:bg-brand-soft' }}"
                     @if ($hero) style="--hero: url('{{ $hero }}')" @endif>
                    <div class="absolute inset-y-0 left-0 hidden w-3/5 bg-gradient-to-r from-krem/90 via-krem/70 to-transparent lg:block" aria-hidden="true"></div>

                    <div class="relative max-w-xl px-5 pt-10 sm:px-8 lg:px-0 lg:pl-10 lg:pt-0">
                        <h1 class="text-[2rem] font-extrabold leading-[1.15] text-brand-tua sm:text-5xl lg:text-[3.25rem]">
                            Stimulasi Sederhana di Rumah, Tumbuh Optimal Setiap Hari
                        </h1>
                        <p class="mt-5 text-lg leading-relaxed text-ink-muted">
                            Program penelitian yang membantu Ibu memberikan stimulasi perkembangan anak usia 12–24 bulan,
                            lewat video singkat yang bisa langsung dipraktikkan di rumah.
                        </p>
                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            <a href="{{ $ctaUrl }}" class="inline-flex min-h-14 items-center justify-center rounded-xl bg-brand px-7 text-lg font-bold text-white shadow-lg shadow-brand/30 hover:bg-brand-tua">
                                {{ Auth::check() ? 'Buka Aplikasi' : 'Daftar Sebagai Peserta' }}
                            </a>
                            <a href="#tentang" class="inline-flex min-h-14 items-center justify-center rounded-xl border-2 border-brand bg-white/70 px-7 text-lg font-bold text-brand-tua hover:bg-brand-soft">
                                Pelajari Dulu
                            </a>
                        </div>
                        <ul class="mt-8 flex flex-wrap gap-3">
                            @foreach ([
                                ['Gratis untuk peserta', '<path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" />'],
                                ['Materi dari tenaga kesehatan', '<path d="M11 2v2M5 2v2M5 3H4a2 2 0 0 0-2 2v4a6 6 0 0 0 12 0V5a2 2 0 0 0-2-2h-1" /><path d="M8 15a6 6 0 0 0 12 0v-3" /><circle cx="20" cy="10" r="2" />'],
                                ['Cukup 10 menit sehari', '<circle cx="12" cy="12" r="10" /><path d="M12 6v6l4 2" />'],
                            ] as [$label, $ikon])
                                <li class="inline-flex items-center gap-2 rounded-full bg-white/85 px-4 py-2 text-sm font-semibold text-brand-tua shadow-md shadow-ink/5">
                                    <svg class="h-4 w-4 text-brand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $ikon !!}</svg>
                                    {{ $label }}
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    @if ($hero)
                        <div class="px-5 pb-4 pt-8 sm:px-8 lg:hidden">
                            <img src="{{ $hero }}" alt="Seorang ibu bertepuk tangan sementara anaknya belajar berjalan dengan alat dorong kayu" class="w-full rounded-2xl" width="940" height="529">
                        </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- TENTANG --}}
        <section id="tentang" class="relative overflow-hidden py-20 sm:py-24">
            <div class="pointer-events-none absolute -left-24 top-10 h-64 w-64 rounded-full bg-aspek-bicara blur-sm" aria-hidden="true"></div>
            <div class="relative mx-auto max-w-7xl px-5 sm:px-8">
                <h2 class="text-3xl font-extrabold leading-tight text-brand-tua sm:text-4xl">Apa itu Tumbuh Ceria?</h2>
                <div class="mt-10 grid gap-10 lg:grid-cols-2 lg:items-start">
                    <div class="space-y-5 text-lg leading-relaxed text-ink-muted">
                        <p>
                            Tumbuh Ceria adalah program penelitian yang dijalankan oleh tim peneliti kesehatan.
                            Lewat program ini, Ibu mendapatkan video stimulasi yang sesuai dengan usia anak,
                            dalam bahasa yang sederhana dan mudah diikuti di rumah.
                        </p>
                        <p>
                            Setiap kali Ibu melakukan stimulasi bersama anak, Ibu cukup mencatatnya. Catatan itu
                            membantu tim peneliti memantau perkembangan anak dari waktu ke waktu, sekaligus
                            mengingatkan Ibu tentang kegiatan yang sudah dilakukan.
                        </p>
                    </div>
                    <div class="rounded-2xl border border-krem-garis bg-white p-7 shadow-xl shadow-ink/5">
                        <h3 class="text-xl font-bold text-brand-tua">Yang Ibu dapatkan</h3>
                        <ul class="mt-5 space-y-4">
                            @foreach ([
                                ['Video stimulasi sesuai usia anak', '<circle cx="12" cy="12" r="10" /><path d="m10 8 6 4-6 4Z" />'],
                                ['Catatan stimulasi harian', '<path d="M2 6h4M2 10h4M2 14h4M2 18h4" /><rect width="16" height="20" x="4" y="2" rx="2" /><path d="M16 2v20" />'],
                                ['Pemantauan perkembangan anak', '<path d="M3 3v18h18" /><path d="m19 9-5 5-4-4-3 3" />'],
                                ['Pendampingan lewat WhatsApp', '<path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z" />'],
                            ] as [$label, $ikon])
                                <li class="flex items-start gap-3 text-base">
                                    <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-soft">
                                        <svg class="h-5 w-5 text-brand-tua" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $ikon !!}</svg>
                                    </span>
                                    <span class="pt-1.5 leading-snug">{{ $label }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        {{-- MANFAAT: empat aspek, ikon & warna sama dengan chip aspek di aplikasi. --}}
        <section id="manfaat" class="relative overflow-hidden py-20 sm:py-24">
            <div class="pointer-events-none absolute -right-20 top-24 h-72 w-72 rounded-full bg-aspek-sosial blur-sm" aria-hidden="true"></div>
            <div class="relative mx-auto max-w-7xl px-5 sm:px-8">
                <h2 class="text-3xl font-extrabold text-brand-tua sm:text-4xl">Empat Aspek yang Dipantau</h2>
                <p class="mt-4 max-w-2xl text-lg text-ink-muted">Stimulasi dalam program ini menyentuh empat aspek perkembangan anak.</p>
                <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ([
                        ['Motorik Kasar', 'Kemampuan gerak besar seperti berdiri, berjalan, dan memanjat.', 'bg-aspek-kasar text-aspek-kasar-teks', '<circle cx="12" cy="5" r="1" /><path d="m9 20 3-6 3 6" /><path d="m6 8 6 2 6-2" /><path d="M12 10v4" />'],
                        ['Motorik Halus', 'Koordinasi tangan dan mata seperti menggenggam dan menyusun.', 'bg-aspek-halus text-aspek-halus-teks', '<path d="M18 11V6a2 2 0 0 0-4 0" /><path d="M14 10V4a2 2 0 0 0-4 0v2" /><path d="M10 10.5V6a2 2 0 0 0-4 0v8" /><path d="M18 8a2 2 0 1 1 4 0v6a8 8 0 0 1-8 8h-2c-2.8 0-4.5-.86-5.99-2.34l-3.6-3.6a2 2 0 0 1 2.83-2.82L7 15" />'],
                        ['Bicara & Bahasa', 'Kemampuan memahami dan menyampaikan apa yang dirasakan anak.', 'bg-aspek-bicara text-aspek-bicara-teks', '<path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z" />'],
                        ['Sosial & Emosional', 'Interaksi dengan orang lain dan mengenali perasaan.', 'bg-aspek-sosial text-aspek-sosial-teks', '<path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" />'],
                    ] as [$judul, $teks, $warna, $ikon])
                        <div class="h-full rounded-2xl border border-krem-garis bg-white p-6 shadow-xl shadow-ink/5">
                            <span class="flex h-14 w-14 items-center justify-center rounded-full {{ $warna }}">
                                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $ikon !!}</svg>
                            </span>
                            <h3 class="mt-5 text-xl font-bold text-brand-tua">{{ $judul }}</h3>
                            <p class="mt-2 text-base leading-relaxed text-ink-muted">{{ $teks }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- CARA IKUT: urutan sesuai alur aplikasi (registrasi → pre-test → materi & kalender → perkembangan). --}}
        <section id="cara-ikut" class="py-20 sm:py-24">
            <div class="mx-auto max-w-7xl px-5 sm:px-8">
                <h2 class="text-3xl font-extrabold text-brand-tua sm:text-4xl">Cara Mengikuti Program</h2>
                <div class="relative mt-12">
                    <div class="absolute left-7 top-8 hidden h-[calc(100%-4rem)] border-l-2 border-dashed border-brand-soft sm:block lg:left-0 lg:top-7 lg:h-0 lg:w-full lg:border-l-0 lg:border-t-2" aria-hidden="true"></div>
                    <ol class="relative grid gap-8 lg:grid-cols-4">
                        @foreach ([
                            ['Daftar', 'Isi data Ibu dan data anak, hanya beberapa menit.'],
                            ['Isi Kuesioner Awal', 'Untuk mengetahui titik awal sebelum program dimulai.'],
                            ['Praktikkan di Rumah', 'Tonton video singkat, lakukan bersama anak, lalu catat di aplikasi.'],
                            ['Pantau Perkembangan', 'Isi checklist perkembangan dan lihat perubahannya dari waktu ke waktu.'],
                        ] as $i => [$judul, $teks])
                            <li class="flex gap-5 lg:block">
                                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-brand text-xl font-extrabold text-white shadow-lg shadow-brand/40">{{ $i + 1 }}</span>
                                <div class="lg:mt-5 lg:pr-6">
                                    <h3 class="text-xl font-bold text-brand-tua">{{ $judul }}</h3>
                                    <p class="mt-2 text-base leading-relaxed text-ink-muted">{{ $teks }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </section>

        {{-- UNTUK SIAPA --}}
        <section class="relative overflow-hidden py-20 sm:py-24">
            <div class="pointer-events-none absolute -left-16 bottom-10 h-64 w-64 rounded-full bg-hangat-bg blur-sm" aria-hidden="true"></div>
            <div class="relative mx-auto max-w-4xl px-5 sm:px-8">
                <h2 class="text-3xl font-extrabold text-brand-tua sm:text-4xl">Siapa yang Bisa Ikut?</h2>
                <div class="mt-8 rounded-2xl border border-krem-garis bg-white p-7 shadow-xl shadow-ink/5 sm:p-9">
                    <ul class="space-y-4">
                        @foreach ([
                            'Ibu atau pengasuh utama anak usia 12–24 bulan.',
                            'Bersedia melakukan stimulasi di rumah secara rutin.',
                            'Memiliki HP dengan koneksi internet.',
                            'Bersedia mengisi kuesioner awal dan akhir.',
                        ] as $syarat)
                            <li class="flex items-start gap-3">
                                <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-soft">
                                    <svg class="h-5 w-5 text-brand-tua" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5" /></svg>
                                </span>
                                <span class="pt-1 text-base leading-relaxed">{{ $syarat }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <p class="mt-7 flex items-start gap-3 rounded-xl bg-brand-soft p-4 text-base leading-relaxed text-brand-tua">
                        <svg class="mt-0.5 h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 12h.01M15 12h.01" /><path d="M10 16c.5.3 1.2.5 2 .5s1.5-.2 2-.5" /><path d="M19 6.3a9 9 0 0 1 1.8 3.9 2 2 0 0 1 0 3.6 9 9 0 0 1-17.6 0 2 2 0 0 1 0-3.6A9 9 0 0 1 12 3c2 0 3.5 1.1 3.5 2.5s-.9 2.5-2 2.5c-.8 0-1.5-.4-1.5-1" /></svg>
                        Keikutsertaan bersifat sukarela. Ibu boleh berhenti kapan saja tanpa perlu menjelaskan alasannya.
                    </p>
                </div>
            </div>
        </section>

        {{-- TANYA JAWAB (details/summary bawaan browser sebagai accordion) --}}
        <section id="tanya-jawab" class="py-20 sm:py-24">
            <div class="mx-auto max-w-3xl px-5 sm:px-8">
                <h2 class="text-3xl font-extrabold text-brand-tua sm:text-4xl">Tanya Jawab</h2>
                <div class="mt-8 space-y-4">
                    @foreach ([
                        'Apakah program ini berbayar?' => 'Tidak. Program ini gratis untuk semua peserta. Ibu hanya perlu menyiapkan waktu bersama anak di rumah.',
                        'Berapa lama waktu yang dibutuhkan setiap hari?' => 'Sekitar 10 menit sehari. Kegiatannya singkat dan bisa dilakukan sambil bermain bersama anak.',
                        'Apakah perlu memasang aplikasi?' => 'Tidak perlu memasang apa pun. Semua video dan catatan bisa dibuka lewat HP menggunakan tautan yang tim peneliti kirimkan.',
                        'Apakah data saya dan anak saya aman?' => 'Aman. Data hanya digunakan untuk keperluan penelitian, disimpan secara aman, dan nama anak dicatat dalam bentuk inisial saja.',
                        'Bagaimana kalau anak saya belum bisa melakukan sesuatu di checklist?' => 'Tidak apa-apa. Setiap anak tumbuh dengan iramanya sendiri. Ibu cukup mencatat apa adanya, lalu bisa mendiskusikannya dengan tim peneliti kapan saja.',
                    ] as $tanya => $jawab)
                        <details name="faq" class="group rounded-2xl border border-krem-garis bg-white px-5">
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-4 py-5 text-lg font-bold text-brand-tua [&::-webkit-details-marker]:hidden">
                                {{ $tanya }}
                                <svg class="h-5 w-5 shrink-0 transition-transform group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6" /></svg>
                            </summary>
                            <p class="pb-5 text-base leading-relaxed text-ink-muted">{{ $jawab }}</p>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- CTA --}}
        <section class="px-5 pb-20 sm:px-8">
            <div class="relative mx-auto max-w-7xl overflow-hidden rounded-[2rem] bg-brand-soft px-6 py-14 text-center sm:px-12">
                <div class="pointer-events-none absolute -right-12 -top-12 h-56 w-56 rounded-full bg-aspek-bicara blur-sm" aria-hidden="true"></div>
                <div class="pointer-events-none absolute -bottom-16 -left-12 h-56 w-56 rounded-full bg-aspek-sosial blur-sm" aria-hidden="true"></div>
                <div class="relative">
                    <h2 class="text-3xl font-extrabold text-brand-tua sm:text-4xl">Siap Memulai Bersama Si Kecil?</h2>
                    <p class="mx-auto mt-4 max-w-2xl text-lg leading-relaxed text-ink-muted">
                        Cukup beberapa menit untuk mendaftar, lalu Ibu bisa langsung mencoba stimulasi pertama di rumah.
                    </p>
                    <a href="{{ $ctaUrl }}" class="mt-8 inline-flex min-h-14 items-center rounded-xl bg-brand px-8 text-lg font-bold text-white shadow-lg shadow-brand/30 hover:bg-brand-tua">{{ $ctaLabel }}</a>
                </div>
            </div>
        </section>

        {{-- FOOTER --}}
        <footer class="bg-brand-tua text-white">
            <div class="mx-auto max-w-7xl px-5 py-14 sm:px-8">
                <div class="grid gap-10 md:grid-cols-3">
                    <div>
                        <p class="text-xl font-extrabold">Tumbuh Ceria</p>
                        <p class="mt-3 max-w-xs text-base leading-relaxed text-white/80">
                            Program penelitian stimulasi perkembangan anak usia 12–24 bulan untuk dilakukan di rumah.
                        </p>
                    </div>
                    <nav>
                        <p class="text-base font-bold">Navigasi</p>
                        <ul class="mt-4 space-y-1">
                            @foreach ($menu as $href => $label)
                                <li><a href="{{ $href }}" class="flex min-h-12 items-center text-base text-white/80 hover:text-white">{{ $label }}</a></li>
                            @endforeach
                        </ul>
                    </nav>
                    <div>
                        <p class="text-base font-bold">Kontak</p>
                        {{-- Pengunjung belum punya kode responden, jadi pesan pembuka tanpa kode (beda dengan x-nav-konsultasi). --}}
                        @if ($wa)
                            <a href="https://wa.me/{{ $wa }}?text={{ rawurlencode('Halo tim peneliti Tumbuh Ceria, saya ingin bertanya tentang program ini.') }}" target="_blank" rel="noopener"
                               class="mt-4 inline-flex min-h-12 items-center gap-2 rounded-xl bg-white px-5 text-base font-bold text-brand-tua hover:opacity-90">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z" /></svg>
                                Hubungi Tim Peneliti
                            </a>
                        @endif
                        <p class="mt-4 text-base text-white/80">[Nama Institusi]</p>
                    </div>
                </div>

                <div class="mt-12 border-t border-white/20 pt-6">
                    <p class="text-base text-white/70">© {{ date('Y') }} [Nama Institusi]. Seluruh hak cipta.</p>
                </div>
            </div>
        </footer>
    </body>
</html>
