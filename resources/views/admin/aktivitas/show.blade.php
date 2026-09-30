@php
    $anak = $responden->anak;
    $totalDurasi = $riwayat->sum('durasi_menit');
@endphp

<x-admin-layout judul="Aktivitas Stimulasi {{ $responden->kode_responden }}">
    <a href="{{ route('admin.aktivitas.index') }}" class="font-bold text-brand hover:underline">
        &larr; Kembali ke rekap aktivitas
    </a>

    <p class="mt-2 text-sm text-ink-muted">
        {{ $responden->kode_responden }} — {{ $responden->nama }}
        (<a href="{{ route('admin.responden.show', $responden) }}" class="font-bold text-brand hover:underline">identitas lengkap</a>)
    </p>

    <section class="mt-4 overflow-hidden rounded-3xl bg-white shadow-sm">
        <h2 class="px-6 py-5 text-lg font-extrabold">
            Riwayat Entri Kalender
            <span class="font-bold text-ink-muted">({{ $riwayat->count() }} entri)</span>
        </h2>

        @if ($riwayat->isEmpty())
            <p class="px-6 pb-6 text-ink-muted">Responden ini belum mencatat aktivitas stimulasi.</p>
        @else
            <div class="grid gap-4 px-6 pb-5 sm:grid-cols-3">
                <div>
                    <p class="text-ink-muted">Jumlah entri</p>
                    <p class="text-2xl font-extrabold">{{ $riwayat->count() }}</p>
                </div>
                <div>
                    <p class="text-ink-muted">Total durasi</p>
                    <p class="text-2xl font-extrabold">
                        {{ $totalDurasi ? number_format((int) $totalDurasi, 0, ',', '.').' menit' : '—' }}
                    </p>
                </div>
                <div>
                    <p class="text-ink-muted">Entri terakhir</p>
                    <p class="text-2xl font-extrabold">{{ $riwayat->first()->tanggal->format('d/m/Y') }}</p>
                </div>
            </div>

            <div class="overflow-x-auto border-t border-krem-garis">
                <table class="min-w-full">
                    <thead class="bg-krem-tua text-left text-sm font-bold text-ink-muted">
                        <tr>
                            <th class="px-6 py-3">Tanggal</th>
                            <th class="px-6 py-3">Usia Anak</th>
                            <th class="px-6 py-3">Aspek</th>
                            <th class="px-6 py-3">Jenis Stimulasi</th>
                            <th class="px-6 py-3">Durasi</th>
                            <th class="px-6 py-3">Pelaku</th>
                            <th class="px-6 py-3">Respons Anak</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-krem-garis">
                        @foreach ($riwayat as $entri)
                            <tr>
                                <td class="whitespace-nowrap px-6 py-4">{{ $entri->tanggal->format('d/m/Y') }}</td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    {{-- Usia saat entri dicatat (PRD §3.4 Tabel 3), bukan usia hari ini. --}}
                                    {{ $anak
                                        ? \App\Services\UsiaAnakService::usiaBulan($anak->tanggal_lahir, $entri->tanggal).' bln'
                                        : '—' }}
                                </td>
                                <td class="px-6 py-4">{{ \App\Models\Materi::ASPEK[$entri->aspek] ?? $entri->aspek }}</td>
                                <td class="px-6 py-4">
                                    @if ($entri->jenis_stimulasi)
                                        {{ $entri->jenis_stimulasi }}
                                    @else
                                        {{-- Entri dari tab Praktik: jenis & durasi memang tidak diisi (Sesi 19). --}}
                                        <span class="text-ink-muted">Praktik: {{ $entri->materi?->judul ?? '—' }}</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    {{ $entri->durasi_menit ? $entri->durasi_menit.' menit' : '—' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    {{ \App\Models\AktivitasStimulasi::PELAKU[$entri->pelaku] ?? $entri->pelaku }}
                                </td>
                                <td class="px-6 py-4">{{ $entri->respons_anak ?: '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</x-admin-layout>
