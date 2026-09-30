<x-admin-layout judul="Aktivitas Stimulasi">
    <section class="overflow-hidden rounded-3xl bg-white shadow-sm">
        <h2 class="px-6 py-5 text-lg font-extrabold">
            Rekap Aktivitas Stimulasi per Responden
            <span class="font-bold text-ink-muted">({{ $responden->total() }} responden)</span>
        </h2>

        @if ($responden->isEmpty())
            <p class="px-6 pb-6 text-ink-muted">Belum ada responden terdaftar.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-krem-tua text-left text-sm font-bold text-ink-muted">
                        <tr>
                            <th class="px-6 py-3">Kode</th>
                            <th class="px-6 py-3">Nama</th>
                            <th class="px-6 py-3">Jumlah Entri</th>
                            <th class="px-6 py-3">Total Durasi</th>
                            <th class="px-6 py-3">Entri Terakhir</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-krem-garis">
                        @foreach ($responden as $r)
                            @php
                                $terakhir = $r->aktivitas_stimulasi_max_tanggal;
                                // tanggal entri selalu <= hari ini (validasi Sesi 20), jadi selisihnya tidak negatif.
                                $selisih = $terakhir ? (int) $terakhir->diffInDays(today()) : null;
                            @endphp
                            <tr>
                                <td class="whitespace-nowrap px-6 py-4 font-bold">{{ $r->kode_responden }}</td>
                                <td class="px-6 py-4">{{ $r->nama }}</td>
                                <td class="whitespace-nowrap px-6 py-4">{{ $r->aktivitas_stimulasi_count }} entri</td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    {{ $r->aktivitas_stimulasi_sum_durasi_menit
                                        ? number_format((int) $r->aktivitas_stimulasi_sum_durasi_menit, 0, ',', '.').' menit'
                                        : '—' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    @if (! $terakhir)
                                        <span class="text-ink-muted/60">Belum ada entri</span>
                                    @else
                                        {{ $terakhir->format('d/m/Y') }}
                                        <span class="block text-xs text-ink-muted">
                                            {{ $selisih === 0 ? 'hari ini' : $selisih.' hari lalu' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right">
                                    <a href="{{ route('admin.aktivitas.show', $r) }}"
                                       class="font-bold text-ink hover:text-brand">Riwayat</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <p class="border-t border-krem-garis px-6 py-4 text-xs text-ink-muted">
                Total durasi hanya menjumlahkan entri yang mengisi durasi — entri dari tab Praktik tidak mencatat durasi,
                jadi tetap terhitung sebagai entri tapi tidak menambah total durasi.
            </p>

            <div class="border-t border-krem-garis px-6 py-4">{{ $responden->links() }}</div>
        @endif
    </section>
</x-admin-layout>
