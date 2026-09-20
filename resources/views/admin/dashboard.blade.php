<x-admin-layout judul="Dashboard">
    {{-- Chart (Sesi 29), kartu rata-rata frekuensi & durasi (Sesi 30), tombol Export (Sesi 35) menyusul. --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-3">
        @foreach ($kartu as $label => $nilai)
            <div class="rounded-lg border border-gray-200 bg-white p-4">
                <p class="text-sm text-gray-500">{{ $label }}</p>
                <p class="mt-1 text-3xl font-semibold text-gray-800">{{ $nilai }}</p>
            </div>
        @endforeach
    </div>

    <section class="mt-6 rounded-lg border border-gray-200 bg-white">
        <h2 class="border-b border-gray-200 px-4 py-3 font-semibold text-gray-800">Aktivitas Terbaru</h2>

        @if ($terbaru->isEmpty())
            <p class="px-4 py-6 text-sm text-gray-500">Belum ada entri stimulasi dari responden.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-4 py-2 font-medium">Tanggal</th>
                            <th class="px-4 py-2 font-medium">Responden</th>
                            <th class="px-4 py-2 font-medium">Aspek</th>
                            <th class="px-4 py-2 font-medium">Jenis</th>
                            <th class="px-4 py-2 font-medium">Durasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @foreach ($terbaru as $entri)
                            <tr>
                                <td class="whitespace-nowrap px-4 py-2">{{ $entri->tanggal->format('d/m/Y') }}</td>
                                <td class="px-4 py-2">
                                    {{ $entri->user->kode_responden }}
                                    <span class="text-gray-500">— {{ $entri->user->nama }}</span>
                                </td>
                                <td class="px-4 py-2">{{ \App\Models\Materi::ASPEK[$entri->aspek] }}</td>
                                <td class="px-4 py-2">
                                    {{ $entri->jenis_stimulasi ?? ($entri->materi ? 'Praktik: '.$entri->materi->judul : '—') }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-2">{{ $entri->durasi_menit ? $entri->durasi_menit.' menit' : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</x-admin-layout>
