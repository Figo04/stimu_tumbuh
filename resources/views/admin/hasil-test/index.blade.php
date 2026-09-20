@php
    // Badge kategori pengetahuan (PRD §5 enum Baik/Cukup/Kurang).
    $warna = ['Baik' => 'bg-emerald-100 text-emerald-800', 'Cukup' => 'bg-amber-100 text-amber-800', 'Kurang' => 'bg-rose-100 text-rose-800'];
@endphp

<x-admin-layout judul="Hasil Test">
    <section class="rounded-lg border border-gray-200 bg-white">
        <h2 class="border-b border-gray-200 px-4 py-3 font-semibold text-gray-800">
            Hasil Pre-test & Post-test
            <span class="font-normal text-gray-500">({{ $responden->total() }} responden)</span>
        </h2>

        @if ($responden->isEmpty())
            <p class="px-4 py-6 text-sm text-gray-500">Belum ada responden terdaftar.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-4 py-2 font-medium" rowspan="2">Kode</th>
                            <th class="px-4 py-2 font-medium" rowspan="2">Nama</th>
                            <th class="border-l border-gray-200 px-4 py-2 font-medium" colspan="3">Pre-test</th>
                            <th class="border-l border-gray-200 px-4 py-2 font-medium" colspan="3">Post-test</th>
                            <th class="px-4 py-2" rowspan="2"></th>
                        </tr>
                        <tr>
                            @foreach (['pre', 'post'] as $tipe)
                                <th class="border-l border-gray-200 px-4 py-2 font-medium">Pengetahuan</th>
                                <th class="px-4 py-2 font-medium">Kategori</th>
                                <th class="px-4 py-2 font-medium">Sikap</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @foreach ($responden as $r)
                            @php $hasil = $r->hasilKuesioner->keyBy('tipe_sesi'); @endphp
                            <tr>
                                <td class="whitespace-nowrap px-4 py-2 font-medium">{{ $r->kode_responden }}</td>
                                <td class="px-4 py-2">{{ $r->nama }}</td>

                                @foreach (['pre', 'post'] as $tipe)
                                    @php $h = $hasil->get($tipe); @endphp
                                    @if (! $h)
                                        <td class="border-l border-gray-200 px-4 py-2 text-gray-400" colspan="3">Belum dikerjakan</td>
                                    @else
                                        <td class="whitespace-nowrap border-l border-gray-200 px-4 py-2">
                                            {{ $h->skor_pengetahuan === null ? '—' : number_format((float) $h->skor_pengetahuan, 2, ',', '.').'%' }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-2">
                                            @if ($h->kategori_pengetahuan)
                                                <span class="rounded px-2 py-0.5 text-xs font-medium {{ $warna[$h->kategori_pengetahuan] ?? 'bg-gray-100 text-gray-700' }}">
                                                    {{ $h->kategori_pengetahuan }}
                                                </span>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-2">
                                            {{ $h->skor_sikap === null ? '—' : number_format((float) $h->skor_sikap, 2, ',', '.') }}
                                        </td>
                                    @endif
                                @endforeach

                                <td class="whitespace-nowrap px-4 py-2 text-right">
                                    <a href="{{ route('admin.hasil-test.show', $r) }}"
                                       class="font-medium text-emerald-700 hover:underline">Detail</a>
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
