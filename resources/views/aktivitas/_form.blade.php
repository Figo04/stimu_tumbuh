{{-- Field entri kalender; $entri null = entri baru. Entri dari tab Praktik: aspek dikunci ke materinya. --}}
@php($field = 'mt-2 block w-full rounded-2xl border-krem-garis px-4 py-3.5 text-base text-ink focus:border-brand focus:ring-brand')

<div>
    <x-input-label for="tanggal" value="Tanggal stimulasi" />
    <x-text-input id="tanggal" name="tanggal" type="date" class="mt-2 block w-full"
                  :value="old('tanggal', $entri?->tanggal->toDateString() ?? now()->toDateString())" max="{{ now()->toDateString() }}" required />
    <x-input-error :messages="$errors->get('tanggal')" class="mt-2" />
</div>

<div>
    <x-input-label for="aspek" value="Aspek perkembangan" />
    @if ($entri?->materi_id)
        <p class="mt-2 text-ink">{{ \App\Models\Materi::ASPEK[$entri->aspek] }} <span class="text-sm text-ink-muted">(dari materi {{ $entri->materi?->judul }})</span></p>
    @else
        <select id="aspek" name="aspek" class="{{ $field }}" required>
            <option value="">— Pilih —</option>
            @foreach (\App\Models\Materi::ASPEK as $value => $label)
                <option value="{{ $value }}" @selected(old('aspek', $entri?->aspek) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('aspek')" class="mt-2" />
    @endif
</div>

<div>
    <x-input-label for="jenis_stimulasi" value="Jenis stimulasi (opsional)" />
    <x-text-input id="jenis_stimulasi" name="jenis_stimulasi" type="text" class="mt-2 block w-full" maxlength="255"
                  :value="old('jenis_stimulasi', $entri?->jenis_stimulasi)" placeholder="mis. bermain cilukba" />
    <x-input-error :messages="$errors->get('jenis_stimulasi')" class="mt-2" />
</div>

<div>
    <x-input-label for="durasi_menit" value="Durasi dalam menit (opsional)" />
    <x-text-input id="durasi_menit" name="durasi_menit" type="number" min="1" max="1440" inputmode="numeric" class="mt-2 block w-full"
                  :value="old('durasi_menit', $entri?->durasi_menit)" />
    <x-input-error :messages="$errors->get('durasi_menit')" class="mt-2" />
</div>

<div>
    <x-input-label for="pelaku" value="Siapa yang melakukan?" />
    <select id="pelaku" name="pelaku" class="{{ $field }}" required>
        @foreach (\App\Models\AktivitasStimulasi::PELAKU as $value => $label)
            <option value="{{ $value }}" @selected(old('pelaku', $entri?->pelaku ?? auth()->user()->hubungan_dengan_anak) === $value)>{{ $label }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('pelaku')" class="mt-2" />
</div>

<div>
    <x-input-label for="respons_anak" value="Bagaimana respons anak? (opsional)" />
    <textarea id="respons_anak" name="respons_anak" rows="3" maxlength="1000" class="{{ $field }}">{{ old('respons_anak', $entri?->respons_anak) }}</textarea>
    <x-input-error :messages="$errors->get('respons_anak')" class="mt-2" />
</div>
