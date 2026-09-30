@php
    $label = ['pre' => 'Pre-test', 'post' => 'Post-test'];
    // Dipisah per tipe: kode "S" berarti Salah di pengetahuan, Setuju di sikap.
    $jawabanLabel = fn ($soal, $kode) => ($soal->tipe === 'pengetahuan'
        ? \App\Models\KuesionerSoal::JAWABAN_PENGETAHUAN
        : \App\Models\KuesionerSoal::JAWABAN_SIKAP)[$kode] ?? $kode;
    $warna = ['Baik' => 'bg-sukses-badge text-sukses', 'Cukup' => 'bg-hangat-bg text-ink', 'Kurang' => 'bg-aspek-sosial text-bahaya'];
@endphp

<x-admin-layout judul="Hasil Test {{ $responden->kode_responden }}">
    <a href="{{ route('admin.hasil-test.index') }}" class="font-bold text-brand hover:underline">
        &larr; Kembali ke daftar hasil test
    </a>

    <p class="mt-2 text-sm text-ink-muted">
        {{ $responden->kode_responden }} — {{ $responden->nama }}
        (<a href="{{ route('admin.responden.show', $responden) }}" class="font-bold text-brand hover:underline">identitas lengkap</a>)
    </p>

    @foreach (['pre', 'post'] as $tipe)
        @php $h = $hasil->get($tipe); @endphp

        <section class="mt-4 overflow-hidden rounded-3xl bg-white shadow-sm">
            <h2 class="px-6 py-5 text-lg font-extrabold">{{ $label[$tipe] }}</h2>

            @if (! $h)
                <p class="px-6 pb-6 text-ink-muted">Belum dikerjakan responden ini.</p>
            @else
                <div class="grid gap-4 px-6 pb-5 sm:grid-cols-4">
                    <div>
                        <p class="text-ink-muted">Skor pengetahuan</p>
                        <p class="text-2xl font-extrabold">
                            {{ $h->skor_pengetahuan === null ? '—' : number_format((float) $h->skor_pengetahuan, 2, ',', '.').'%' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-ink-muted">Kategori</p>
                        <p class="mt-1">
                            @if ($h->kategori_pengetahuan)
                                <span class="rounded-full px-3 py-1 text-sm font-bold {{ $warna[$h->kategori_pengetahuan] ?? 'bg-krem-tua text-ink' }}">
                                    {{ $h->kategori_pengetahuan }}
                                </span>
                            @else
                                —
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-ink-muted">Skor sikap (total)</p>
                        <p class="text-2xl font-extrabold">
                            {{ $h->skor_sikap === null ? '—' : number_format((float) $h->skor_sikap, 2, ',', '.') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-ink-muted">Dikirim</p>
                        <p class="mt-1 text-ink">{{ $h->submitted_at?->format('d/m/Y H:i') ?? '—' }}</p>
                    </div>
                </div>

                <div class="overflow-x-auto border-t border-krem-garis">
                    <table class="min-w-full">
                        <thead class="bg-krem-tua text-left text-sm font-bold text-ink-muted">
                            <tr>
                                <th class="px-6 py-3">Tipe</th>
                                <th class="px-6 py-3">Pertanyaan</th>
                                <th class="px-6 py-3">Jawaban</th>
                                <th class="px-6 py-3">Kunci</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-krem-garis">
                            @foreach ($h->detail as $d)
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        {{ \App\Models\KuesionerSoal::TIPE[$d->soal->tipe] }}
                                        @if ($d->soal->reverse_scored)
                                            <span class="text-xs text-ink-muted">(reverse)</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">{{ $d->soal->pertanyaan }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        {{ $jawabanLabel($d->soal, $d->jawaban_responden) }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        @if ($d->soal->tipe !== 'pengetahuan')
                                            <span class="text-ink-muted/60">—</span>
                                        @elseif ($d->jawaban_responden === $d->soal->jawaban_benar)
                                            <span class="text-sukses">Benar</span>
                                        @else
                                            <span class="text-bahaya">Salah</span>
                                            <span class="text-xs text-ink-muted">(kunci: {{ $jawabanLabel($d->soal, $d->soal->jawaban_benar) }})</span>
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
