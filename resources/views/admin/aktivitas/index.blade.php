<x-admin-layout judul="Aktivitas Stimulasi">
    <section class="rounded-lg border border-gray-200 bg-white">
        <h2 class="border-b border-gray-200 px-4 py-3 font-semibold text-gray-800">
            Rekap Aktivitas Stimulasi per Responden
            <span class="font-normal text-gray-500">({{ $responden->total() }} responden)</span>
        </h2>

        @if ($responden->isEmpty())
            <p class="px-4 py-6 text-sm text-gray-500">Belum ada responden terdaftar.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-4 py-2 font-medium">Kode</th>
                            <th class="px-4 py-2 font-medium">Nama</th>
                            <th class="px-4 py-2 font-medium">Jumlah Entri</th>
                            <th class="px-4 py-2 font-medium">Total Durasi</th>
                            <th class="px-4 py-2 font-medium">Entri Terakhir</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @foreach ($responden as $r)
                            @php
                                $terakhir = $r->aktivitas_stimulasi_max_tanggal;
                                // tanggal entri selalu <= hari ini (validasi Sesi 20), jadi selisihnya tidak negatif.
                                $selisih = $terakhir ? (int) $terakhir->diffInDays(today()) : null;
                            @endphp
                            <tr>
                                <td class="whitespace-nowrap px-4 py-2 font-medium">{{ $r->kode_responden }}</td>
                                <td class="px-4 py-2">{{ $r->nama }}</td>
                                <td class="whitespace-nowrap px-4 py-2">{{ $r->aktivitas_stimulasi_count }} entri</td>
                                <td class="whitespace-nowrap px-4 py-2">
                                    {{ $r->aktivitas_stimulasi_sum_durasi_menit
                                        ? number_format((int) $r->aktivitas_stimulasi_sum_durasi_menit, 0, ',', '.').' menit'
                                        : '—' }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-2">
                                    @if (! $terakhir)
                                        <span class="text-gray-400">Belum ada entri</span>
                                    @else
                                        {{ $terakhir->format('d/m/Y') }}
                                        <span class="block text-xs text-gray-500">
                                            {{ $selisih === 0 ? 'hari ini' : $selisih.' hari lalu' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-2 text-right">
                                    <a href="{{ route('admin.aktivitas.show', $r) }}"
                                       class="font-medium text-emerald-700 hover:underline">Riwayat</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <p class="border-t border-gray-200 px-4 py-3 text-xs text-gray-500">
                Total durasi hanya menjumlahkan entri yang mengisi durasi — entri dari tab Praktik tidak mencatat durasi,
                jadi tetap terhitung sebagai entri tapi tidak menambah total durasi.
            </p>

            <div class="border-t border-gray-200 px-4 py-3">{{ $responden->links() }}</div>
        @endif
    </section>
</x-admin-layout>
