<x-admin-layout judul="Kelola Materi">
    @if (session('status'))
        <p class="mb-4 rounded-md bg-emerald-50 p-4 text-sm text-emerald-800">{{ session('status') }}</p>
    @endif

    <a href="{{ route('admin.materi.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&lsaquo; Semua materi</a>

    <h2 class="mt-1 text-xl font-semibold text-gray-800">{{ $materi->judul }}</h2>
    <p class="mb-6 text-sm text-gray-500">
        {{ \App\Models\Materi::ASPEK[$materi->aspek] }} ·
        usia {{ str_replace('-', '–', $materi->kelompok_usia) }} bulan ·
        urutan {{ $materi->urutan }}
    </p>

    <section class="mb-6 rounded-lg border border-gray-200 bg-white p-4">
        <h3 class="font-semibold text-gray-800">Tautan video YouTube</h3>
        <p class="mt-1 text-sm text-gray-600">
            Tempel tautan lengkap (<code>youtube.com/watch?v=…</code>, <code>youtu.be/…</code>) atau ID videonya saja.
            Kosongkan lalu simpan untuk menghapus video — tombol "Tonton video" akan hilang dari halaman orang tua.
        </p>

        <form method="POST" action="{{ route('admin.materi.update', $materi) }}" class="mt-3 flex flex-wrap items-start gap-2">
            @csrf
            @method('PATCH')
            <div class="min-w-64 flex-1">
                <input type="text" name="video_youtube_id" value="{{ old('video_youtube_id', $materi->video_youtube_id) }}"
                       placeholder="https://www.youtube.com/watch?v=…"
                       class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                @error('video_youtube_id')
                    <p class="mt-1 text-sm text-rose-700">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                Simpan
            </button>
        </form>

        @if ($materi->video_youtube_id)
            <div class="mt-4 aspect-video max-w-xl">
                <iframe class="h-full w-full rounded-md" src="https://www.youtube-nocookie.com/embed/{{ $materi->video_youtube_id }}?rel=0"
                        title="Pratinjau video {{ $materi->judul }}" allow="encrypted-media; picture-in-picture; fullscreen" allowfullscreen></iframe>
            </div>
        @endif
    </section>

    <section class="rounded-lg border border-gray-200 bg-white">
        <div class="flex flex-wrap items-baseline justify-between gap-2 border-b border-gray-200 px-4 py-3">
            <h3 class="font-semibold text-gray-800">Isi materi (tampilan bagi orang tua)</h3>
            <p class="text-xs text-gray-500">Hanya bisa dilihat. Perubahan isi lewat developer — <code>{{ $materi->konten_view }}</code></p>
        </div>
        <article class="p-6 text-base leading-relaxed text-gray-800">
            @include($materi->konten_view)
        </article>
    </section>
</x-admin-layout>
