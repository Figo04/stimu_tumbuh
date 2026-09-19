<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pre-test</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="mb-6 text-gray-700">
                Isi semua pertanyaan di bawah sebelum membuka materi. Jawaban hanya bisa dikirim
                <strong>satu kali</strong> dan tidak dapat diubah setelah dikirim.
            </p>

            @if ($errors->any())
                <div class="mb-6 rounded-md bg-red-50 p-4 text-sm text-red-700">
                    Masih ada pertanyaan yang belum dijawab. Periksa kembali tanda merah di bawah.
                </div>
            @endif

            <form method="POST" action="{{ route('pretest.store') }}"
                  onsubmit="return confirm('Kirim jawaban sekarang? Jawaban tidak dapat diubah setelah dikirim.')">
                @csrf

                @foreach ($soal as $tipe => $items)
                    @php
                        $pilihan = $tipe === 'pengetahuan'
                            ? \App\Models\KuesionerSoal::JAWABAN_PENGETAHUAN
                            : \App\Models\KuesionerSoal::JAWABAN_SIKAP;
                    @endphp

                    <section class="mb-8 bg-white shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">
                            Bagian {{ $loop->iteration }}: {{ \App\Models\KuesionerSoal::TIPE[$tipe] }}
                        </h3>
                        <p class="mb-4 text-sm text-gray-600">
                            {{ $tipe === 'pengetahuan'
                                ? 'Pilih Benar atau Salah untuk setiap pernyataan.'
                                : 'Pilih seberapa setuju Anda dengan setiap pernyataan.' }}
                        </p>

                        @foreach ($items as $s)
                            <fieldset class="py-4 border-t border-gray-100">
                                <legend class="text-gray-900 mb-3">{{ $loop->iteration }}. {{ $s->pertanyaan }}</legend>
                                <div class="flex flex-wrap gap-3">
                                    @foreach ($pilihan as $kode => $label)
                                        <label class="flex items-center gap-2 rounded-md border border-gray-300 px-4 py-3 cursor-pointer has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50">
                                            <input type="radio" name="jawaban[{{ $s->id }}]" value="{{ $kode }}" required
                                                   @checked(old("jawaban.{$s->id}") === $kode)>
                                            <span>{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <x-input-error :messages="$errors->get('jawaban.'.$s->id)" class="mt-2" />
                            </fieldset>
                        @endforeach
                    </section>
                @endforeach

                <x-input-error :messages="$errors->get('jawaban')" class="mb-4" />

                <x-primary-button class="w-full justify-center py-3 text-base">Kirim Jawaban</x-primary-button>
            </form>
        </div>
    </div>
</x-app-layout>
