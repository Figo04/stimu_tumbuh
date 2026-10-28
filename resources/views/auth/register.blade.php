<x-guest-layout>
    {{-- Select/textarea tidak punya komponen Breeze; samakan dengan x-text-input. --}}
    @php($field = 'mt-2 block w-full rounded-2xl border-krem-garis px-4 py-3.5 text-base text-ink focus:border-brand focus:ring-brand')

    <h1 class="text-3xl font-extrabold">Daftar sebagai responden</h1>
    <p class="mt-2 text-lg text-ink-muted">Isi data Anda dan si kecil. Semua kolom wajib diisi, kecuali yang bertanda "tidak wajib".</p>

    <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-5">
        @csrf

        <h2 class="text-xl font-extrabold">Data Orang Tua</h2>

        <div>
            <x-input-label for="nama" value="Nama (boleh inisial)" />
            <x-text-input id="nama" class="mt-2 block w-full" type="text" name="nama" :value="old('nama')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('nama')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="hubungan_dengan_anak" value="Hubungan dengan anak" />
            <select id="hubungan_dengan_anak" name="hubungan_dengan_anak" class="{{ $field }}" required>
                @foreach (['ibu' => 'Ibu', 'ayah' => 'Ayah', 'pengasuh' => 'Pengasuh'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('hubungan_dengan_anak', 'ibu') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('hubungan_dengan_anak')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="umur" value="Umur (tahun)" />
            <x-text-input id="umur" class="mt-2 block w-full" type="number" inputmode="numeric" min="15" max="70" name="umur" :value="old('umur')" required />
            <x-input-error :messages="$errors->get('umur')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="no_hp" value="Nomor HP / WhatsApp" />
            <x-text-input id="no_hp" class="mt-2 block w-full" type="tel" inputmode="numeric" name="no_hp" :value="old('no_hp')" autocomplete="tel" placeholder="08xxxxxxxxxx" required />
            <x-input-error :messages="$errors->get('no_hp')" class="mt-2" />
        </div>

        @foreach (['pendidikan_terakhir' => ['Pendidikan terakhir', $pendidikan], 'asuransi' => ['Kepemilikan asuransi kesehatan', $asuransi]] as $name => [$label, $opsi])
            <div>
                <x-input-label for="{{ $name }}" :value="$label" />
                <select id="{{ $name }}" name="{{ $name }}" class="{{ $field }}" required>
                    <option value="">— Pilih —</option>
                    @foreach ($opsi as $value)
                        <option value="{{ $value }}" @selected(old($name) === $value)>{{ $value }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get($name)" class="mt-2" />
            </div>
        @endforeach

        <div>
            <x-input-label for="pekerjaan" value="Pekerjaan" />
            <x-text-input id="pekerjaan" class="mt-2 block w-full" type="text" name="pekerjaan" :value="old('pekerjaan')" required />
            <x-input-error :messages="$errors->get('pekerjaan')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="kecamatan" value="Kecamatan" />
            <x-text-input id="kecamatan" class="mt-2 block w-full" type="text" name="kecamatan" :value="old('kecamatan')" required />
            <x-input-error :messages="$errors->get('kecamatan')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="alamat" value="Alamat" />
            <textarea id="alamat" name="alamat" rows="2" class="{{ $field }}" autocomplete="street-address" required>{{ old('alamat') }}</textarea>
            <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
        </div>

        <h2 class="border-t border-krem-garis pt-8 text-xl font-extrabold">Identitas Anak</h2>

        <div>
            <x-input-label for="anak_nama_inisial" value="Nama anak (inisial)" />
            <x-text-input id="anak_nama_inisial" class="mt-2 block w-full" type="text" name="anak[nama_inisial]" :value="old('anak.nama_inisial')" required maxlength="50" />
            <x-input-error :messages="$errors->get('anak.nama_inisial')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="anak_tanggal_lahir" value="Tanggal lahir anak" />
            <x-text-input id="anak_tanggal_lahir" class="mt-2 block w-full" type="date" name="anak[tanggal_lahir]" :value="old('anak.tanggal_lahir')" required
                min="{{ now()->subMonthsNoOverflow(25)->addDay()->toDateString() }}" max="{{ now()->subMonthsNoOverflow(12)->toDateString() }}" />
            <p class="mt-2 text-sm text-ink-muted">Untuk anak usia 12–24 bulan.</p>
            <x-input-error :messages="$errors->get('anak.tanggal_lahir')" class="mt-2" />
        </div>

        <fieldset>
            <legend class="font-bold">Jenis kelamin anak</legend>
            <div class="mt-2 grid grid-cols-2 gap-3">
                @foreach (['L' => 'Laki-laki', 'P' => 'Perempuan'] as $value => $label)
                    <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-krem-garis px-4 py-3.5 has-[:checked]:border-brand has-[:checked]:bg-brand-soft">
                        <input type="radio" name="anak[jenis_kelamin]" value="{{ $value }}" class="h-5 w-5 border-krem-garis text-brand focus:ring-brand" @checked(old('anak.jenis_kelamin') === $value) required>
                        <span class="font-semibold">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
            <x-input-error :messages="$errors->get('anak.jenis_kelamin')" class="mt-2" />
        </fieldset>

        <fieldset>
            <legend class="font-bold">Kondisi saat lahir (boleh pilih lebih dari satu)</legend>
            <div class="mt-2 grid grid-cols-2 gap-3">
                @foreach (\App\Models\Anak::KONDISI_LAHIR as $value => $label)
                    <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-krem-garis px-4 py-3.5 has-[:checked]:border-brand has-[:checked]:bg-brand-soft">
                        <input type="checkbox" name="anak[kondisi_lahir][]" value="{{ $value }}" class="h-5 w-5 rounded border-krem-garis text-brand focus:ring-brand" @checked(in_array($value, old('anak.kondisi_lahir', []), true))>
                        <span class="font-semibold">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
            <x-input-error :messages="$errors->get('anak.kondisi_lahir')" class="mt-2" />
        </fieldset>

        <div>
            <x-input-label for="anak_jenis_persalinan" value="Jenis persalinan" />
            <select id="anak_jenis_persalinan" name="anak[jenis_persalinan]" class="{{ $field }}" required>
                <option value="">— Pilih —</option>
                @foreach (\App\Models\Anak::JENIS_PERSALINAN as $value => $label)
                    <option value="{{ $value }}" @selected(old('anak.jenis_persalinan') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('anak.jenis_persalinan')" class="mt-2" />
        </div>

        @foreach ([
            'bb_lahir_gram' => ['Berat lahir (gram)', '1', true],
            'pb_lahir_cm' => ['Panjang lahir (cm)', '0.1', true],
            'lingkar_kepala_cm' => ['Lingkar kepala saat lahir (cm, tidak wajib)', '0.1', false],
            'usia_gestasi_minggu' => ['Usia kehamilan saat lahir (minggu)', '1', true],
        ] as $name => [$label, $step, $wajib])
            <div>
                <x-input-label for="anak_{{ $name }}" :value="$label" />
                <x-text-input id="anak_{{ $name }}" class="mt-2 block w-full" type="number" inputmode="decimal" step="{{ $step }}" name="anak[{{ $name }}]" :value="old('anak.'.$name)" :required="$wajib" />
                <x-input-error :messages="$errors->get('anak.'.$name)" class="mt-2" />
            </div>
        @endforeach

        <h2 class="border-t border-krem-garis pt-8 text-xl font-extrabold">Riwayat Kehamilan & Kesehatan Ibu</h2>

        @include('profile.partials.riwayat-ibu', ['anak' => null])

        <h2 class="border-t border-krem-garis pt-8 text-xl font-extrabold">Buat Kata Sandi</h2>

        <div>
            <x-input-label for="password" value="Kata sandi" />
            <x-text-input id="password" class="mt-2 block w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Ulangi kata sandi" />
            <x-text-input id="password_confirmation" class="mt-2 block w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <x-primary-button class="w-full">Daftar</x-primary-button>
    </form>

    <p class="mt-6 text-center">
        <a href="{{ route('login') }}" class="font-bold text-brand hover:underline">Sudah punya akun? Masuk</a>
    </p>
</x-guest-layout>
