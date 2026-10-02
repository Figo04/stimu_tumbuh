<x-app-layout>
    <div class="mx-auto max-w-2xl px-4 py-6">
        <h1 class="text-3xl font-extrabold">Materi Stimulasi</h1>

        @if (session('status'))
            <x-auth-session-status class="mt-4" :status="session('status')" />
        @endif

        {{-- Kartu hero: kelompok usia + progres belajar. --}}
        <section class="mt-6 rounded-3xl bg-brand p-6 text-white shadow-lg shadow-brand/20">
            <p class="font-bold text-white/90">Disiapkan khusus untuk {{ $anak->nama_inisial }}</p>
            <p class="mt-1 text-2xl font-extrabold">Materi untuk usia {{ str_replace('-', '–', $kelompokUsia) }} bulan</p>
            <p class="mt-2 text-lg text-white/90">Materi mengikuti usia anak dan berubah saat anak bertambah besar.</p>

            @if ($total)
                <div class="mt-6 flex items-center justify-between gap-4 font-bold">
                    <span>Perkembangan belajar</span>
                    <span>{{ $selesai }} dari {{ $total }} materi selesai</span>
                </div>
                <div class="mt-3 h-3 overflow-hidden rounded-full bg-brand-muda" role="progressbar"
                     aria-valuemin="0" aria-valuemax="{{ $total }}" aria-valuenow="{{ $selesai }}">
                    <div class="h-full rounded-full bg-white" style="width: {{ round($selesai / $total * 100) }}%"></div>
                </div>
            @endif
        </section>

        @foreach (\App\Models\Materi::ASPEK as $aspek => $label)
            @continue(! $materi->has($aspek))

            <section class="mt-8">
                <x-chip-aspek :aspek="$aspek" />

                <ul class="mt-4 space-y-4">
                    @foreach ($materi[$aspek] as $m)
                        @php
                            $p = $progress->get($m->id);
                            [$status, $badge] = match (true) {
                                (bool) $p?->materi_selesai => ['Selesai', 'bg-sukses-badge text-sukses'],
                                $p !== null => ['Sedang ditonton','bg-hangat-bg text-ink'],
                                default => ['Belum dibuka', 'bg-krem-tua text-ink-muted'],
                            };
                        @endphp
                        <li>
                            <a href="{{ route('materi.show', $m) }}"
                               class="flex items-center gap-4 rounded-3xl p-3 shadow-sm transition hover:shadow-md {{ $p?->materi_selesai ? 'bg-sukses-bg' : 'bg-white' }}">
                                <span class="relative flex h-28 w-28 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-krem-tua text-ink-muted">
                                    @if ($m->video_youtube_id)
                                        <img src="https://i.ytimg.com/vi/{{ $m->video_youtube_id }}/mqdefault.jpg" alt="" loading="lazy" class="h-full w-full object-cover">
                                        <span class="absolute bottom-2 left-2 flex h-9 w-9 items-center justify-center rounded-full bg-brand text-white" title="Ada video">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5.5v13l11-6.5z" /></svg>
                                            <span class="sr-only">Ada video</span>
                                        </span>
                                    @else
                                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M12 7v14" /><path d="M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3z" />
                                        </svg>
                                    @endif
                                </span>
                                <span class="min-w-0">
                                    <span class="block text-xl font-extrabold leading-snug">{{ $m->judul }}</span>
                                    <span class="mt-3 inline-block rounded-full px-3 py-1 text-sm font-bold {{ $badge }}">{{ $status }}</span>
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endforeach

        @if ($materi->isEmpty())
            <p class="mt-8 rounded-3xl bg-white p-6 text-ink-muted shadow-sm">Materi untuk kelompok usia ini belum tersedia.</p>
        @endif
    </div>
</x-app-layout>
