<x-admin-layout judul="Perkembangan">
    <section class="rounded-lg border border-gray-200 bg-white">
        <h2 class="border-b border-gray-200 px-4 py-3 font-semibold text-gray-800">
            Rekap Penilaian Perkembangan per Responden
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
                            <th class="px-4 py-2 font-medium">Jumlah Penilaian</th>
                            <th class="px-4 py-2 font-medium">Penilaian Terakhir</th>
                            <th class="px-4 py-2 font-medium">Skor Total Terakhir</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @foreach ($responden as $r)
                            @php
                                $terakhir = $r->penilaianTerakhir;
                                $selisih = $terakhir ? (int) $terakhir->tanggal_penilaian->diffInDays(today()) : null;
                            @endphp
                            <tr>
                                <td class="whitespace-nowrap px-4 py-2 font-medium">{{ $r->kode_responden }}</td>
                                <td class="px-4 py-2">{{ $r->nama }}</td>
                                <td class="whitespace-nowrap px-4 py-2">{{ $r->penilaian_perkembangan_count }}&times;</td>
                                <td class="whitespace-nowrap px-4 py-2">
                                    @if (! $terakhir)
                                        <span class="text-gray-400">Belum pernah menilai</span>
                                    @else
                                        {{ $terakhir->tanggal_penilaian->format('d/m/Y') }}
                                        <span class="block text-xs text-gray-500">
                                            {{ $selisih === 0 ? 'hari ini' : $selisih.' hari lalu' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-2">
                                    {{ $terakhir ? number_format((float) $terakhir->skor_total, 2, ',', '.').'%' : '—' }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-2 text-right">
                                    <a href="{{ route('admin.perkembangan.show', $r) }}"
                                       class="font-medium text-emerald-700 hover:underline">Riwayat</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-gray-200 px-4 py-3">{{ $responden->links() }}</div>
        @endif
    </section>
</x-admin-layout>
