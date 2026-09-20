@php
    $anak = $responden->anak;
    $totalDurasi = $riwayat->sum('durasi_menit');
@endphp

<x-admin-layout judul="Aktivitas Stimulasi {{ $responden->kode_responden }}">
    <a href="{{ route('admin.aktivitas.index') }}" class="text-sm font-medium text-emerald-700 hover:underline">
        &larr; Kembali ke rekap aktivitas
    </a>

    <p class="mt-2 text-sm text-gray-600">
        {{ $responden->kode_responden }} — {{ $responden->nama }}
        (<a href="{{ route('admin.responden.show', $responden) }}" class="text-emerald-700 hover:underline">identitas lengkap</a>)
    </p>

    <section class="mt-4 rounded-lg border border-gray-200 bg-white">
        <h2 class="border-b border-gray-200 px-4 py-3 font-semibold text-gray-800">
            Riwayat Entri Kalender
            <span class="font-normal text-gray-500">({{ $riwayat->count() }} entri)</span>
        </h2>

        @if ($riwayat->isEmpty())
            <p class="px-4 py-6 text-sm text-gray-500">Responden ini belum mencatat aktivitas stimulasi.</p>
        @else
            <div class="grid gap-4 px-4 py-3 text-sm sm:grid-cols-3">
                <div>
                    <p class="text-gray-500">Jumlah entri</p>
                    <p class="text-xl font-semibold text-gray-800">{{ $riwayat->count() }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Total durasi</p>
                    <p class="text-xl font-semibold text-gray-800">
                        {{ $totalDurasi ? number_format((int) $totalDurasi, 0, ',', '.').' menit' : '—' }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-500">Entri terakhir</p>
                    <p class="text-xl font-semibold text-gray-800">{{ $riwayat->first()->tanggal->format('d/m/Y') }}</p>
                </div>
            </div>

            <div class="overflow-x-auto border-t border-gray-200">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-4 py-2 font-medium">Tanggal</th>
                            <th class="px-4 py-2 font-medium">Usia Anak</th>
                            <th class="px-4 py-2 font-medium">Aspek</th>
                            <th class="px-4 py-2 font-medium">Jenis Stimulasi</th>
                            <th class="px-4 py-2 font-medium">Durasi</th>
                            <th class="px-4 py-2 font-medium">Pelaku</th>
                            <th class="px-4 py-2 font-medium">Respons Anak</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @foreach ($riwayat as $entri)
                            <tr>
                                <td class="whitespace-nowrap px-4 py-2">{{ $entri->tanggal->format('d/m/Y') }}</td>
                                <td class="whitespace-nowrap px-4 py-2">
                                    {{-- Usia saat entri dicatat (PRD §3.4 Tabel 3), bukan usia hari ini. --}}
                                    {{ $anak
                                        ? \App\Services\UsiaAnakService::usiaBulan($anak->tanggal_lahir, $entri->tanggal).' bln'
                                        : '—' }}
                                </td>
                                <td class="px-4 py-2">{{ \App\Models\Materi::ASPEK[$entri->aspek] ?? $entri->aspek }}</td>
                                <td class="px-4 py-2">
                                    @if ($entri->jenis_stimulasi)
                                        {{ $entri->jenis_stimulasi }}
                                    @else
                                        {{-- Entri dari tab Praktik: jenis & durasi memang tidak diisi (Sesi 19). --}}
                                        <span class="text-gray-500">Praktik: {{ $entri->materi?->judul ?? '—' }}</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-2">
                                    {{ $entri->durasi_menit ? $entri->durasi_menit.' menit' : '—' }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-2">
                                    {{ \App\Models\AktivitasStimulasi::PELAKU[$entri->pelaku] ?? $entri->pelaku }}
                                </td>
                                <td class="px-4 py-2">{{ $entri->respons_anak ?: '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</x-admin-layout>
