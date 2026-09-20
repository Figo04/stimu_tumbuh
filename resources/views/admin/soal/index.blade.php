<x-admin-layout judul="Kelola Soal">
    @if (session('status'))
        <p class="mb-4 rounded-md bg-emerald-50 p-4 text-sm text-emerald-800">{{ session('status') }}</p>
    @endif
    @if (session('galat'))
        <p class="mb-4 rounded-md bg-rose-50 p-4 text-sm text-rose-800">{{ session('galat') }}</p>
    @endif

    <div class="mb-4 flex items-center justify-between gap-4">
        <p class="text-sm text-gray-600">
            Soal yang sudah dijawab responden tidak bisa dihapus — jawaban mereka akan ikut hilang.
        </p>
        <a href="{{ route('admin.soal.create') }}"
           class="shrink-0 rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
            Tambah soal
        </a>
    </div>

    <div class="space-y-6">
        @foreach (\App\Models\KuesionerSoal::TIPE as $tipe => $labelTipe)
            @php($daftar = $soal->get($tipe, collect()))
            <section class="rounded-lg border border-gray-200 bg-white">
                <h2 class="border-b border-gray-200 px-4 py-3 font-semibold text-gray-800">
                    Soal {{ $labelTipe }}
                    <span class="font-normal text-gray-500">({{ $daftar->count() }} soal)</span>
                </h2>

                @if ($daftar->isEmpty())
                    <p class="px-4 py-6 text-sm text-gray-500">Belum ada soal {{ strtolower($labelTipe) }}.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                                <tr>
                                    <th class="px-4 py-2 font-medium">No.</th>
                                    <th class="px-4 py-2 font-medium">Pertanyaan</th>
                                    <th class="px-4 py-2 font-medium">{{ $tipe === 'pengetahuan' ? 'Kunci' : 'Skor dibalik' }}</th>
                                    <th class="px-4 py-2 font-medium">Dijawab</th>
                                    <th class="px-4 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-700">
                                @foreach ($daftar as $s)
                                    <tr>
                                        <td class="whitespace-nowrap px-4 py-2 align-top font-medium">{{ $s->urutan }}</td>
                                        <td class="px-4 py-2 align-top">{{ $s->pertanyaan }}</td>
                                        <td class="whitespace-nowrap px-4 py-2 align-top">
                                            @if ($tipe === 'pengetahuan')
                                                {{ \App\Models\KuesionerSoal::JAWABAN_PENGETAHUAN[$s->jawaban_benar] ?? '—' }}
                                            @else
                                                {{ $s->reverse_scored ? 'Ya' : 'Tidak' }}
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-2 align-top text-gray-500">
                                            {{ $s->detail_count ? $s->detail_count.' responden' : '—' }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-2 align-top text-right">
                                            <a href="{{ route('admin.soal.edit', $s) }}"
                                               class="font-medium text-emerald-700 hover:underline">Ubah</a>

                                            @if ($s->detail_count)
                                                <span class="ms-3 text-gray-400" title="Sudah dijawab responden">Hapus</span>
                                            @else
                                                <form method="POST" action="{{ route('admin.soal.destroy', $s) }}" class="ms-3 inline"
                                                      onsubmit="return confirm('Hapus soal ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="font-medium text-rose-700 hover:underline">Hapus</button>
                                                </form>
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
    </div>
</x-admin-layout>
