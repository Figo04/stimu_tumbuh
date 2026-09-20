<x-admin-layout judul="Kelola Materi">
    <p class="mb-4 text-sm text-gray-600">
        Isi materi dihardcode developer dan tidak bisa diubah di sini. Yang bisa Anda ganti sendiri:
        <span class="font-medium text-gray-800">tautan video YouTube</span> tiap materi — buka materinya, lalu tempel tautannya.
    </p>

    <div class="space-y-6">
        @foreach ($kelompokUsia as $kelompok)
            @php($daftar = $materi->get($kelompok, collect()))
            <section class="rounded-lg border border-gray-200 bg-white">
                <h2 class="border-b border-gray-200 px-4 py-3 font-semibold text-gray-800">
                    Usia {{ str_replace('-', '–', $kelompok) }} bulan
                    <span class="font-normal text-gray-500">({{ $daftar->count() }} materi)</span>
                </h2>

                @if ($daftar->isEmpty())
                    <p class="px-4 py-6 text-sm text-gray-500">Belum ada materi untuk kelompok usia ini.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                                <tr>
                                    <th class="px-4 py-2 font-medium">Aspek</th>
                                    <th class="px-4 py-2 font-medium">Judul</th>
                                    <th class="px-4 py-2 font-medium">Video</th>
                                    <th class="px-4 py-2 font-medium">Dibuka</th>
                                    <th class="px-4 py-2 font-medium">Selesai</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-700">
                                @foreach ($daftar as $m)
                                    <tr>
                                        <td class="whitespace-nowrap px-4 py-2 align-top">{{ \App\Models\Materi::ASPEK[$m->aspek] }}</td>
                                        <td class="px-4 py-2 align-top">
                                            <a href="{{ route('admin.materi.show', $m) }}" class="font-medium text-emerald-700 hover:underline">
                                                {{ $m->judul }}
                                            </a>
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-2 align-top">
                                            @if ($m->video_youtube_id)
                                                <span class="text-gray-700">✓ {{ $m->video_youtube_id }}</span>
                                            @else
                                                <span class="text-gray-400">belum diisi</span>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-2 align-top text-gray-500">
                                            {{ $m->dibuka_count ? $m->dibuka_count.' responden' : '—' }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-2 align-top text-gray-500">
                                            {{ $m->selesai_count ? $m->selesai_count.' responden' : '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        @endforeach
    </div>
</x-admin-layout>
