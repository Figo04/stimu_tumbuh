{{-- Riwayat kehamilan & kesehatan ibu — dipakai form daftar dan profil ($anak null saat daftar). --}}
@php
    $pilihan = 'flex cursor-pointer items-center gap-3 rounded-2xl border border-krem-garis px-4 py-3.5 has-[:checked]:border-brand has-[:checked]:bg-brand-soft';
    $radio = 'h-5 w-5 border-krem-garis text-brand focus:ring-brand';
    // old() berisi '1'/'0', model berisi true/false/null.
    $pilih = fn ($k) => ($v = old("anak.$k", $anak?->$k)) === null ? null : (bool) $v;
    $kebiasaan = old('anak.kebiasaan_ibu', $anak?->kebiasaan_ibu ?? []);
@endphp

<fieldset>
    <legend class="font-bold">Riwayat anemia ibu saat hamil</legend>
    <div class="mt-2 grid grid-cols-2 gap-3">
        @foreach ([1 => 'Anemia', 0 => 'Tidak anemia'] as $value => $label)
            <label class="{{ $pilihan }}">
                <input type="radio" name="anak[anemia_hamil]" value="{{ $value }}" class="{{ $radio }}" @checked($pilih('anemia_hamil') === (bool) $value) required>
                <span class="font-semibold">{{ $label }}</span>
            </label>
        @endforeach
    </div>
    <x-input-error :messages="$errors->get('anak.anemia_hamil')" class="mt-2" />
</fieldset>

@foreach (['penyakit_hamil' => 'Riwayat penyakit ibu saat hamil', 'penyakit_keturunan' => 'Riwayat penyakit keturunan'] as $name => $judul)
    <fieldset x-data="{ ada: @js($pilih($name) === true) }">
        <legend class="font-bold">{{ $judul }}</legend>
        <div class="mt-2 grid grid-cols-2 gap-3">
            @foreach ([1 => 'Ada', 0 => 'Tidak ada'] as $value => $label)
                <label class="{{ $pilihan }}">
                    <input type="radio" name="anak[{{ $name }}]" value="{{ $value }}" class="{{ $radio }}" x-on:change="ada = @js((bool) $value)" @checked($pilih($name) === (bool) $value) required>
                    <span class="font-semibold">{{ $label }}</span>
                </label>
            @endforeach
        </div>
        <x-input-error :messages="$errors->get('anak.'.$name)" class="mt-2" />

        <div x-show="ada" x-cloak class="mt-3">
            <x-input-label for="anak_{{ $name }}_ket" value="Tuliskan nama penyakitnya" />
            <x-text-input id="anak_{{ $name }}_ket" class="mt-2 block w-full" type="text" name="anak[{{ $name }}_ket]" :value="old('anak.'.$name.'_ket', $anak?->{$name.'_ket'})" maxlength="255" x-bind:required="ada" />
            <x-input-error :messages="$errors->get('anak.'.$name.'_ket')" class="mt-2" />
        </div>
    </fieldset>
@endforeach

<fieldset x-data="{ lain: @js(in_array('lainnya', $kebiasaan, true)) }">
    <legend class="font-bold">Kebiasaan ibu (boleh pilih lebih dari satu)</legend>
    <div class="mt-2 grid grid-cols-2 gap-3">
        @foreach (\App\Models\Anak::KEBIASAAN_IBU as $value => $label)
            <label class="{{ $pilihan }}">
                <input type="checkbox" name="anak[kebiasaan_ibu][]" value="{{ $value }}" class="h-5 w-5 rounded border-krem-garis text-brand focus:ring-brand"
                    @if ($value === 'lainnya') x-on:change="lain = $event.target.checked" @endif @checked(in_array($value, $kebiasaan, true))>
                <span class="font-semibold">{{ $label }}</span>
            </label>
        @endforeach
    </div>
    <x-input-error :messages="$errors->get('anak.kebiasaan_ibu')" class="mt-2" />

    <div x-show="lain" x-cloak class="mt-3">
        <x-input-label for="anak_kebiasaan_ibu_lain" value="Tuliskan kebiasaan lainnya" />
        <x-text-input id="anak_kebiasaan_ibu_lain" class="mt-2 block w-full" type="text" name="anak[kebiasaan_ibu_lain]" :value="old('anak.kebiasaan_ibu_lain', $anak?->kebiasaan_ibu_lain)" maxlength="255" x-bind:required="lain" />
        <x-input-error :messages="$errors->get('anak.kebiasaan_ibu_lain')" class="mt-2" />
    </div>
</fieldset>
