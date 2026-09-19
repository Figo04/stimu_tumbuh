<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Perkembangan Anak</h2>
        <p class="text-sm text-gray-500">
            Kelompok usia {{ $anak->kelompok_usia }} bulan. Boleh dinilai ulang kapan saja untuk melihat perubahan.
        </p>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <p class="rounded-md bg-emerald-50 px-4 py-3 text-emerald-800">✓ {{ session('status') }}</p>
            @endif

            @if ($terakhir)
                <section class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6 text-gray-800">
                    <h3 class="font-semibold text-gray-900">
                        Hasil penilaian terakhir
                        <span class="font-normal text-sm text-gray-500">({{ $terakhir->tanggal_penilaian->translatedFormat('j F Y') }})</span>
                    </h3>
                    <dl class="mt-3 grid grid-cols-2 gap-3">
                        @foreach (\App\Models\Materi::ASPEK as $aspek => $label)
                            <div class="rounded-md bg-gray-50 p-3">
                                <dt class="text-sm text-gray-600">{{ $label }}</dt>
                                <dd class="text-lg font-semibold">{{ $terakhir->{"skor_$aspek"} + 0 }}%</dd>
                            </div>
                        @endforeach
                    </dl>
                    <p class="mt-3 text-gray-700">Total: <strong>{{ $terakhir->skor_total + 0 }}%</strong> kemampuan sudah bisa dilakukan anak.</p>
                </section>
            @endif

            @if ($item->isEmpty())
                <p class="rounded-md bg-amber-50 px-4 py-3 text-amber-800">
                    Checklist untuk kelompok usia ini belum tersedia.
                </p>
            @else
                @if ($errors->any())
                    <div class="rounded-md bg-red-50 p-4 text-sm text-red-700">
                        Masih ada kemampuan yang belum dijawab. Periksa kembali tanda merah di bawah.
                    </div>
                @endif

                <form method="POST" action="{{ route('perkembangan.store') }}" class="space-y-6">
                    @csrf
                    <p class="text-gray-700">Apakah anak Anda <strong>sudah bisa</strong> melakukan hal berikut? Jawab Ya atau Tidak.</p>

                    @foreach (\App\Models\Materi::ASPEK as $aspek => $label)
                        @continue(! $item->has($aspek))
                        <section class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $label }}</h3>
                            @foreach ($item[$aspek] as $i)
                                <fieldset class="py-4 border-t border-gray-100">
                                    <legend class="text-gray-900 mb-3">{{ $loop->iteration }}. {{ $i->pertanyaan }}</legend>
                                    <div class="flex gap-3">
                                        @foreach (['1' => 'Ya', '0' => 'Tidak'] as $nilai => $teks)
                                            <label class="flex flex-1 sm:flex-none items-center justify-center gap-2 rounded-md border border-gray-300 px-6 py-3 cursor-pointer has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50">
                                                <input type="radio" name="jawaban[{{ $i->id }}]" value="{{ $nilai }}" required
                                                       @checked(old("jawaban.{$i->id}") === (string) $nilai)>
                                                <span>{{ $teks }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <x-input-error :messages="$errors->get('jawaban.'.$i->id)" class="mt-2" />
                                </fieldset>
                            @endforeach
                        </section>
                    @endforeach

                    <x-input-error :messages="$errors->get('jawaban')" />

                    <x-primary-button class="w-full justify-center py-3 text-base">Simpan Penilaian</x-primary-button>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
