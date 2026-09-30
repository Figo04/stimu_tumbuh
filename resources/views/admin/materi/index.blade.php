<x-admin-layout judul="Kelola Materi">
    <p class="mb-4 text-sm text-ink-muted">
        Isi materi dihardcode developer dan tidak bisa diubah di sini. Yang bisa Anda ganti sendiri:
        <span class="font-medium text-ink">tautan video YouTube</span> tiap materi — buka materinya, lalu tempel tautannya.
    </p>

    <div class="space-y-6">
        @foreach ($kelompokUsia as $kelompok)
            @php($daftar = $materi->get($kelompok, collect()))
            <section class="overflow-hidden rounded-3xl bg-white shadow-sm">
                <h2 class="px-6 py-5 text-lg font-extrabold">
                    Usia {{ str_replace('-', '–', $kelompok) }} bulan
                    <span class="font-bold text-ink-muted">({{ $daftar->count() }} materi)</span>
                </h2>

                @if ($daftar->isEmpty())
                    <p class="px-6 pb-6 text-ink-muted">Belum ada materi untuk kelompok usia ini.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-krem-tua text-left text-sm font-bold text-ink-muted">
                                <tr>
                                    <th class="px-6 py-3">Aspek</th>
                                    <th class="px-6 py-3">Judul</th>
                                    <th class="px-6 py-3">Video</th>
                                    <th class="px-6 py-3">Dibuka</th>
                                    <th class="px-6 py-3">Selesai</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-krem-garis">
                                @foreach ($daftar as $m)
                                    <tr>
                                        <td class="whitespace-nowrap px-6 py-4 align-top">{{ \App\Models\Materi::ASPEK[$m->aspek] }}</td>
                                        <td class="px-6 py-4 align-top">
                                            <a href="{{ route('admin.materi.show', $m) }}" class="font-bold text-ink hover:text-brand">
                                                {{ $m->judul }}
                                            </a>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 align-top">
                                            @if ($m->video_youtube_id)
                                                <span class="text-ink">✓ {{ $m->video_youtube_id }}</span>
                                            @else
                                                <span class="text-ink-muted/60">belum diisi</span>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 align-top text-ink-muted">
                                            {{ $m->dibuka_count ? $m->dibuka_count.' responden' : '—' }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 align-top text-ink-muted">
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
