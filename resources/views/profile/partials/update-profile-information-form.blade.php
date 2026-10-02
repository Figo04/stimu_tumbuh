@php
    $field = 'mt-2 block w-full rounded-2xl border-krem-garis px-4 py-3.5 text-base text-ink focus:border-brand focus:ring-brand';
    $anak = $user->anak;
    $tglLahir = $anak?->tanggal_lahir?->toDateString();
    // Petunjuk browser saja (aturan tepatnya di backend): usia 12–24 bulan, atau tanggal lahir tersimpan.
    $minLahir = min(array_filter([now()->subMonthsNoOverflow(25)->addDay()->toDateString(), $tglLahir]));
    $maksLahir = max(array_filter([now()->subMonthsNoOverflow(12)->toDateString(), $tglLahir]));
    $angka = fn ($v, $satuan) => filled($v) ? str_replace('.', ',', $v + 0).' '.$satuan : '—';
    $dataAnak = [
        'Usia kehamilan' => $angka($anak?->usia_gestasi_minggu, 'minggu'),
        'Jenis persalinan' => \App\Models\Anak::JENIS_PERSALINAN[$anak?->jenis_persalinan] ?? '—',
        'Berat lahir' => filled($anak?->bb_lahir_gram) ? number_format($anak->bb_lahir_gram, 0, ',', '.').' gram' : '—',
        'Panjang lahir' => $angka($anak?->pb_lahir_cm, 'cm'),
        'Lingkar kepala' => $angka($anak?->lingkar_kepala_cm, 'cm'),
        'Kondisi saat lahir' => \App\Models\Anak::KONDISI_LAHIR[$anak?->kondisi_lahir] ?? '—',
    ];
    $dataOrtu = [
        'Nama' => $user->nama,
        'Hubungan' => ucfirst($user->hubungan_dengan_anak),
        'Email' => $user->email,
        'Nomor HP' => $user->no_hp,
        'Pendidikan' => $user->pendidikan_terakhir,
        'Pekerjaan' => $user->pekerjaan,
        'Kecamatan' => $user->kecamatan,
        'Alamat' => $user->alamat,
    ];
    $galatOrtu = $errors->hasAny(['nama', 'hubungan_dengan_anak', 'email', 'no_hp', 'pendidikan_terakhir', 'pekerjaan', 'kecamatan', 'alamat']);
@endphp

{{-- Satu form untuk dua kartu (ProfileUpdateRequest memvalidasi orang tua + anak sekaligus).
     Kartu yang tidak sedang diubah tetap mengirim nilai tersimpannya lewat input tersembunyi (x-show). --}}
