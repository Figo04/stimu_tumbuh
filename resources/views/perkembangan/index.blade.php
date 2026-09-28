<x-app-layout>
    @php
        $namaAnak = $anak->nama_inisial;
        $sebelumTerakhir = $riwayat->get(1);
        // Beda kelompok usia = beda item checklist, jadi skor tidak dibandingkan.
        $selisihTotal = $terakhir && $sebelumTerakhir && $sebelumTerakhir->kelompok_usia === $terakhir->kelompok_usia
            ? round($terakhir->skor_total - $sebelumTerakhir->skor_total, 2)
            : null;
    @endphp

    {{-- nilai: form penilaian dibuka lewat tombol; terbuka otomatis bila validasi gagal. --}}
    <div class="mx-auto max-w-2xl px-4 py-6" x-data="{ nilai: @js($errors->any()) }">
        <h1 class="text-3xl font-extrabold">Perkembangan Anak</h1>
        <p class="mt-1 text-lg text-ink-muted">Lihat perubahan kemampuan {{ $namaAnak }} dari waktu ke waktu.</p>

        @if (session('status'))
            <x-auth-session-status class="mt-4" :status="'✓ '.session('status')" />
        @endif

        @if ($terakhir)
            <section class="mt-6 rounded-3xl bg-brand p-6 text-white shadow-lg shadow-brand/20">
                <p class="font-bold text-white/90">Penilaian terbaru · {{ $terakhir->tanggal_penilaian->translatedFormat('j F Y') }}</p>
                <p class="mt-2 flex items-baseline gap-3">
                    <span class="text-5xl font-extrabold">{{ $terakhir->skor_total + 0 }}%</span>
                    <span class="text-lg">secara keseluruhan</span>
                </p>
                <dl class="mt-5 space-y-4">
                    @foreach (\App\Models\Materi::ASPEK as $aspek => $label)
                        @php $skor = $terakhir->{"skor_$aspek"} + 0; @endphp
                        <div>
                            <div class="flex justify-between gap-4 font-bold">
                                <dt>{{ $label }}</dt>
                                <dd>{{ $skor }}%</dd>
                            </div>
                            <div class="mt-2 h-2.5 overflow-hidden rounded-full bg-brand-muda" aria-hidden="true">
                                <div class="h-full rounded-full bg-white" style="width: {{ $skor }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </dl>
            </section>

            @if ($selisihTotal !== null)
                <p class="mt-4 flex items-center gap-2 rounded-2xl px-5 py-4 font-bold {{ $selisihTotal < 0 ? 'bg-hangat-bg text-ink' : 'bg-sukses-bg text-sukses' }}">
                    <svg class="h-5 w-5 shrink-0 {{ $selisihTotal < 0 ? 'rotate-90' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 7h6v6" /><path d="m22 7-8.5 8.5-5-5L2 17" /></svg>
                    @if ($selisihTotal > 0)
                        Naik {{ $selisihTotal + 0 }}% dibanding penilaian sebelumnya
                    @elseif ($selisihTotal < 0)
                        Turun {{ abs($selisihTotal) + 0 }}% dibanding penilaian sebelumnya
                    @else
                        Sama seperti penilaian sebelumnya
                    @endif
                </p>
            @endif
        @else
            <section class="mt-6 rounded-3xl bg-white p-6 text-center shadow-sm">
                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-soft text-brand">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" /><path d="M3.22 12H9.5l.5-1 2 4.5 2-7 1.5 3.5h5.27" /></svg>
                </span>
                <p class="mt-4 text-xl font-extrabold">Belum ada penilaian</p>
                <p class="mt-1 text-ink-muted">Jawab Ya/Tidak untuk kemampuan {{ $namaAnak }} saat ini. Ulangi kapan saja untuk melihat perubahannya.</p>
            </section>
        @endif

        @if ($riwayat->isNotEmpty())
            <h2 class="mt-8 text-xl font-extrabold">Riwayat penilaian</h2>
            <ol class="mt-3 space-y-3">
                @foreach ($riwayat as $p)
                    @php
                        $sebelum = $riwayat->get($loop->index + 1);
                        $banding = $sebelum && $sebelum->kelompok_usia === $p->kelompok_usia;
                    @endphp
                    <li>
                        <details class="group rounded-3xl bg-white shadow-sm">
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-4 p-5">
                                <span>
                                    <span class="block text-lg font-extrabold">{{ $p->tanggal_penilaian->translatedFormat('j F Y') }}</span>
                                    <span class="block text-ink-muted">Nilai keseluruhan {{ $p->skor_total + 0 }}%</span>
                                </span>
                                <svg class="h-5 w-5 shrink-0 transition group-open:rotate-90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6" /></svg>
                            </summary>
                            <div class="border-t border-krem-garis px-5 pb-5 pt-4">
                                <p class="text-sm text-ink-muted">
                                    Usia {{ $p->usia_bulan }} bulan · kelompok {{ $p->kelompok_usia }} bulan
                                    @if (! $sebelum)
                                        · penilaian pertama
                                    @elseif (! $banding)
                                        · <span class="font-bold text-ink">kelompok usia baru, tidak dibandingkan</span>
                                    @endif
                                </p>
                                <dl class="mt-3 grid grid-cols-2 gap-2">
                                    @foreach (\App\Models\Materi::ASPEK + ['total' => 'Total'] as $aspek => $label)
                                        <div class="rounded-2xl bg-krem p-3 {{ $aspek === 'total' ? 'col-span-2' : '' }}">
                                            <dt class="text-sm text-ink-muted">{{ $label }}</dt>
                                            <dd class="font-extrabold">
                                                {{ $p->{"skor_$aspek"} + 0 }}%
                                                @if ($banding)
                                                    @php $selisih = round($p->{"skor_$aspek"} - $sebelum->{"skor_$aspek"}, 2); @endphp
                                                    <span class="ml-1 text-sm font-bold {{ $selisih > 0 ? 'text-sukses' : ($selisih < 0 ? 'text-bahaya' : 'text-ink-muted') }}">
                                                        {{ $selisih > 0 ? '▲ +'.$selisih : ($selisih < 0 ? '▼ −'.abs($selisih) : 'tetap') }}
                                                    </span>
                                                @endif
                                            </dd>
                                        </div>
                                    @endforeach
                                </dl>
                            </div>
                        </details>
                    </li>
                @endforeach
            </ol>
        @endif

        @if ($item->isEmpty())
            <p class="mt-8 rounded-2xl bg-hangat-bg px-5 py-4 font-bold">Checklist untuk kelompok usia ini belum tersedia.</p>
        @else
            <x-primary-button type="button" x-show="! nilai" class="mt-8 w-full"
                              x-on:click="nilai = true; $nextTick(() => $refs.form.scrollIntoView({ behavior: 'smooth' }))">
                {{ $terakhir ? 'Lakukan Penilaian Baru' : 'Mulai Penilaian Pertama' }}
            </x-primary-button>

            <form method="POST" action="{{ route('perkembangan.store') }}" x-ref="form" x-show="nilai" x-cloak class="mt-8 scroll-mt-24 space-y-5">
                @csrf
                <h2 class="text-xl font-extrabold">Penilaian baru · usia {{ str_replace('-', '–', $anak->kelompok_usia) }} bulan</h2>
                <p class="text-lg text-ink-muted">Apakah {{ $namaAnak }} <strong class="text-ink">sudah bisa</strong> melakukan hal berikut? Jawab Ya atau Tidak.</p>

                @if ($errors->any())
                    <p class="rounded-2xl bg-aspek-sosial px-5 py-4 font-bold text-bahaya">
                        Masih ada kemampuan yang belum dijawab. Periksa kembali tanda merah di bawah.
                    </p>
                @endif

                @foreach (\App\Models\Materi::ASPEK as $aspek => $label)
                    @continue(! $item->has($aspek))
                    <section class="rounded-3xl bg-white p-5 shadow-sm">
                        <x-chip-aspek :aspek="$aspek" />
                        @foreach ($item[$aspek] as $i)
                            <fieldset class="border-b border-krem-garis py-5 last:border-0 last:pb-0">
                                <legend class="float-left mb-3 w-full text-lg">{{ $loop->iteration }}. {{ $i->pertanyaan }}</legend>
                                <div class="clear-left grid grid-cols-2 gap-3">
                                    @foreach (['1' => 'Ya', '0' => 'Tidak'] as $nilai => $teks)
                                        <label class="flex cursor-pointer items-center justify-center gap-2 rounded-2xl border border-krem-garis px-4 py-3.5 font-bold has-[:checked]:border-brand has-[:checked]:bg-brand-soft has-[:checked]:text-brand">
                                            <input type="radio" name="jawaban[{{ $i->id }}]" value="{{ $nilai }}" required
                                                   class="h-5 w-5 border-krem-garis text-brand focus:ring-brand"
                                                   @checked(old("jawaban.{$i->id}") === (string) $nilai)>
                                            {{ $teks }}
                                        </label>
                                    @endforeach
                                </div>
                                <x-input-error :messages="$errors->get('jawaban.'.$i->id)" class="mt-2" />
                            </fieldset>
                        @endforeach
                    </section>
                @endforeach

                <x-input-error :messages="$errors->get('jawaban')" />

                <x-primary-button class="w-full">Simpan Penilaian</x-primary-button>
            </form>
        @endif
    </div>
</x-app-layout>
