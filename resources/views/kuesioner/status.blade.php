{{-- Tab "Tes" saat tidak ada tes yang bisa diisi. Sengaja tanpa skor (skor hanya untuk peneliti). --}}
<x-app-layout>
    @php $selesaiSemua = $hasil->has('post'); @endphp

    <div class="mx-auto max-w-2xl px-4 py-6">
        <h1 class="text-3xl font-extrabold">Tes Pemahaman</h1>

        @if (session('status'))
            <x-auth-session-status class="mt-4" :status="session('status')" />
        @endif

        <section class="mt-6 rounded-3xl bg-white p-6 shadow-lg shadow-ink/5">
            <span class="flex h-16 w-16 items-center justify-center rounded-2xl {{ $selesaiSemua ? 'bg-sukses-bg text-sukses' : 'bg-krem-tua text-ink-muted' }}">
                @if ($selesaiSemua)
                    <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="8" height="4" x="8" y="2" rx="1" /><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" /><path d="m9 14 2 2 4-4" /></svg>
                @else
                    <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="18" height="11" x="3" y="11" rx="2" /><path d="M7 11V7a5 5 0 0 1 10 0v4" /></svg>
                @endif
            </span>

            @if ($selesaiSemua)
                <h2 class="mt-5 text-2xl font-extrabold">Terima kasih, semua tes sudah dikirim</h2>
                <p class="mt-2 text-lg text-ink-muted">Jawaban Anda sangat membantu penelitian ini. Tetap lanjutkan stimulasi dan catat di Kalender, ya.</p>
            @else
                <h2 class="mt-5 text-2xl font-extrabold">Post-test belum terbuka</h2>
                <p class="mt-2 text-lg text-ink-muted">
                    Post-test terbuka setelah semua materi usia {{ str_replace('-', '–', $kelompokUsia) }} bulan ditandai selesai.
                </p>
                @if ($totalMateri)
                    <div class="mt-5 flex justify-between gap-4 font-bold">
                        <span>Materi selesai</span>
                        <span>{{ $materiSelesai }} dari {{ $totalMateri }}</span>
                    </div>
                    <div class="mt-2 h-3 overflow-hidden rounded-full bg-krem-tua" role="progressbar" aria-valuemin="0" aria-valuemax="{{ $totalMateri }}" aria-valuenow="{{ $materiSelesai }}">
                        <div class="h-full rounded-full bg-brand" style="width: {{ round($materiSelesai / $totalMateri * 100) }}%"></div>
                    </div>
                @endif
            @endif

            <ul class="mt-6 space-y-2">
                @foreach (\App\Http\Controllers\KuesionerController::LABEL as $tipe => $label)
                    <li class="flex items-center justify-between gap-4 rounded-2xl bg-krem px-4 py-3">
                        <span class="font-bold">{{ $label }}</span>
                        @if ($hasil->has($tipe))
                            <span class="rounded-full bg-sukses-badge px-3 py-1 text-sm font-bold text-sukses">
                                ✓ Dikirim {{ $hasil[$tipe]->translatedFormat('j M Y') }}
                            </span>
                        @else
                            <span class="rounded-full bg-krem-tua px-3 py-1 text-sm font-bold text-ink-muted">Terkunci</span>
                        @endif
                    </li>
                @endforeach
            </ul>

            <a href="{{ $selesaiSemua ? route('aktivitas.index') : route('materi.index') }}"
               class="mt-6 flex w-full items-center justify-center rounded-2xl bg-brand px-6 py-4 text-base font-bold text-white shadow-lg shadow-brand/20 hover:bg-brand/90">
                {{ $selesaiSemua ? 'Buka Kalender Stimulasi' : 'Lanjutkan Materi' }}
            </a>
        </section>
    </div>
</x-app-layout>
