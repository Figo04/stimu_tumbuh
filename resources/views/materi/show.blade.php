<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('materi.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&lsaquo; Semua materi</a>
        <h2 class="mt-1 font-semibold text-xl text-gray-800 leading-tight">{{ $materi->judul }}</h2>
        <p class="text-sm text-gray-500">{{ \App\Models\Materi::ASPEK[$materi->aspek] }}</p>
    </x-slot>

    <div class="py-8" x-data="{ tab: @js(session('status') === 'praktik-tersimpan' || $errors->any() ? 'praktik' : 'materi') }">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-4 grid grid-cols-2 gap-2" role="tablist">
                <button type="button" role="tab" @click="tab = 'materi'" :aria-selected="tab === 'materi'"
                        :class="tab === 'materi' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700'"
                        class="rounded-md px-4 py-3 text-base font-medium shadow-sm">Materi</button>
                <button type="button" role="tab" @click="tab = 'praktik'" :aria-selected="tab === 'praktik'"
                        :class="tab === 'praktik' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700'"
                        class="rounded-md px-4 py-3 text-base font-medium shadow-sm">Praktik{{ $progress->materi_selesai ? '' : ' 🔒' }}</button>
            </div>

            <article x-show="tab === 'materi'" class="bg-white shadow-sm sm:rounded-lg p-6 text-base leading-relaxed text-gray-800">
                @if ($materi->video_youtube_id)
                    <div x-data="{ buka: false, ditonton: @js($progress->video_ditonton) }" @keydown.escape.window="buka = false" class="mb-6">
                        <button type="button" class="w-full rounded-md bg-red-600 px-4 py-3 text-base font-medium text-white shadow-sm hover:bg-red-700"
                                @click="buka = true; if (! ditonton) { ditonton = true; fetch(@js(route('materi.video', $materi)), { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content } }) }">
                            ▶ Tonton video
                        </button>
                        <p x-show="ditonton" class="mt-2 text-sm text-emerald-700">✓ Video sudah ditonton</p>

                        {{-- x-if (bukan x-show): iframe dihapus saat popup ditutup supaya video berhenti. --}}
                        <template x-if="buka">
                            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/75 p-4" @click.self="buka = false" role="dialog" aria-modal="true" aria-label="Video materi">
                                <div class="w-full max-w-3xl">
                                    <button type="button" @click="buka = false" class="mb-2 ml-auto block rounded-md bg-white px-4 py-2 text-base font-medium text-gray-800">✕ Tutup</button>
                                    <div class="aspect-video w-full">
                                        <iframe class="h-full w-full rounded-md" src="https://www.youtube-nocookie.com/embed/{{ $materi->video_youtube_id }}?autoplay=1&rel=0"
                                                title="Video {{ $materi->judul }}" allow="autoplay; encrypted-media; picture-in-picture; fullscreen" allowfullscreen></iframe>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                @endif

                @include($materi->konten_view)

                <div class="mt-8 border-t border-gray-100 pt-6">
                    @if ($progress->materi_selesai)
                        <p class="rounded-md bg-emerald-50 px-4 py-3 text-emerald-800">
                            ✓ Materi selesai dibaca pada {{ $progress->materi_selesai_at->translatedFormat('d F Y') }}.
                        </p>
                    @else
                        <form method="POST" action="{{ route('materi.selesai', $materi) }}">
                            @csrf
                            <button type="submit" class="w-full rounded-md bg-indigo-600 px-4 py-3 text-base font-medium text-white shadow-sm hover:bg-indigo-700">
                                Tandai selesai dibaca
                            </button>
                        </form>
                    @endif
                </div>
            </article>

            {{-- Berkelanjutan: boleh diisi berulang, tiap kiriman = entri baru di kalender stimulasi. --}}
            <div x-show="tab === 'praktik'" style="display: none" class="bg-white shadow-sm sm:rounded-lg p-6 text-base text-gray-800">
                @if (! $progress->materi_selesai)
                    <p class="text-gray-600">🔒 Tab Praktik terbuka setelah materi ini selesai dibaca.</p>
                @else
                    @if (session('status') === 'praktik-tersimpan')
                        <p class="mb-4 rounded-md bg-emerald-50 px-4 py-3 text-emerald-800">✓ Praktik tersimpan. Anda bisa mencatatnya lagi kapan saja.</p>
                    @endif

                    <form method="POST" action="{{ route('materi.praktik', $materi) }}" class="space-y-4">
                        @csrf
                        <div>
                            <x-input-label for="tanggal" value="Tanggal praktik" />
                            <x-text-input id="tanggal" name="tanggal" type="date" class="mt-1 block w-full"
                                          :value="old('tanggal', now()->toDateString())" max="{{ now()->toDateString() }}" required />
                            <x-input-error :messages="$errors->get('tanggal')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="respons_anak" value="Bagaimana respons anak? (opsional)" />
                            <textarea id="respons_anak" name="respons_anak" rows="3" maxlength="1000"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('respons_anak') }}</textarea>
                            <x-input-error :messages="$errors->get('respons_anak')" class="mt-2" />
                        </div>
                        <button type="submit" class="w-full rounded-md bg-indigo-600 px-4 py-3 text-base font-medium text-white shadow-sm hover:bg-indigo-700">
                            Sudah saya praktikkan
                        </button>
                    </form>

                    <h3 class="mt-8 font-semibold text-gray-800">Riwayat praktik</h3>
                    @forelse ($riwayatPraktik as $entri)
                        <div class="mt-3 border-t border-gray-100 pt-3">
                            <p class="text-sm font-medium text-gray-600">{{ $entri->tanggal->translatedFormat('d F Y') }}</p>
                            @if ($entri->respons_anak)
                                <p class="mt-1 whitespace-pre-line">{{ $entri->respons_anak }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="mt-2 text-gray-500">Belum ada catatan praktik.</p>
                    @endforelse
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
