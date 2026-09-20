@php
    // Kolom skor mengikuti Materi::ASPEK supaya label & urutannya satu sumber.
    $aspek = \App\Models\Materi::ASPEK;
@endphp

<x-admin-layout judul="Perkembangan {{ $responden->kode_responden }}">
    <a href="{{ route('admin.perkembangan.index') }}" class="text-sm font-medium text-emerald-700 hover:underline">
        &larr; Kembali ke rekap perkembangan
    </a>

    <p class="mt-2 text-sm text-gray-600">
        {{ $responden->kode_responden }} — {{ $responden->nama }}
        (<a href="{{ route('admin.responden.show', $responden) }}" class="text-emerald-700 hover:underline">identitas lengkap</a>)
    </p>

    <section class="mt-4 rounded-lg border border-gray-200 bg-white">
        <h2 class="border-b border-gray-200 px-4 py-3 font-semibold text-gray-800">
            Riwayat Penilaian
            <span class="font-normal text-gray-500">({{ $riwayat->count() }} penilaian)</span>
        </h2>

        @if ($riwayat->isEmpty())
            <p class="px-4 py-6 text-sm text-gray-500">Responden ini belum pernah mengisi penilaian perkembangan.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-4 py-2 font-medium">Tanggal</th>
                            <th class="px-4 py-2 font-medium">Usia</th>
                            <th class="px-4 py-2 font-medium">Kelompok</th>
                            @foreach ($aspek as $label)
                                <th class="border-l border-gray-200 px-4 py-2 font-medium">{{ $label }}</th>
                            @endforeach
                            <th class="border-l border-gray-200 px-4 py-2 font-medium">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @foreach ($riwayat as $p)
                            <tr>
                                <td class="whitespace-nowrap px-4 py-2">{{ $p->tanggal_penilaian->format('d/m/Y') }}</td>
                                {{-- Snapshot saat penilaian, bukan usia hari ini (PRD §5). --}}
                                <td class="whitespace-nowrap px-4 py-2">{{ $p->usia_bulan }} bln</td>
                                <td class="whitespace-nowrap px-4 py-2">{{ $p->kelompok_usia }} bln</td>
                                @foreach ($aspek as $key => $label)
                                    <td class="whitespace-nowrap border-l border-gray-200 px-4 py-2">
                                        {{ number_format((float) $p->{"skor_$key"}, 2, ',', '.') }}%
                                    </td>
                                @endforeach
                                <td class="whitespace-nowrap border-l border-gray-200 px-4 py-2 font-semibold">
                                    {{ number_format((float) $p->skor_total, 2, ',', '.') }}%
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <p class="border-t border-gray-200 px-4 py-3 text-xs text-gray-500">
                Skor = persentase item checklist yang dijawab "Ya". Item checklist berbeda antar kelompok usia,
                jadi skor lintas kelompok usia tidak setara untuk dibandingkan langsung.
            </p>
        @endif
    </section>
</x-admin-layout>
