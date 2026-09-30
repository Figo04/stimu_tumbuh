<x-admin-layout judul="Dashboard">
    <x-slot name="aksi">
        @foreach ([
            ['admin.export.excel', 'Export Excel', 'Excel 5 sheet: identitas, kondisi lahir, aktivitas stimulasi, perkembangan, penggunaan aplikasi'],
            ['admin.export.csv', 'Export CSV', 'CSV ringkas: identitas + hasil test'],
        ] as [$rute, $label, $isi])
            <a href="{{ route($rute) }}" title="{{ $isi }}"
               class="flex items-center gap-2 rounded-2xl border border-krem-garis bg-white px-4 py-2.5 font-bold shadow-sm hover:bg-krem">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 15V3" /><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" /><path d="m7 10 5 5 5-5" /></svg>
                {{ $label }}
            </a>
        @endforeach
    </x-slot>

    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 2xl:grid-cols-6">
        @foreach ($kartu as $label => $nilai)
            <div class="rounded-3xl p-5 shadow-sm {{ $loop->last ? 'bg-hangat-bg' : 'bg-white' }}">
                <p class="text-sm text-ink-muted">{{ $label }}</p>
                <p class="mt-2 text-4xl font-extrabold">{{ $nilai }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid gap-4 md:grid-cols-2">
        @foreach ([
            ['Rata-rata Frekuensi Stimulasi', $rataStimulasi['frekuensi'], 'kali/minggu',
                'Per responden selama rentang aktifnya (entri pertama sampai terakhir).'],
            ['Rata-rata Durasi per Sesi', $rataStimulasi['durasi'], 'menit',
                'Entri dari tab Praktik tidak mencatat durasi, jadi tidak ikut dihitung.'],
        ] as [$label, $nilai, $satuan, $catatan])
            <div class="rounded-3xl bg-white p-6 shadow-sm">
                <p class="text-ink-muted">{{ $label }}</p>
                <p class="mt-2 text-4xl font-extrabold">
                    @if ($nilai === null)
                        —
                    @else
                        {{ number_format($nilai, 1, ',', '.') }}
                        <span class="text-xl font-bold text-ink-muted">{{ $satuan }}</span>
                    @endif
                </p>
                <p class="mt-2 text-sm text-ink-muted">{{ $catatan }}</p>
            </div>
        @endforeach
    </div>

    @php
        // Warna mengikuti mockup: selesai = brand, berjalan = hangat, belum = sukses. Urutan = urutan data controller.
        $status = ['#48A874', '#EDA065', '#2E8C76'];
        $chart = [
            ['Status Pengisian Tes', 'doughnut', $statusTest, 'responden', $status],
            ['Progres Materi', 'doughnut', $progresMateri, 'responden', $status],
            ['Sebaran Kelompok Usia Anak', 'bar', $sebaranUsia, 'anak', ['#2E8C76']],
            ['Rata-rata Skor per Aspek Perkembangan', 'bar', $rataSkorAspek, '% "Ya"', ['#2E8C76', '#EDA065', '#418DC2', '#D66E74']],
        ];
    @endphp

    <div class="mt-6 grid gap-4 xl:grid-cols-2">
        @foreach ($chart as [$judul, $tipe, $data, $satuan, $warna])
            <section class="rounded-3xl bg-white p-6 shadow-sm">
                <h2 class="text-lg font-extrabold">{{ $judul }}</h2>

                @if (array_sum($data) <= 0)
                    <p class="py-16 text-center text-ink-muted">Belum ada data.</p>
                @elseif ($tipe === 'doughnut')
                    <div class="mt-4 flex flex-col items-center gap-8 sm:flex-row sm:justify-center">
                        <div class="h-56 w-56 shrink-0">
                            <canvas data-chart data-tipe="doughnut" data-satuan="{{ $satuan }}" data-warna="{{ json_encode($warna) }}"
                                    data-label="{{ json_encode(array_keys($data)) }}"
                                    data-nilai="{{ json_encode(array_values($data)) }}"></canvas>
                        </div>
                        {{-- Legenda HTML (bukan legenda Chart.js) supaya angka ikut tampil; yang paling maju di atas. --}}
                        <ul class="space-y-3 text-lg">
                            @foreach (array_reverse(array_keys($data)) as $i => $label)
                                <li class="flex items-center gap-3">
                                    <span class="h-3.5 w-3.5 rounded-full" style="background: {{ array_reverse($warna)[$i] }}"></span>
                                    {{ $label }}: <strong>{{ $data[$label] }}</strong>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="mt-4 h-64">
                        <canvas data-chart data-tipe="bar" data-satuan="{{ $satuan }}" data-warna="{{ json_encode($warna) }}"
                                data-label="{{ json_encode(array_keys($data)) }}"
                                data-nilai="{{ json_encode(array_values($data)) }}"></canvas>
                    </div>
                @endif
            </section>
        @endforeach
    </div>

    @vite('resources/js/chart.js')

    <section class="mt-6 overflow-hidden rounded-3xl bg-white shadow-sm">
        <h2 class="px-6 py-5 text-lg font-extrabold">Aktivitas Terbaru</h2>

        @if ($terbaru->isEmpty())
            <p class="px-6 pb-6 text-ink-muted">Belum ada entri stimulasi dari responden.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-krem-tua text-left text-sm font-bold text-ink-muted">
                        <tr>
                            <th class="px-6 py-3">Tanggal</th>
                            <th class="px-6 py-3">Responden</th>
                            <th class="px-6 py-3">Aspek</th>
                            <th class="px-6 py-3">Jenis</th>
                            <th class="px-6 py-3">Durasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-krem-garis">
                        @foreach ($terbaru as $entri)
                            <tr>
                                <td class="whitespace-nowrap px-6 py-4">{{ $entri->tanggal->format('d/m/Y') }}</td>
                                <td class="px-6 py-4">
                                    <span class="font-bold">{{ $entri->user->kode_responden }}</span>
                                    <span class="block text-sm text-ink-muted">{{ $entri->user->nama }}</span>
                                </td>
                                <td class="px-6 py-4"><x-chip-aspek :aspek="$entri->aspek" class="whitespace-nowrap" /></td>
                                <td class="px-6 py-4">
                                    {{ $entri->jenis_stimulasi ?? ($entri->materi ? 'Praktik: '.$entri->materi->judul : '—') }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">{{ $entri->durasi_menit ? $entri->durasi_menit.' menit' : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</x-admin-layout>
