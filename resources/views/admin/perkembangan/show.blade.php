@php
    // Kolom skor mengikuti Materi::ASPEK supaya label & urutannya satu sumber.
    $aspek = \App\Models\Materi::ASPEK;
@endphp

<x-admin-layout judul="Perkembangan {{ $responden->kode_responden }}">
    <a href="{{ route('admin.perkembangan.index') }}" class="font-bold text-brand hover:underline">
        &larr; Kembali ke rekap perkembangan
    </a>

    <p class="mt-2 text-sm text-ink-muted">
        {{ $responden->kode_responden }} — {{ $responden->nama }}
        (<a href="{{ route('admin.responden.show', $responden) }}" class="font-bold text-brand hover:underline">identitas lengkap</a>)
    </p>

    <section class="mt-4 overflow-hidden rounded-3xl bg-white shadow-sm">
        <h2 class="px-6 py-5 text-lg font-extrabold">
            Riwayat Penilaian
            <span class="font-bold text-ink-muted">({{ $riwayat->count() }} penilaian)</span>
        </h2>

        @if ($riwayat->isEmpty())
            <p class="px-6 pb-6 text-ink-muted">Responden ini belum pernah mengisi penilaian perkembangan.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-krem-tua text-left text-sm font-bold text-ink-muted">
                        <tr>
                            <th class="px-6 py-3">Tanggal</th>
                            <th class="px-6 py-3">Usia</th>
                            <th class="px-6 py-3">Kelompok</th>
                            @foreach ($aspek as $label)
                                <th class="border-l border-krem-garis px-6 py-3">{{ $label }}</th>
                            @endforeach
                            <th class="border-l border-krem-garis px-6 py-3">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-krem-garis">
                        @foreach ($riwayat as $p)
                            <tr>
                                <td class="whitespace-nowrap px-6 py-4">{{ $p->tanggal_penilaian->format('d/m/Y') }}</td>
                                {{-- Snapshot saat penilaian, bukan usia hari ini (PRD §5). --}}
                                <td class="whitespace-nowrap px-6 py-4">{{ $p->usia_bulan }} bln</td>
                                <td class="whitespace-nowrap px-6 py-4">{{ $p->kelompok_usia }} bln</td>
                                @foreach ($aspek as $key => $label)
                                    <td class="whitespace-nowrap border-l border-krem-garis px-6 py-4">
                                        {{ number_format((float) $p->{"skor_$key"}, 2, ',', '.') }}%
                                    </td>
                                @endforeach
                                <td class="whitespace-nowrap border-l border-krem-garis px-6 py-4 font-semibold">
                                    {{ number_format((float) $p->skor_total, 2, ',', '.') }}%
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <p class="border-t border-krem-garis px-6 py-4 text-xs text-ink-muted">
                Skor = persentase item checklist yang dijawab "Ya". Item checklist berbeda antar kelompok usia,
                jadi skor lintas kelompok usia tidak setara untuk dibandingkan langsung.
            </p>
        @endif
    </section>
</x-admin-layout>
