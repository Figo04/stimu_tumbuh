<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Materi Stimulasi</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="mb-6 text-gray-700">
                Materi untuk anak usia <strong>{{ $kelompokUsia }} bulan</strong>. Daftar ini menyesuaikan
                otomatis saat usia anak bertambah.
            </p>

            @foreach (\App\Models\Materi::ASPEK as $aspek => $label)
                @continue(! $materi->has($aspek))

                <section class="mb-6 bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">{{ $label }}</h3>
                    <ul class="divide-y divide-gray-100">
                        @foreach ($materi[$aspek] as $m)
                            <li>
                                <a href="{{ route('materi.show', $m) }}"
                                   class="flex items-center justify-between py-4 text-base text-gray-800 hover:text-indigo-600">
                                    <span>{{ $m->judul }}</span>
                                    <span aria-hidden="true">&rsaquo;</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endforeach

            @if ($materi->isEmpty())
                <div class="bg-white shadow-sm sm:rounded-lg p-6 text-gray-600">
                    Materi untuk kelompok usia ini belum tersedia.
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
