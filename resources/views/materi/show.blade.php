<x-app-layout>
    <div class="mx-auto max-w-2xl px-4 py-6" x-data="{ tab: @js(session('status') === 'praktik-tersimpan' || $errors->any() ? 'praktik' : 'materi') }">
        <a href="{{ route('materi.index') }}" class="inline-flex items-center gap-1 font-bold text-brand hover:underline">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6" /></svg>
            Semua materi
        </a>
        <div class="mt-4"><x-chip-aspek :aspek="$materi->aspek" /></div>
        <h1 class="mt-3 text-3xl font-extrabold leading-tight">{{ $materi->judul }}</h1>

        <div class="mt-6 grid grid-cols-2 gap-2 rounded-2xl bg-krem-tua p-1.5" role="tablist">
            <button type="button" role="tab" @click="tab = 'materi'" :aria-selected="tab === 'materi'"
                    :class="tab === 'materi' ? 'bg-white text-ink shadow-sm' : 'text-ink-muted'"
                    class="rounded-xl px-4 py-3 text-base font-bold">Materi</button>
            <button type="button" role="tab" @click="tab = 'praktik'" :aria-selected="tab === 'praktik'"
                    :class="tab === 'praktik' ? 'bg-white text-ink shadow-sm' : 'text-ink-muted'"
                    class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-3 text-base font-bold">
                Praktik
                @unless ($progress->materi_selesai)
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-label="terkunci"><rect width="18" height="11" x="3" y="11" rx="2" /><path d="M7 11V7a5 5 0 0 1 10 0v4" /></svg>
                @endunless
            </button>
        </div>

        {{-- Materi = video saja (keputusan klien, 2 Okt 2026); selesai baru bisa ditandai setelah video dibuka. --}}
        <article x-show="tab === 'materi'" class="mt-4 rounded-3xl bg-white p-6 text-lg leading-relaxed shadow-sm">
            @if ($materi->video_youtube_id)
                <div x-data="{ buka: false, ditonton: @js($progress->video_ditonton) }" @keydown.escape.window="buka = false">
                    <button type="button" class="flex w-full items-center justify-center gap-3 rounded-2xl bg-brand px-4 py-4 text-base font-bold text-white shadow-lg shadow-brand/20 hover:bg-brand/90"
                            @click="buka = true; if (! ditonton) { ditonton = true; fetch(@js(route('materi.video', $materi)), { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content } }) }">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5.5v13l11-6.5z" /></svg>
                        Tonton video
                    </button>
                    <p x-show="ditonton" class="mt-2 text-sm font-bold text-sukses">✓ Video sudah ditonton</p>

                    {{-- x-if (bukan x-show): iframe dihapus saat popup ditutup supaya video berhenti. --}}
                    <template x-if="buka">
                        <div class="fixed inset-0 z-50 flex items-center justify-center bg-ink/80 p-4" @click.self="buka = false" role="dialog" aria-modal="true" aria-label="Video materi">
                            <div class="w-full max-w-3xl">
                                <button type="button" @click="buka = false" class="mb-3 ml-auto block rounded-2xl bg-white px-5 py-2.5 text-base font-bold text-ink">✕ Tutup</button>
                                <div class="aspect-video w-full">
                                    <iframe class="h-full w-full rounded-2xl" src="https://www.youtube-nocookie.com/embed/{{ $materi->video_youtube_id }}?autoplay=1&rel=0"
                                            title="Video {{ $materi->judul }}" allow="autoplay; encrypted-media; picture-in-picture; fullscreen" allowfullscreen></iframe>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div class="mt-8 border-t border-krem-garis pt-6">
                        @if ($progress->materi_selesai)
                            <p class="rounded-2xl bg-sukses-bg px-4 py-3 font-bold text-sukses">
                                ✓ Materi selesai ditonton pada {{ $progress->materi_selesai_at->translatedFormat('d F Y') }}.
                            </p>
                        @else
                            <p x-show="! ditonton" class="text-ink-muted">Tonton videonya dulu, lalu tandai selesai di sini.</p>
                            <form x-show="ditonton" method="POST" action="{{ route('materi.selesai', $materi) }}">
                                @csrf
                                <x-primary-button class="w-full">Tandai selesai ditonton</x-primary-button>
                            </form>
                        @endif
                    </div>
                </div>
            @else
                <p class="text-ink-muted">Video untuk materi ini belum tersedia. Silakan kembali lagi nanti.</p>
            @endif
        </article>

        {{-- Berkelanjutan: boleh diisi berulang, tiap kiriman = entri baru di kalender stimulasi. --}}
        <div x-show="tab === 'praktik'" style="display: none" class="mt-4 rounded-3xl bg-white p-6 text-lg shadow-sm">
            @if (! $progress->materi_selesai)
                <p class="text-ink-muted">Tab Praktik terbuka setelah video materi ini selesai ditonton.</p>
            @else
                @if (session('status') === 'praktik-tersimpan')
                    <p class="mb-4 rounded-2xl bg-sukses-bg px-4 py-3 font-bold text-sukses">✓ Praktik tersimpan. Anda bisa mencatatnya lagi kapan saja.</p>
                @endif

                <form method="POST" action="{{ route('materi.praktik', $materi) }}" class="space-y-5">
                    @csrf
                    <div>
                        <x-input-label for="tanggal" value="Tanggal praktik" />
                        <x-text-input id="tanggal" name="tanggal" type="date" class="mt-2 block w-full"
                                      :value="old('tanggal', now()->toDateString())" max="{{ now()->toDateString() }}" required />
                        <x-input-error :messages="$errors->get('tanggal')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="respons_anak" value="Bagaimana respons anak? (opsional)" />
                        <textarea id="respons_anak" name="respons_anak" rows="3" maxlength="1000"
                                  class="mt-2 block w-full rounded-2xl border-krem-garis px-4 py-3.5 text-base text-ink focus:border-brand focus:ring-brand">{{ old('respons_anak') }}</textarea>
                        <x-input-error :messages="$errors->get('respons_anak')" class="mt-2" />
                    </div>
                    <x-primary-button class="w-full">Sudah saya praktikkan</x-primary-button>
                </form>

                <h2 class="mt-8 text-xl font-extrabold">Riwayat praktik</h2>
                @forelse ($riwayatPraktik as $entri)
                    <div class="mt-3 rounded-2xl bg-krem px-4 py-3">
                        <p class="text-sm font-bold text-ink-muted">{{ $entri->tanggal->translatedFormat('d F Y') }}</p>
                        @if ($entri->respons_anak)
                            <p class="mt-1 whitespace-pre-line">{{ $entri->respons_anak }}</p>
                        @endif
                    </div>
                @empty
                    <p class="mt-2 text-ink-muted">Belum ada catatan praktik.</p>
                @endforelse
            @endif
        </div>
    </div>
</x-app-layout>
