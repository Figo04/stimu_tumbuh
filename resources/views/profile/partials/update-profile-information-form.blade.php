@php
    $field = 'block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm';
    $anak = $user->anak;
    $tglLahir = $anak?->tanggal_lahir?->toDateString();
    // Sama dengan aturan backend: 36 bulan, atau tanggal lahir tersimpan bila anak sudah lebih tua.
    $minLahir = min(array_filter([now()->subMonths(36)->toDateString(), $tglLahir]));
@endphp

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Data Orang Tua & Anak') }}
        </h2>

        <dl class="mt-2 text-sm text-gray-600 space-y-1">
            <div><dt class="inline">Kode responden:</dt> <dd class="inline font-medium text-gray-900">{{ $user->kode_responden }}</dd></div>
            @if ($anak)
                <div><dt class="inline">Usia anak saat ini:</dt> <dd class="inline font-medium text-gray-900">{{ \App\Services\UsiaAnakService::usiaBulan($anak->tanggal_lahir) }} bulan (kelompok {{ $anak->kelompok_usia }} bulan)</dd></div>
            @endif
        </dl>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="nama" :value="__('Nama (boleh inisial)')" />
            <x-text-input id="nama" name="nama" type="text" class="mt-1 block w-full" :value="old('nama', $user->nama)" required autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('nama')" />
        </div>

        <div>
            <x-input-label for="hubungan_dengan_anak" :value="__('Hubungan dengan anak')" />
            <select id="hubungan_dengan_anak" name="hubungan_dengan_anak" class="{{ $field }}" required>
                @foreach (['ibu' => 'Ibu', 'ayah' => 'Ayah', 'pengasuh' => 'Pengasuh'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('hubungan_dengan_anak', $user->hubungan_dengan_anak) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('hubungan_dengan_anak')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div>
            <x-input-label for="no_hp" :value="__('Nomor HP / WhatsApp (tidak wajib)')" />
            <x-text-input id="no_hp" name="no_hp" type="tel" inputmode="numeric" class="mt-1 block w-full" :value="old('no_hp', $user->no_hp)" autocomplete="tel" placeholder="08xxxxxxxxxx" />
            <x-input-error class="mt-2" :messages="$errors->get('no_hp')" />
        </div>

        <div>
            <x-input-label for="pendidikan_terakhir" :value="__('Pendidikan terakhir (tidak wajib)')" />
            <select id="pendidikan_terakhir" name="pendidikan_terakhir" class="{{ $field }}">
                <option value="">— Pilih —</option>
                @foreach ($pendidikan as $value)
                    <option value="{{ $value }}" @selected(old('pendidikan_terakhir', $user->pendidikan_terakhir) === $value)>{{ $value }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('pendidikan_terakhir')" />
        </div>

        @foreach (['pekerjaan' => 'Pekerjaan (tidak wajib)', 'kecamatan' => 'Kecamatan (tidak wajib)'] as $name => $label)
            <div>
                <x-input-label for="{{ $name }}" :value="$label" />
                <x-text-input id="{{ $name }}" name="{{ $name }}" type="text" class="mt-1 block w-full" :value="old($name, $user->$name)" />
                <x-input-error class="mt-2" :messages="$errors->get($name)" />
            </div>
        @endforeach

        <div>
            <x-input-label for="alamat" :value="__('Alamat (tidak wajib)')" />
            <textarea id="alamat" name="alamat" rows="2" class="{{ $field }}" autocomplete="street-address">{{ old('alamat', $user->alamat) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('alamat')" />
        </div>

        <h3 class="pt-4 text-lg font-medium text-gray-900">{{ __('Identitas Anak') }}</h3>

        <div>
            <x-input-label for="anak_nama_inisial" :value="__('Nama anak (inisial)')" />
            <x-text-input id="anak_nama_inisial" name="anak[nama_inisial]" type="text" class="mt-1 block w-full" :value="old('anak.nama_inisial', $anak?->nama_inisial)" required maxlength="50" />
            <x-input-error class="mt-2" :messages="$errors->get('anak.nama_inisial')" />
        </div>

        <div>
            <x-input-label for="anak_tanggal_lahir" :value="__('Tanggal lahir anak')" />
            <x-text-input id="anak_tanggal_lahir" name="anak[tanggal_lahir]" type="date" class="mt-1 block w-full" :value="old('anak.tanggal_lahir', $tglLahir)" required
                min="{{ $minLahir }}" max="{{ now()->toDateString() }}" />
            <p class="mt-1 text-sm text-amber-700">Mengubah tanggal lahir dapat mengubah kelompok usia anak, sehingga materi dan checklist perkembangan yang tampil ikut berganti.</p>
            <x-input-error class="mt-2" :messages="$errors->get('anak.tanggal_lahir')" />
        </div>

        <div>
            <x-input-label :value="__('Jenis kelamin anak')" />
            <div class="mt-2 flex gap-6">
                @foreach (['L' => 'Laki-laki', 'P' => 'Perempuan'] as $value => $label)
                    <label class="inline-flex items-center gap-2">
                        <input type="radio" name="anak[jenis_kelamin]" value="{{ $value }}" class="text-indigo-600 focus:ring-indigo-500" @checked(old('anak.jenis_kelamin', $anak?->jenis_kelamin) === $value) required>
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('anak.jenis_kelamin')" />
        </div>

        @foreach ([
            'kondisi_lahir' => ['Kondisi saat lahir (tidak wajib)', \App\Models\Anak::KONDISI_LAHIR],
            'jenis_persalinan' => ['Jenis persalinan (tidak wajib)', \App\Models\Anak::JENIS_PERSALINAN],
        ] as $name => [$label, $opsi])
            <div>
                <x-input-label for="anak_{{ $name }}" :value="$label" />
                <select id="anak_{{ $name }}" name="anak[{{ $name }}]" class="{{ $field }}">
                    <option value="">— Pilih —</option>
                    @foreach ($opsi as $value => $text)
                        <option value="{{ $value }}" @selected(old('anak.'.$name, $anak?->$name) === $value)>{{ $text }}</option>
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('anak.'.$name)" />
            </div>
        @endforeach

        @foreach ([
            'bb_lahir_gram' => ['Berat lahir (gram, tidak wajib)', '1'],
            'pb_lahir_cm' => ['Panjang lahir (cm, tidak wajib)', '0.1'],
            'lingkar_kepala_cm' => ['Lingkar kepala saat lahir (cm, tidak wajib)', '0.1'],
            'usia_gestasi_minggu' => ['Usia kehamilan saat lahir (minggu, tidak wajib)', '1'],
        ] as $name => [$label, $step])
            <div>
                <x-input-label for="anak_{{ $name }}" :value="$label" />
                <x-text-input id="anak_{{ $name }}" name="anak[{{ $name }}]" type="number" inputmode="decimal" step="{{ $step }}" class="mt-1 block w-full" :value="old('anak.'.$name, $anak?->$name)" />
                <x-input-error class="mt-2" :messages="$errors->get('anak.'.$name)" />
            </div>
        @endforeach

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Simpan') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Tersimpan.') }}</p>
            @endif
        </div>
    </form>
</section>
