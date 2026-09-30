@php
    // Badge kategori pengetahuan (PRD §5 enum Baik/Cukup/Kurang).
    $warna = ['Baik' => 'bg-sukses-badge text-sukses', 'Cukup' => 'bg-hangat-bg text-ink', 'Kurang' => 'bg-aspek-sosial text-bahaya'];
@endphp

<x-admin-layout judul="Hasil Test">
    <section class="overflow-hidden rounded-3xl bg-white shadow-sm">
        <h2 class="px-6 py-5 text-lg font-extrabold">
            Hasil Pre-test & Post-test
            <span class="font-bold text-ink-muted">({{ $responden->total() }} responden)</span>
        </h2>

        @if ($responden->isEmpty())
            <p class="px-6 pb-6 text-ink-muted">Belum ada responden terdaftar.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-krem-tua text-left text-sm font-bold text-ink-muted">
                        <tr>
                            <th class="px-6 py-3" rowspan="2">Kode</th>
                            <th class="px-6 py-3" rowspan="2">Nama</th>
                            <th class="border-l border-krem-garis px-6 py-3" colspan="3">Pre-test</th>
                            <th class="border-l border-krem-garis px-6 py-3" colspan="3">Post-test</th>
                            <th class="px-6 py-3" rowspan="2"></th>
                        </tr>
                        <tr>
                            @foreach (['pre', 'post'] as $tipe)
                                <th class="border-l border-krem-garis px-6 py-3">Pengetahuan</th>
                                <th class="px-6 py-3">Kategori</th>
                                <th class="px-6 py-3">Sikap</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-krem-garis">
                        @foreach ($responden as $r)
                            @php $hasil = $r->hasilKuesioner->keyBy('tipe_sesi'); @endphp
                            <tr>
                                <td class="whitespace-nowrap px-6 py-4 font-bold">{{ $r->kode_responden }}</td>
                                <td class="px-6 py-4">{{ $r->nama }}</td>

                                @foreach (['pre', 'post'] as $tipe)
                                    @php $h = $hasil->get($tipe); @endphp
                                    @if (! $h)
                                        <td class="border-l border-krem-garis px-6 py-4 text-ink-muted/60" colspan="3">Belum dikerjakan</td>
                                    @else
                                        <td class="whitespace-nowrap border-l border-krem-garis px-6 py-4">
                                            {{ $h->skor_pengetahuan === null ? '—' : number_format((float) $h->skor_pengetahuan, 2, ',', '.').'%' }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            @if ($h->kategori_pengetahuan)
                                                <span class="rounded-full px-3 py-1 text-sm font-bold {{ $warna[$h->kategori_pengetahuan] ?? 'bg-krem-tua text-ink' }}">
                                                    {{ $h->kategori_pengetahuan }}
                                                </span>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            {{ $h->skor_sikap === null ? '—' : number_format((float) $h->skor_sikap, 2, ',', '.') }}
                                        </td>
                                    @endif
                                @endforeach

                                <td class="whitespace-nowrap px-6 py-4 text-right">
                                    <a href="{{ route('admin.hasil-test.show', $r) }}"
                                       class="font-bold text-ink hover:text-brand">Detail</a>
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
