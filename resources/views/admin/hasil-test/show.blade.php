@php
    $label = ['pre' => 'Pre-test', 'post' => 'Post-test'];
    $persen = fn ($nilai) => $nilai === null ? '—' : number_format((float) $nilai, 2, ',', '.').'%';
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
                <div class="grid gap-4 px-6 pb-5 sm:grid-cols-3">
                    <div>
                        <p class="text-ink-muted">Skor total (% Ya)</p>
                        <p class="text-2xl font-extrabold">{{ $persen($h->skor_total) }}</p>
                    </div>
                    <div>
                        <p class="text-ink-muted">Kelompok usia soal</p>
                        <p class="mt-1 text-ink">{{ $h->kelompok_usia }} bulan</p>
                    </div>
                    <div>
                        <p class="text-ink-muted">Dikirim</p>
                        <p class="mt-1 text-ink">{{ $h->submitted_at?->format('d/m/Y H:i') ?? '—' }}</p>
                    </div>
                </div>

                <dl class="grid grid-cols-2 gap-2 px-6 pb-5 sm:grid-cols-4">
                    @foreach (\App\Models\ItemPerkembangan::ASPEK as $aspek => $labelAspek)
                        <div class="rounded-2xl bg-krem p-3">
                            <dt class="text-sm text-ink-muted">{{ $labelAspek }}</dt>
                            <dd class="font-extrabold">{{ $persen($h->{"skor_$aspek"}) }}</dd>
                        </div>
                    @endforeach
                </dl>

                <div class="overflow-x-auto border-t border-krem-garis">
                    <table class="min-w-full">
                        <thead class="bg-krem-tua text-left text-sm font-bold text-ink-muted">
                            <tr>
                                <th class="px-6 py-3">Kategori</th>
                                <th class="px-6 py-3">Pertanyaan</th>
                                <th class="px-6 py-3">Jawaban</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-krem-garis">
                            @foreach ($h->detail as $d)
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4">{{ \App\Models\ItemPerkembangan::ASPEK[$d->soal->aspek] }}</td>
                                    <td class="px-6 py-4">{{ $d->soal->pertanyaan }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 font-bold {{ $d->jawaban_responden ? 'text-sukses' : 'text-bahaya' }}">
                                        {{ $d->jawaban_responden ? 'Ya' : 'Tidak' }}
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
