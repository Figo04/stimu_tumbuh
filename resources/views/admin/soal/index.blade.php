<x-admin-layout judul="Kelola Soal">
    <x-slot name="aksi">
        <a href="{{ route('admin.soal.create') }}"
           class="flex items-center gap-2 rounded-2xl bg-brand px-5 py-3 font-bold text-white shadow-lg shadow-brand/20 hover:bg-brand/90">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14" /><path d="M12 5v14" /></svg>
            Tambah Soal
        </a>
    </x-slot>

    @if (session('status'))
        <x-auth-session-status class="mb-4" :status="session('status')" />
    @endif
    @if (session('galat'))
        <p class="mb-4 rounded-2xl bg-aspek-sosial px-4 py-3 text-sm font-semibold text-bahaya">{{ session('galat') }}</p>
    @endif

    <p class="mb-4 text-ink-muted">
        Soal yang sudah dijawab responden tidak bisa dihapus — jawaban mereka akan ikut hilang.
    </p>

    <div class="space-y-6">
        @foreach (\App\Models\KuesionerSoal::TIPE as $tipe => $labelTipe)
            @php($daftar = $soal->get($tipe, collect()))
            <section class="overflow-hidden rounded-3xl bg-white shadow-sm">
                <h2 class="px-6 py-5 text-lg font-extrabold">
                    Soal {{ $labelTipe }}
                    <span class="font-bold text-ink-muted">({{ $daftar->count() }} soal)</span>
                </h2>

                @if ($daftar->isEmpty())
                    <p class="px-6 pb-6 text-ink-muted">Belum ada soal {{ strtolower($labelTipe) }}.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-krem-tua text-left text-sm font-bold text-ink-muted">
                                <tr>
                                    <th class="px-6 py-3">Nomor</th>
                                    <th class="px-6 py-3">Pertanyaan</th>
                                    <th class="px-6 py-3">{{ $tipe === 'pengetahuan' ? 'Kunci Jawaban' : 'Skor Dibalik' }}</th>
                                    <th class="px-6 py-3">Dijawab</th>
                                    <th class="px-6 py-3"><span class="sr-only">Aksi</span></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-krem-garis">
                                @foreach ($daftar as $s)
                                    <tr>
                                        <td class="whitespace-nowrap px-6 py-4 align-top font-bold">{{ $s->urutan }}</td>
                                        <td class="px-6 py-4 align-top">{{ $s->pertanyaan }}</td>
                                        <td class="whitespace-nowrap px-6 py-4 align-top">
                                            @if ($tipe === 'pengetahuan')
                                                {{ \App\Models\KuesionerSoal::JAWABAN_PENGETAHUAN[$s->jawaban_benar] ?? '—' }}
                                            @else
                                                {{ $s->reverse_scored ? 'Ya' : 'Tidak' }}
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 align-top text-ink-muted">
                                            {{ $s->detail_count ? $s->detail_count.' responden' : '—' }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-3 align-top">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('admin.soal.edit', $s) }}"
                                                   class="flex items-center gap-1.5 rounded-xl border border-krem-garis px-3 py-1.5 text-sm font-bold hover:bg-krem">
                                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21.17 6.81a1 1 0 0 0-3.99-3.99L3.84 16.17a2 2 0 0 0-.5.83l-1.32 4.35a.5.5 0 0 0 .62.62l4.35-1.32a2 2 0 0 0 .83-.5z" /></svg>
                                                    Ubah
                                                </a>

                                                @if ($s->detail_count)
                                                    <span class="flex items-center rounded-xl px-3 py-1.5 text-sm font-bold text-ink-muted/60" title="Sudah dijawab responden">Hapus</span>
                                                @else
                                                    <form method="POST" action="{{ route('admin.soal.destroy', $s) }}"
                                                          onsubmit="return confirm('Hapus soal ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="flex items-center gap-1.5 rounded-xl bg-bahaya px-3 py-1.5 text-sm font-bold text-white hover:bg-bahaya/90">
                                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18" /><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" /><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" /></svg>
                                                            Hapus
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
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
