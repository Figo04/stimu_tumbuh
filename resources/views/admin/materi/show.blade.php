<x-admin-layout judul="Kelola Materi">
    @if (session('status'))
        <x-auth-session-status class="mb-4" :status="session('status')" />
    @endif

    <a href="{{ route('admin.materi.index') }}" class="font-bold text-brand hover:underline">&larr; Semua materi</a>

    <h2 class="mt-1 text-2xl font-extrabold">{{ $materi->judul }}</h2>
    <p class="mb-6 text-sm text-ink-muted">
        {{ \App\Models\Materi::ASPEK[$materi->aspek] }} ·
        usia {{ str_replace('-', '–', $materi->kelompok_usia) }} bulan ·
        urutan {{ $materi->urutan }}
    </p>

    <section class="mb-6 overflow-hidden rounded-3xl bg-white p-6 shadow-sm">
        <h3 class="text-lg font-extrabold">Tautan video YouTube</h3>
        <p class="mt-1 text-sm text-ink-muted">
            Tempel tautan lengkap (<code>youtube.com/watch?v=…</code>, <code>youtu.be/…</code>) atau ID videonya saja.
            Kosongkan lalu simpan untuk menghapus video — tombol "Tonton video" akan hilang dari halaman orang tua.
        </p>

        <form method="POST" action="{{ route('admin.materi.update', $materi) }}" class="mt-3 flex flex-wrap items-start gap-2">
            @csrf
            @method('PATCH')
            <div class="min-w-64 flex-1">
                <x-text-input type="text" name="video_youtube_id" :value="old('video_youtube_id', $materi->video_youtube_id)"
                              placeholder="https://www.youtube.com/watch?v=…" class="w-full" />
                @error('video_youtube_id')
                    <p class="mt-1 text-sm text-bahaya">{{ $message }}</p>
                @enderror
            </div>
            <x-primary-button>Simpan</x-primary-button>
        </form>

        @if ($materi->video_youtube_id)
            <div class="mt-4 aspect-video max-w-xl">
                <iframe class="h-full w-full rounded-2xl" src="https://www.youtube-nocookie.com/embed/{{ $materi->video_youtube_id }}?rel=0"
                        title="Pratinjau video {{ $materi->judul }}" allow="encrypted-media; picture-in-picture; fullscreen" allowfullscreen></iframe>
            </div>
        @endif
    </section>

    <section class="overflow-hidden rounded-3xl bg-white shadow-sm">
        <div class="flex flex-wrap items-baseline justify-between gap-2 border-b border-krem-garis px-6 py-5">
            <h3 class="text-lg font-extrabold">Isi materi (tampilan bagi orang tua)</h3>
            <p class="text-xs text-ink-muted">Hanya bisa dilihat. Perubahan isi lewat developer — <code>{{ $materi->konten_view }}</code></p>
        </div>
        <article class="p-6 text-base leading-relaxed text-ink">
            @include($materi->konten_view)
        </article>
    </section>
</x-admin-layout>
