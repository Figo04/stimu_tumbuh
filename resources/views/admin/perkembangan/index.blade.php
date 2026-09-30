<x-admin-layout judul="Perkembangan">
    <section class="overflow-hidden rounded-3xl bg-white shadow-sm">
        <h2 class="px-6 py-5 text-lg font-extrabold">
            Rekap Penilaian Perkembangan per Responden
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
                            <th class="px-6 py-3">Jumlah Penilaian</th>
                            <th class="px-6 py-3">Penilaian Terakhir</th>
                            <th class="px-6 py-3">Skor Total Terakhir</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-krem-garis">
                        @foreach ($responden as $r)
                            @php
                                $terakhir = $r->penilaianTerakhir;
                                $selisih = $terakhir ? (int) $terakhir->tanggal_penilaian->diffInDays(today()) : null;
                            @endphp
                            <tr>
                                <td class="whitespace-nowrap px-6 py-4 font-bold">{{ $r->kode_responden }}</td>
                                <td class="px-6 py-4">{{ $r->nama }}</td>
                                <td class="whitespace-nowrap px-6 py-4">{{ $r->penilaian_perkembangan_count }}&times;</td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    @if (! $terakhir)
                                        <span class="text-ink-muted/60">Belum pernah menilai</span>
                                    @else
                                        {{ $terakhir->tanggal_penilaian->format('d/m/Y') }}
                                        <span class="block text-xs text-ink-muted">
                                            {{ $selisih === 0 ? 'hari ini' : $selisih.' hari lalu' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    {{ $terakhir ? number_format((float) $terakhir->skor_total, 2, ',', '.').'%' : '—' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right">
                                    <a href="{{ route('admin.perkembangan.show', $r) }}"
                                       class="font-bold text-ink hover:text-brand">Riwayat</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-krem-garis px-6 py-4">{{ $responden->links() }}</div>
        @endif
    </section>
</x-admin-layout>
