<x-guest-layout>
    @php($field = 'block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm')

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="nama" :value="__('Nama (boleh inisial)')" />
            <x-text-input id="nama" class="block mt-1 w-full" type="text" name="nama" :value="old('nama')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('nama')" class="mt-2" />
        </div>

        <!-- Hubungan dengan anak -->
        <div class="mt-4">
            <x-input-label for="hubungan_dengan_anak" :value="__('Hubungan dengan anak')" />
            <select id="hubungan_dengan_anak" name="hubungan_dengan_anak" class="{{ $field }}" required>
                @foreach (['ibu' => 'Ibu', 'ayah' => 'Ayah', 'pengasuh' => 'Pengasuh'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('hubungan_dengan_anak', 'ibu') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('hubungan_dengan_anak')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- No HP -->
        <div class="mt-4">
            <x-input-label for="no_hp" :value="__('Nomor HP / WhatsApp (tidak wajib)')" />
            <x-text-input id="no_hp" class="block mt-1 w-full" type="tel" inputmode="numeric" name="no_hp" :value="old('no_hp')" autocomplete="tel" placeholder="08xxxxxxxxxx" />
            <x-input-error :messages="$errors->get('no_hp')" class="mt-2" />
        </div>

        <!-- Pendidikan terakhir -->
        <div class="mt-4">
            <x-input-label for="pendidikan_terakhir" :value="__('Pendidikan terakhir (tidak wajib)')" />
            <select id="pendidikan_terakhir" name="pendidikan_terakhir" class="{{ $field }}">
                <option value="">— Pilih —</option>
                @foreach ($pendidikan as $value)
                    <option value="{{ $value }}" @selected(old('pendidikan_terakhir') === $value)>{{ $value }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('pendidikan_terakhir')" class="mt-2" />
        </div>

        <!-- Pekerjaan -->
        <div class="mt-4">
            <x-input-label for="pekerjaan" :value="__('Pekerjaan (tidak wajib)')" />
            <x-text-input id="pekerjaan" class="block mt-1 w-full" type="text" name="pekerjaan" :value="old('pekerjaan')" />
            <x-input-error :messages="$errors->get('pekerjaan')" class="mt-2" />
        </div>

        <!-- Kecamatan -->
        <div class="mt-4">
            <x-input-label for="kecamatan" :value="__('Kecamatan (tidak wajib)')" />
            <x-text-input id="kecamatan" class="block mt-1 w-full" type="text" name="kecamatan" :value="old('kecamatan')" />
            <x-input-error :messages="$errors->get('kecamatan')" class="mt-2" />
        </div>

        <!-- Alamat -->
        <div class="mt-4">
            <x-input-label for="alamat" :value="__('Alamat (tidak wajib)')" />
            <textarea id="alamat" name="alamat" rows="2" class="{{ $field }}" autocomplete="street-address">{{ old('alamat') }}</textarea>
            <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
