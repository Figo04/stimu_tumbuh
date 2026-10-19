{{-- Field soal kuesioner; $soal null = soal baru. Kelompok usia & kategori hanya dipilih saat membuat (lihat SoalController::data). --}}
@php($field = 'mt-2 block w-full rounded-2xl border-krem-garis px-4 py-3.5 text-base text-ink focus:border-brand focus:ring-brand')

<div class="space-y-5">
    @if ($soal)
        <p>
            <span class="rounded-full bg-krem-tua px-3 py-1 text-sm font-bold">Usia {{ $soal->kelompok_usia }} bln</span>
            <span class="rounded-full bg-krem-tua px-3 py-1 text-sm font-bold">{{ \App\Models\ItemPerkembangan::ASPEK[$soal->aspek] }}</span>
            <span class="text-sm text-ink-muted">— tidak bisa diubah; jawaban yang sudah masuk terskor di kelompok & kategori ini.</span>
        </p>
    @else
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <x-input-label for="kelompok_usia" value="Kelompok usia" />
                <select id="kelompok_usia" name="kelompok_usia" class="{{ $field }}" required>
                    @foreach (\App\Services\UsiaAnakService::KELOMPOK_USIA as $kelompok)
                        <option value="{{ $kelompok }}" @selected(old('kelompok_usia') === $kelompok)>{{ $kelompok }} bulan</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('kelompok_usia')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="aspek" value="Kategori" />
                <select id="aspek" name="aspek" class="{{ $field }}" required>
                    @foreach (\App\Models\ItemPerkembangan::ASPEK as $value => $label)
                        <option value="{{ $value }}" @selected(old('aspek') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('aspek')" class="mt-2" />
            </div>
        </div>
    @endif

    <div>
        <x-input-label for="pertanyaan" value="Pertanyaan" />
        <textarea id="pertanyaan" name="pertanyaan" rows="3" maxlength="1000" class="{{ $field }}" required>{{ old('pertanyaan', $soal?->pertanyaan) }}</textarea>
        <p class="mt-2 text-sm text-ink-muted">Dijawab orang tua dengan Ya atau Tidak.</p>
        <x-input-error :messages="$errors->get('pertanyaan')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="urutan" value="Urutan tampil" />
        <x-text-input id="urutan" name="urutan" type="number" min="1" max="255" inputmode="numeric" class="mt-2 block w-32"
                      :value="old('urutan', $soal?->urutan ?? ($urutanBerikutnya ?? 1))" required />
        <p class="mt-2 text-sm text-ink-muted">Urutan soal di dalam kelompok usianya; angka kecil tampil lebih dulu.</p>
        <x-input-error :messages="$errors->get('urutan')" class="mt-2" />
    </div>
</div>