<form method="post" action="{{ route('profile.update') }}" class="space-y-5"
      x-data="{ ubahAnak: @js(! $anak || $errors->has('anak.*')), ubahOrtu: @js($galatOrtu) }">
    @csrf
    @method('patch')

    <section class="rounded-3xl bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-xl font-extrabold">Data Anak</h2>
            <button type="button" x-show="! ubahAnak" x-on:click="ubahAnak = true" class="rounded-xl px-3 py-2 font-bold hover:bg-krem">Ubah</button>
            {{-- Batal = muat ulang, supaya isian yang dibatalkan tidak ikut tersimpan saat kartu lain disimpan. --}}
            <a href="{{ route('profile.edit') }}" x-show="ubahAnak" x-cloak class="rounded-xl px-3 py-2 font-bold text-ink-muted hover:bg-krem">Batal</a>
        </div>

        <dl x-show="! ubahAnak" class="mt-2">
            @foreach ($dataAnak as $label => $nilai)
                <div class="border-b border-krem-garis py-3 last:border-0 last:pb-0">
                    <dt class="text-sm text-ink-muted">{{ $label }}</dt>
                    <dd class="text-lg font-bold">{{ $nilai }}</dd>
                </div>
            @endforeach
        </dl>

        <div x-show="ubahAnak" x-cloak class="mt-4 space-y-5">
            <div>
                <x-input-label for="anak_nama_inisial" value="Nama anak (inisial)" />
                <x-text-input id="anak_nama_inisial" name="anak[nama_inisial]" type="text" class="mt-2 block w-full" :value="old('anak.nama_inisial', $anak?->nama_inisial)" required maxlength="50" />
                <x-input-error class="mt-2" :messages="$errors->get('anak.nama_inisial')" />
            </div>

            <div>
                <x-input-label for="anak_tanggal_lahir" value="Tanggal lahir anak" />
                <x-text-input id="anak_tanggal_lahir" name="anak[tanggal_lahir]" type="date" class="mt-2 block w-full" :value="old('anak.tanggal_lahir', $tglLahir)" required
                    min="{{ $minLahir }}" max="{{ $maksLahir }}" />
                <p class="mt-2 rounded-2xl bg-hangat-bg px-4 py-3 text-sm">Mengubah tanggal lahir dapat mengubah kelompok usia anak, sehingga materi dan checklist perkembangan yang tampil ikut berganti.</p>
                <x-input-error class="mt-2" :messages="$errors->get('anak.tanggal_lahir')" />
            </div>

            <fieldset>
                <legend class="font-bold text-ink">Jenis kelamin anak</legend>
                <div class="mt-2 grid grid-cols-2 gap-3">
                    @foreach (['L' => 'Laki-laki', 'P' => 'Perempuan'] as $value => $label)
                        <label class="flex cursor-pointer items-center justify-center gap-2 rounded-2xl border border-krem-garis px-4 py-3.5 font-bold has-[:checked]:border-brand has-[:checked]:bg-brand-soft has-[:checked]:text-brand">
                            <input type="radio" name="anak[jenis_kelamin]" value="{{ $value }}" class="h-5 w-5 border-krem-garis text-brand focus:ring-brand" @checked(old('anak.jenis_kelamin', $anak?->jenis_kelamin) === $value) required>
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('anak.jenis_kelamin')" />
            </fieldset>

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
                    <x-text-input id="anak_{{ $name }}" name="anak[{{ $name }}]" type="number" inputmode="decimal" step="{{ $step }}" class="mt-2 block w-full" :value="old('anak.'.$name, $anak?->$name)" />
                    <x-input-error class="mt-2" :messages="$errors->get('anak.'.$name)" />
                </div>
            @endforeach

            <x-primary-button class="w-full">Simpan</x-primary-button>
        </div>
    </section>

    <section class="rounded-3xl bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-xl font-extrabold">Data Orang Tua</h2>
            <button type="button" x-show="! ubahOrtu" x-on:click="ubahOrtu = true" class="rounded-xl px-3 py-2 font-bold hover:bg-krem">Ubah</button>
            <a href="{{ route('profile.edit') }}" x-show="ubahOrtu" x-cloak class="rounded-xl px-3 py-2 font-bold text-ink-muted hover:bg-krem">Batal</a>
        </div>

        <dl x-show="! ubahOrtu" class="mt-2">
            @foreach ($dataOrtu as $label => $nilai)
                <div class="border-b border-krem-garis py-3 last:border-0 last:pb-0">
                    <dt class="text-sm text-ink-muted">{{ $label }}</dt>
                    <dd class="break-words text-lg font-bold">{{ filled($nilai) ? $nilai : '—' }}</dd>
                </div>
            @endforeach
        </dl>

        <div x-show="ubahOrtu" x-cloak class="mt-4 space-y-5">
            <div>
                <x-input-label for="nama" value="Nama (boleh inisial)" />
                <x-text-input id="nama" name="nama" type="text" class="mt-2 block w-full" :value="old('nama', $user->nama)" required autocomplete="name" />
                <x-input-error class="mt-2" :messages="$errors->get('nama')" />
            </div>

            <div>
                <x-input-label for="hubungan_dengan_anak" value="Hubungan dengan anak" />
                <select id="hubungan_dengan_anak" name="hubungan_dengan_anak" class="{{ $field }}" required>
                    @foreach (['ibu' => 'Ibu', 'ayah' => 'Ayah', 'pengasuh' => 'Pengasuh'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('hubungan_dengan_anak', $user->hubungan_dengan_anak) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('hubungan_dengan_anak')" />
            </div>

            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" name="email" type="email" class="mt-2 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />
            </div>

            <div>
                <x-input-label for="no_hp" value="Nomor HP / WhatsApp (tidak wajib)" />
                <x-text-input id="no_hp" name="no_hp" type="tel" inputmode="numeric" class="mt-2 block w-full" :value="old('no_hp', $user->no_hp)" autocomplete="tel" placeholder="08xxxxxxxxxx" />
                <x-input-error class="mt-2" :messages="$errors->get('no_hp')" />
            </div>

            <div>
                <x-input-label for="pendidikan_terakhir" value="Pendidikan terakhir (tidak wajib)" />
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
                    <x-text-input id="{{ $name }}" name="{{ $name }}" type="text" class="mt-2 block w-full" :value="old($name, $user->$name)" />
                    <x-input-error class="mt-2" :messages="$errors->get($name)" />
                </div>
            @endforeach

            <div>
                <x-input-label for="alamat" value="Alamat (tidak wajib)" />
                <textarea id="alamat" name="alamat" rows="2" class="{{ $field }}" autocomplete="street-address">{{ old('alamat', $user->alamat) }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('alamat')" />
            </div>

            <x-primary-button class="w-full">Simpan</x-primary-button>
        </div>
    </section>
</form>
