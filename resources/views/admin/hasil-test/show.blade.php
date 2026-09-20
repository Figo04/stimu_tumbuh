@php
    $label = ['pre' => 'Pre-test', 'post' => 'Post-test'];
    // Dipisah per tipe: kode "S" berarti Salah di pengetahuan, Setuju di sikap.
    $jawabanLabel = fn ($soal, $kode) => ($soal->tipe === 'pengetahuan'
        ? \App\Models\KuesionerSoal::JAWABAN_PENGETAHUAN
        : \App\Models\KuesionerSoal::JAWABAN_SIKAP)[$kode] ?? $kode;
    $warna = ['Baik' => 'bg-emerald-100 text-emerald-800', 'Cukup' => 'bg-amber-100 text-amber-800', 'Kurang' => 'bg-rose-100 text-rose-800'];
@endphp

<x-admin-layout judul="Hasil Test {{ $responden->kode_responden }}">
    <a href="{{ route('admin.hasil-test.index') }}" class="text-sm font-medium text-emerald-700 hover:underline">
        &larr; Kembali ke daftar hasil test
    </a>

    <p class="mt-2 text-sm text-gray-600">
        {{ $responden->kode_responden }} — {{ $responden->nama }}
        (<a href="{{ route('admin.responden.show', $responden) }}" class="text-emerald-700 hover:underline">identitas lengkap</a>)
    </p>

    @foreach (['pre', 'post'] as $tipe)
        @php $h = $hasil->get($tipe); @endphp

        <section class="mt-4 rounded-lg border border-gray-200 bg-white">
            <h2 class="border-b border-gray-200 px-4 py-3 font-semibold text-gray-800">{{ $label[$tipe] }}</h2>

            @if (! $h)
                <p class="px-4 py-6 text-sm text-gray-500">Belum dikerjakan responden ini.</p>
            @else
                <div class="grid gap-4 px-4 py-3 text-sm sm:grid-cols-4">
                    <div>
                        <p class="text-gray-500">Skor pengetahuan</p>
                        <p class="text-xl font-semibold text-gray-800">
                            {{ $h->skor_pengetahuan === null ? '—' : number_format((float) $h->skor_pengetahuan, 2, ',', '.').'%' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500">Kategori</p>
                        <p class="mt-1">
                            @if ($h->kategori_pengetahuan)
                                <span class="rounded px-2 py-0.5 text-sm font-medium {{ $warna[$h->kategori_pengetahuan] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $h->kategori_pengetahuan }}
                                </span>
                            @else
                                —
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500">Skor sikap (total)</p>
                        <p class="text-xl font-semibold text-gray-800">
                            {{ $h->skor_sikap === null ? '—' : number_format((float) $h->skor_sikap, 2, ',', '.') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500">Dikirim</p>
                        <p class="mt-1 text-gray-800">{{ $h->submitted_at?->format('d/m/Y H:i') ?? '—' }}</p>
                    </div>
                </div>

                <div class="overflow-x-auto border-t border-gray-200">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-4 py-2 font-medium">Tipe</th>
                                <th class="px-4 py-2 font-medium">Pertanyaan</th>
                                <th class="px-4 py-2 font-medium">Jawaban</th>
                                <th class="px-4 py-2 font-medium">Kunci</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @foreach ($h->detail as $d)
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-2">
                                        {{ \App\Models\KuesionerSoal::TIPE[$d->soal->tipe] }}
                                        @if ($d->soal->reverse_scored)
                                            <span class="text-xs text-gray-500">(reverse)</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2">{{ $d->soal->pertanyaan }}</td>
                                    <td class="whitespace-nowrap px-4 py-2">
                                        {{ $jawabanLabel($d->soal, $d->jawaban_responden) }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-2">
                                        @if ($d->soal->tipe !== 'pengetahuan')
                                            <span class="text-gray-400">—</span>
                                        @elseif ($d->jawaban_responden === $d->soal->jawaban_benar)
                                            <span class="text-emerald-700">Benar</span>
                                        @else
                                            <span class="text-rose-700">Salah</span>
                                            <span class="text-xs text-gray-500">(kunci: {{ $jawabanLabel($d->soal, $d->soal->jawaban_benar) }})</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    @endforeach
</x-admin-layout>
