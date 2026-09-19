<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kalender Stimulasi</h2>
        <p class="text-sm text-gray-500">Catat setiap kali Anda melakukan stimulasi. Boleh diisi berkali-kali.</p>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <p class="rounded-md bg-emerald-50 px-4 py-3 text-emerald-800">✓ {{ session('status') }}</p>
            @endif

            <section class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6 text-base text-gray-800">
                <div class="flex items-center justify-between gap-2">
                    <a href="{{ route('aktivitas.index', ['bulan' => $bulan->copy()->subMonth()->format('Y-m')]) }}"
                       class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50" aria-label="Bulan sebelumnya">‹</a>
                    <h3 class="font-semibold text-gray-800">{{ $bulan->translatedFormat('F Y') }}</h3>
                    @if ($bulan->lessThan($bulanIni))
                        <a href="{{ route('aktivitas.index', ['bulan' => $bulan->copy()->addMonth()->format('Y-m')]) }}"
                           class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50" aria-label="Bulan berikutnya">›</a>
                    @else
                        <span class="px-4 py-2 text-sm text-gray-300" aria-hidden="true">›</span>
                    @endif
                </div>

                <div class="mt-4 grid grid-cols-7 gap-1 text-center">
                    @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $hari)
                        <div class="py-1 text-xs font-medium text-gray-500">{{ $hari }}</div>
                    @endforeach
                    @for ($i = 1; $i < $bulan->dayOfWeekIso; $i++)
                        <div></div>
                    @endfor
                    @for ($tgl = 1; $tgl <= $bulan->daysInMonth; $tgl++)
                        @php $hariIni = $bulan->isSameMonth(now()) && $tgl === now()->day; @endphp
                        @if ($jumlah = $penanda[$tgl] ?? 0)
                            <a href="#tgl-{{ $bulan->copy()->day($tgl)->toDateString() }}"
                               class="flex flex-col items-center rounded-md bg-emerald-50 py-2 font-medium text-emerald-800 hover:bg-emerald-100 {{ $hariIni ? 'ring-2 ring-indigo-500' : '' }}"
                               title="{{ $jumlah }} catatan">
                                {{ $tgl }}
                                <span class="mt-1 h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                            </a>
                        @else
                            <div class="flex flex-col items-center rounded-md py-2 text-gray-700 {{ $hariIni ? 'ring-2 ring-indigo-500' : '' }}">
                                {{ $tgl }}
                                <span class="mt-1 h-1.5 w-1.5"></span>
                            </div>
                        @endif
                    @endfor
                </div>
                <p class="mt-3 text-sm text-gray-500">
                    <span class="inline-block h-1.5 w-1.5 rounded-full bg-emerald-600 align-middle"></span>
                    Tanggal yang sudah ada catatan — ketuk untuk melihat riwayatnya.
                </p>
            </section>

            <section class="bg-white shadow-sm sm:rounded-lg p-6 text-base text-gray-800">
                <h3 class="font-semibold text-gray-800">Tambah catatan stimulasi</h3>
                <form method="POST" action="{{ route('aktivitas.store') }}" class="mt-4 space-y-4">
                    @csrf
                    @include('aktivitas._form', ['entri' => null])
                    <button type="submit" class="w-full rounded-md bg-indigo-600 px-4 py-3 text-base font-medium text-white shadow-sm hover:bg-indigo-700">
                        Simpan catatan
                    </button>
                </form>
            </section>

            <section class="bg-white shadow-sm sm:rounded-lg p-6 text-base text-gray-800">
                <h3 class="font-semibold text-gray-800">Riwayat stimulasi ({{ $riwayat->count() }})</h3>
                @forelse ($riwayat as $entri)
                    @php $tanggal = $entri->tanggal->toDateString(); @endphp
                    {{-- Anchor hanya di entri pertama tiap tanggal (target link dari grid kalender). --}}
                    <div @if ($tanggal !== ($tanggalSebelum ?? null)) id="tgl-{{ $tanggal }}" @endif class="mt-3 border-t border-gray-100 pt-3 scroll-mt-4">
                    @php $tanggalSebelum = $tanggal; @endphp
                        <p class="text-sm font-medium text-gray-600">{{ $entri->tanggal->translatedFormat('l, d F Y') }}</p>
                        <p class="mt-1 font-medium">
                            {{ \App\Models\Materi::ASPEK[$entri->aspek] }}{{ $entri->jenis_stimulasi ? ' — '.$entri->jenis_stimulasi : '' }}
                        </p>
                        <p class="text-sm text-gray-600">
                            Oleh {{ \App\Models\AktivitasStimulasi::PELAKU[$entri->pelaku] }}{{ $entri->durasi_menit ? ' · '.$entri->durasi_menit.' menit' : '' }}{{ $entri->materi ? ' · dari Praktik: '.$entri->materi->judul : '' }}
                        </p>
                        @if ($entri->respons_anak)
                            <p class="mt-1 whitespace-pre-line">{{ $entri->respons_anak }}</p>
                        @endif
                        <div class="mt-2 flex gap-2">
                            <a href="{{ route('aktivitas.edit', $entri) }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Ubah</a>
                            <form method="POST" action="{{ route('aktivitas.destroy', $entri) }}" onsubmit="return confirm('Hapus catatan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-md border border-red-300 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-50">Hapus</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="mt-2 text-gray-500">Belum ada catatan stimulasi.</p>
                @endforelse
            </section>
        </div>
    </div>
</x-app-layout>
