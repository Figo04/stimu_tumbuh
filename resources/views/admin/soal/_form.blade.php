{{-- Field soal kuesioner; $soal null = soal baru. Tipe hanya dipilih saat membuat (lihat SoalController::aturan). --}}
@php($field = 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500')

<div x-data="{ tipe: @js(old('tipe', $soal?->tipe ?? 'pengetahuan')) }" class="space-y-5">
    <div>
        <x-input-label for="tipe" value="Tipe soal" />
        @if ($soal)
            <p class="mt-1 text-gray-700">
                {{ \App\Models\KuesionerSoal::TIPE[$soal->tipe] }}
                <span class="text-sm text-gray-500">— tidak bisa diubah; jawaban yang sudah masuk memakai skala tipe ini.</span>
            </p>
        @else
            <select id="tipe" name="tipe" x-model="tipe" class="{{ $field }}" required>
                @foreach (\App\Models\KuesionerSoal::TIPE as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
            <p class="mt-1 text-sm text-gray-500">Pengetahuan dijawab Benar/Salah, sikap dijawab SS/S/TS/STS.</p>
            <x-input-error :messages="$errors->get('tipe')" class="mt-2" />
        @endif
    </div>

    <div>
        <x-input-label for="pertanyaan" value="Pertanyaan" />
        <textarea id="pertanyaan" name="pertanyaan" rows="3" maxlength="1000" class="{{ $field }}" required>{{ old('pertanyaan', $soal?->pertanyaan) }}</textarea>
        <x-input-error :messages="$errors->get('pertanyaan')" class="mt-2" />
    </div>

    <div x-show="tipe === 'pengetahuan'" x-cloak>
        <x-input-label for="jawaban_benar" value="Jawaban benar" />
        <select id="jawaban_benar" name="jawaban_benar" class="{{ $field }}">
            <option value="">— Pilih —</option>
            @foreach (\App\Models\KuesionerSoal::JAWABAN_PENGETAHUAN as $value => $label)
                <option value="{{ $value }}" @selected(old('jawaban_benar', $soal?->jawaban_benar) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('jawaban_benar')" class="mt-2" />
    </div>

    <div x-show="tipe === 'sikap'" x-cloak>
        <label class="flex items-start gap-3">
            <input type="hidden" name="reverse_scored" value="0">
            <input type="checkbox" name="reverse_scored" value="1" @checked(old('reverse_scored', $soal?->reverse_scored))
                   class="mt-1 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
            <span class="text-sm text-gray-700">
                <span class="font-medium">Skor dibalik (reverse scored)</span><br>
                Centang bila pernyataan ini bernada negatif, sehingga "Sangat Setuju" justru bernilai rendah.
            </span>
        </label>
    </div>

    <div>
        <x-input-label for="urutan" value="Urutan tampil" />
        <x-text-input id="urutan" name="urutan" type="number" min="1" max="255" inputmode="numeric" class="mt-1 block w-32"
                      :value="old('urutan', $soal?->urutan ?? ($urutanBerikutnya ?? 1))" required />
        <p class="mt-1 text-sm text-gray-500">Urutan soal di dalam tipenya; angka kecil tampil lebih dulu.</p>
        <x-input-error :messages="$errors->get('urutan')" class="mt-2" />
    </div>
</div>
