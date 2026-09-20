<x-admin-layout judul="Dashboard">
    <div class="mb-4 flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.export.excel') }}"
           class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
            Export Excel (5 sheet)
        </a>
        <a href="{{ route('admin.export.csv') }}"
           class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
            Export CSV (ringkas)
        </a>
        <span class="text-xs text-gray-500">
            Excel berisi identitas, kondisi lahir, aktivitas stimulasi, perkembangan, dan penggunaan aplikasi.
            CSV berisi identitas + hasil test saja.
        </span>
    </div>

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-3">
        @foreach ($kartu as $label => $nilai)
            <div class="rounded-lg border border-gray-200 bg-white p-4">
                <p class="text-sm text-gray-500">{{ $label }}</p>
                <p class="mt-1 text-3xl font-semibold text-gray-800">{{ $nilai }}</p>
            </div>
        @endforeach
    </div>

    @php
        $chart = [
            ['Status Test', 'doughnut', $statusTest, 'responden'],
            ['Progres Materi', 'doughnut', $progresMateri, 'responden'],
            ['Sebaran Kelompok Usia Anak', 'bar', $sebaranUsia, 'anak'],
            ['Rata-rata Skor per Aspek Perkembangan', 'bar', $rataSkorAspek, '% "Ya"'],
        ];
    @endphp

    <div class="mt-6 grid gap-4 xl:grid-cols-2">
        @foreach ($chart as [$judul, $tipe, $data, $satuan])
            <section class="rounded-lg border border-gray-200 bg-white p-4">
                <h2 class="font-semibold text-gray-800">{{ $judul }}</h2>

                @if (array_sum($data) <= 0)
                    <p class="py-10 text-center text-sm text-gray-500">Belum ada data.</p>
                @else
                    <div class="mt-3 h-64">
                        <canvas data-chart data-tipe="{{ $tipe }}" data-satuan="{{ $satuan }}"
                                data-label="{{ json_encode(array_keys($data)) }}"
                                data-nilai="{{ json_encode(array_values($data)) }}"></canvas>
                    </div>
                @endif
            </section>
        @endforeach
    </div>

    @vite('resources/js/chart.js')

    <div class="mt-6 grid gap-4 sm:grid-cols-2">
        @foreach ([
            ['Rata-rata Frekuensi Stimulasi', $rataStimulasi['frekuensi'], 'kali/minggu',
                'Dihitung per responden selama rentang aktifnya (entri pertama sampai terakhir).'],
            ['Rata-rata Durasi per Sesi', $rataStimulasi['durasi'], 'menit',
                'Entri dari tab Praktik tidak mencatat durasi, jadi tidak ikut dihitung.'],
        ] as [$label, $nilai, $satuan, $catatan])
            <div class="rounded-lg border border-gray-200 bg-white p-4">
                <p class="text-sm text-gray-500">{{ $label }}</p>
                <p class="mt-1 text-3xl font-semibold text-gray-800">
                    @if ($nilai === null)
                        —
                    @else
                        {{ number_format($nilai, 1, ',', '.') }}
                        <span class="text-base font-normal text-gray-500">{{ $satuan }}</span>
                    @endif
                </p>
                <p class="mt-1 text-xs text-gray-400">{{ $catatan }}</p>
            </div>
        @endforeach
    </div>

    <section class="mt-6 rounded-lg border border-gray-200 bg-white">
        <h2 class="border-b border-gray-200 px-4 py-3 font-semibold text-gray-800">Aktivitas Terbaru</h2>

        @if ($terbaru->isEmpty())
            <p class="px-4 py-6 text-sm text-gray-500">Belum ada entri stimulasi dari responden.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-4 py-2 font-medium">Tanggal</th>
                            <th class="px-4 py-2 font-medium">Responden</th>
                            <th class="px-4 py-2 font-medium">Aspek</th>
                            <th class="px-4 py-2 font-medium">Jenis</th>
                            <th class="px-4 py-2 font-medium">Durasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @foreach ($terbaru as $entri)
                            <tr>
                                <td class="whitespace-nowrap px-4 py-2">{{ $entri->tanggal->format('d/m/Y') }}</td>
                                <td class="px-4 py-2">
                                    {{ $entri->user->kode_responden }}
                                    <span class="text-gray-500">— {{ $entri->user->nama }}</span>
                                </td>
                                <td class="px-4 py-2">{{ \App\Models\Materi::ASPEK[$entri->aspek] }}</td>
                                <td class="px-4 py-2">
                                    {{ $entri->jenis_stimulasi ?? ($entri->materi ? 'Praktik: '.$entri->materi->judul : '—') }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-2">{{ $entri->durasi_menit ? $entri->durasi_menit.' menit' : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</x-admin-layout>
