<x-app-layout>
    {{-- tambah: lembar form dari bawah; terbuka lagi otomatis bila validasi gagal. --}}
    {{-- pb-20: ruang agar tombol + tidak menutupi kartu terakhir. --}}
    <div class="mx-auto max-w-2xl px-4 pb-20 pt-6" x-data="{ tambah: @js($errors->any()) }" @keydown.escape.window="tambah = false">
        <h1 class="text-3xl font-extrabold">Kalender Stimulasi</h1>
        <p class="mt-1 text-lg text-ink-muted">Catatan kecil yang tumbuh bersama {{ rtrim(Auth::user()->anak?->nama_inisial ?? 'si kecil', '.') }}.</p>

        @if (session('status'))
            <x-auth-session-status class="mt-4" :status="'✓ '.session('status')" />
        @endif

        <section class="mt-6 rounded-3xl bg-white p-4 shadow-sm sm:p-6">
            <div class="flex items-center justify-between gap-2">
                <a href="{{ route('aktivitas.index', ['bulan' => $bulan->copy()->subMonth()->format('Y-m')]) }}"
                   class="flex h-11 w-11 items-center justify-center rounded-full hover:bg-krem" aria-label="Bulan sebelumnya">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6" /></svg>
                </a>
                <h2 class="text-lg font-extrabold">{{ $bulan->translatedFormat('F Y') }}</h2>
                @if ($bulan->lessThan($bulanIni))
                    <a href="{{ route('aktivitas.index', ['bulan' => $bulan->copy()->addMonth()->format('Y-m')]) }}"
                       class="flex h-11 w-11 items-center justify-center rounded-full hover:bg-krem" aria-label="Bulan berikutnya">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6" /></svg>
                    </a>
                @else
                    <span class="flex h-11 w-11 items-center justify-center text-krem-garis" aria-hidden="true">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6" /></svg>
                    </span>
                @endif
            </div>

            <div class="mt-4 grid grid-cols-7 gap-y-1 text-center">
                @foreach (['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $hari)
                    <div class="py-2 text-sm font-bold text-ink-muted">{{ $hari }}</div>
                @endforeach
                {{-- Minggu di kolom pertama (mockup): dayOfWeek 0 = Minggu. --}}
                @for ($i = 0; $i < $bulan->dayOfWeek; $i++)
                    <div></div>
                @endfor
                @for ($tgl = 1; $tgl <= $bulan->daysInMonth; $tgl++)
                    @php
                        $hariIni = $bulan->isSameMonth(now()) && $tgl === now()->day;
                        $jumlah = $penanda[$tgl] ?? 0;
                        $lingkar = $hariIni ? 'bg-brand text-white' : ($jumlah ? 'text-ink hover:bg-brand-soft' : 'text-ink');
                    @endphp
                    <div class="flex justify-center">
                        @if ($jumlah)
                            <a href="#tgl-{{ $bulan->copy()->day($tgl)->toDateString() }}" title="{{ $jumlah }} catatan"
                               class="relative flex h-11 w-11 items-center justify-center rounded-full font-bold {{ $lingkar }}">
                                {{ $tgl }}
                                <span class="absolute bottom-1 h-1.5 w-1.5 rounded-full {{ $hariIni ? 'bg-white' : 'bg-brand' }}"></span>
                            </a>
                        @else
                            <span class="flex h-11 w-11 items-center justify-center rounded-full font-bold {{ $lingkar }}">{{ $tgl }}</span>
                        @endif
                    </div>
                @endfor
            </div>
            <p class="mt-3 flex items-center gap-2 text-sm text-ink-muted">
                <span class="inline-block h-1.5 w-1.5 rounded-full bg-brand"></span>
                Ada catatan — ketuk tanggalnya untuk melihat.
            </p>
        </section>

        <div class="mt-4 grid grid-cols-3 gap-3">
            @foreach ([
                'Minggu ini' => $ringkasan['mingguIni'].' kali',
                'Total' => $ringkasan['total'].' kali',
                'Rata-rata' => $ringkasan['rataDurasi'] ? round($ringkasan['rataDurasi']).' menit' : '–',
            ] as $label => $nilai)
                <div class="rounded-2xl bg-white px-2 py-4 text-center shadow-sm">
                    <p class="text-sm text-ink-muted">{{ $label }}</p>
                    <p class="mt-1 text-lg font-extrabold">{{ $nilai }}</p>
                </div>
            @endforeach
        </div>

        @forelse ($perTanggal as $tanggal => $entriHari)
            @php($tgl = $entriHari->first()->tanggal)
            <section id="tgl-{{ $tanggal }}" class="mt-8 scroll-mt-24">
                <h2 class="text-xl font-extrabold">{{ $tgl->translatedFormat($tgl->isCurrentYear() ? 'j F' : 'j F Y') }}</h2>

                <div class="mt-3 space-y-3">
                    @foreach ($entriHari as $entri)
                        <article class="rounded-3xl bg-white p-5 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <x-chip-aspek :aspek="$entri->aspek" />
                                <div class="-mr-2 -mt-1 flex shrink-0">
                                    <a href="{{ route('aktivitas.edit', $entri) }}" class="flex h-11 w-11 items-center justify-center rounded-full text-ink hover:bg-krem" aria-label="Ubah catatan" title="Ubah">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21.17 6.81a1 1 0 0 0-3.99-3.99L3.84 16.17a2 2 0 0 0-.5.83l-1.32 4.35a.5.5 0 0 0 .62.62l4.35-1.32a2 2 0 0 0 .83-.5z" /></svg>
                                    </a>
                                    <form method="POST" action="{{ route('aktivitas.destroy', $entri) }}" onsubmit="return confirm('Hapus catatan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="flex h-11 w-11 items-center justify-center rounded-full text-bahaya hover:bg-aspek-sosial" aria-label="Hapus catatan" title="Hapus">
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18" /><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" /><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" /><path d="M10 11v6" /><path d="M14 11v6" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <p class="mt-3 text-lg font-extrabold">
                                {{ $entri->jenis_stimulasi ?? ($entri->materi ? 'Praktik: '.$entri->materi->judul : 'Stimulasi') }}{{ $entri->durasi_menit ? ' · '.$entri->durasi_menit.' menit' : '' }}
                            </p>
                            @if ($entri->respons_anak)
                                <p class="mt-1 whitespace-pre-line text-lg text-ink-muted">{{ $entri->respons_anak }}</p>
                            @endif
                            <p class="mt-2 text-sm text-ink-muted">Oleh {{ \App\Models\AktivitasStimulasi::PELAKU[$entri->pelaku] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>
        @empty
            <div class="mt-8 rounded-3xl bg-white p-6 text-center shadow-sm">
                <p class="text-lg font-extrabold">Belum ada catatan stimulasi</p>
                <p class="mt-1 text-ink-muted">Ketuk tombol + untuk mencatat stimulasi pertama. Boleh diisi berkali-kali.</p>
            </div>
        @endforelse

        {{-- Tombol tambah melayang, di atas tab bar. --}}
        <button type="button" @click="tambah = true" aria-label="Tambah catatan stimulasi"
                class="fixed bottom-24 right-4 z-30 flex h-16 w-16 items-center justify-center rounded-full bg-brand text-white shadow-lg shadow-brand/30 hover:bg-brand/90 sm:right-[max(1rem,calc(50%-20rem))]">
            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14" /></svg>
        </button>

        <div x-show="tambah" x-cloak class="fixed inset-0 z-40 flex items-end justify-center bg-ink/40 sm:items-center" @click.self="tambah = false">
            <section role="dialog" aria-modal="true" aria-labelledby="judul-tambah"
                     class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-t-3xl bg-white p-6 sm:rounded-3xl">
                <div class="flex items-center justify-between gap-4">
                    <h2 id="judul-tambah" class="text-xl font-extrabold">Tambah catatan stimulasi</h2>
                    <button type="button" @click="tambah = false" class="flex h-11 w-11 items-center justify-center rounded-full hover:bg-krem" aria-label="Tutup">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12" /></svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('aktivitas.store') }}" class="mt-4 space-y-5">
                    @csrf
                    @include('aktivitas._form', ['entri' => null])
                    <x-primary-button class="w-full">Simpan catatan</x-primary-button>
                </form>
            </section>
        </div>
    </div>
</x-app-layout>
