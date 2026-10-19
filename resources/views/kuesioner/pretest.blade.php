{{-- Dipakai pre-test dan post-test; bedanya hanya judul, pengantar, dan tujuan form ($tipe). --}}
<x-app-layout>
    @php
        $label = \App\Http\Controllers\KuesionerController::LABEL[$tipe];
        $sapaan = ['ibu' => 'Ibu', 'ayah' => 'Ayah'][Auth::user()->hubungan_dengan_anak] ?? 'Anda';
        $jumlahSoal = $soal->flatten()->count();
    @endphp

    {{-- mulai: kartu pembuka dulu (mockup "Tes"), soal tampil setelah tombol Mulai; langsung ke soal bila validasi gagal. --}}
    <div class="mx-auto max-w-2xl px-4 py-6" x-data="{ mulai: @js($errors->any()) }">
        <h1 class="text-3xl font-extrabold">Tes Tumbuh Kembang</h1>
        <p class="mt-1 text-lg text-ink-muted">Jawab sesuai kemampuan {{ $anak->nama_inisial }} yang {{ $sapaan }} amati sehari-hari.</p>

        <section x-show="! mulai" class="mt-6 rounded-3xl bg-white p-6 shadow-lg shadow-ink/5">
            <span class="flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-soft text-brand">
                <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="8" height="4" x="8" y="2" rx="1" /><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" /><path d="m9 14 2 2 4-4" /></svg>
            </span>
            <h2 class="mt-5 text-2xl font-extrabold">{{ $tipe === 'pre' ? 'Selesaikan pre-test lebih dulu' : 'Saatnya mengisi post-test' }}</h2>
            <p class="mt-3 text-lg leading-relaxed text-ink-muted">
                @if ($tipe === 'pre')
                    Ini bukan ujian. Jawab Ya atau Tidak sesuai yang {{ $sapaan }} amati pada {{ $anak->nama_inisial }}. Setelah selesai, semua materi akan terbuka.
                @else
                    {{ $sapaan }} sudah menyelesaikan seluruh materi. Isi kembali pertanyaan yang sama untuk melihat perkembangan {{ $anak->nama_inisial }} setelah stimulasi.
                @endif
            </p>
            <ul class="mt-4 space-y-2 text-ink-muted">
                <li class="flex gap-2"><span class="font-bold text-brand">•</span> {{ $jumlahSoal }} pertanyaan</li>
                <li class="flex gap-2"><span class="font-bold text-brand">•</span> <span>Jawaban hanya bisa dikirim <strong class="text-ink">satu kali</strong> dan tidak dapat diubah setelah dikirim.</span></li>
            </ul>
            <x-primary-button type="button" class="mt-6 w-full" x-on:click="mulai = true; window.scrollTo({ top: 0 })">
                Mulai {{ $label }}
            </x-primary-button>
        </section>

        <form method="POST" action="{{ route($tipe === 'pre' ? 'pretest.store' : 'posttest.store') }}" x-show="mulai" x-cloak
              class="mt-6 space-y-6" onsubmit="return confirm('Kirim jawaban sekarang? Jawaban tidak dapat diubah setelah dikirim.')">
            @csrf

            @if ($errors->any())
                <p class="rounded-2xl bg-aspek-sosial px-5 py-4 font-bold text-bahaya">
                    Masih ada pertanyaan yang belum dijawab. Periksa kembali tanda merah di bawah.
                </p>
            @endif

            <p class="text-lg text-ink-muted">
                Usia {{ str_replace('-', '–', $kelompokUsia) }} bulan. Apakah {{ $anak->nama_inisial }} <strong class="text-ink">sudah bisa</strong> melakukan hal berikut?
            </p>

            {{-- Pola tampilan sama dengan checklist Perkembangan (perkembangan/index). --}}
            @foreach (\App\Models\ItemPerkembangan::ASPEK as $aspek => $label)
                @continue(! $soal->has($aspek))
                <section class="rounded-3xl bg-white p-5 shadow-sm">
                    <x-chip-aspek :aspek="$aspek" />
                    @foreach ($soal[$aspek] as $s)
                        <fieldset class="border-b border-krem-garis py-5 last:border-0 last:pb-0">
                            <legend class="float-left mb-3 w-full text-lg">{{ $loop->iteration }}. {{ $s->pertanyaan }}</legend>
                            <div class="clear-left grid grid-cols-2 gap-3">
                                @foreach (\App\Models\KuesionerSoal::JAWABAN as $nilai => $teks)
                                    <label class="flex cursor-pointer items-center justify-center gap-2 rounded-2xl border border-krem-garis px-4 py-3.5 font-bold has-[:checked]:border-brand has-[:checked]:bg-brand-soft has-[:checked]:text-brand">
                                        <input type="radio" name="jawaban[{{ $s->id }}]" value="{{ $nilai }}" required
                                               class="h-5 w-5 border-krem-garis text-brand focus:ring-brand"
                                               @checked(old("jawaban.{$s->id}") === (string) $nilai)>
                                        {{ $teks }}
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('jawaban.'.$s->id)" class="mt-2" />
                        </fieldset>
                    @endforeach
                </section>
            @endforeach

            <x-input-error :messages="$errors->get('jawaban')" />

            <x-primary-button class="w-full">Kirim Jawaban</x-primary-button>
        </form>
    </div>
</x-app-layout>
