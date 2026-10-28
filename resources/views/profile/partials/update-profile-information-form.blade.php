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
        'Kondisi saat lahir' => \App\Models\Anak::labelGanda(\App\Models\Anak::KONDISI_LAHIR, $anak?->kondisi_lahir) ?? '—',
        ...array_map(fn ($v) => $v ?? '—', $anak?->riwayatIbu() ?? []),
    ];
    $dataOrtu = [
        'Nama' => $user->nama,
        'Hubungan' => ucfirst($user->hubungan_dengan_anak),
        'Umur' => filled($user->umur) ? $user->umur.' tahun' : null,
        'Email' => $user->email,
        'Nomor HP' => $user->no_hp,
        'Pendidikan' => $user->pendidikan_terakhir,
        'Pekerjaan' => $user->pekerjaan,
        'Asuransi' => $user->asuransi,
        'Kecamatan' => $user->kecamatan,
        'Alamat' => $user->alamat,
    ];
    $galatOrtu = $errors->hasAny(['nama', 'hubungan_dengan_anak', 'umur', 'email', 'no_hp', 'pendidikan_terakhir', 'pekerjaan', 'asuransi', 'kecamatan', 'alamat']);
@endphp

{{-- Satu form untuk dua kartu (ProfileUpdateRequest memvalidasi orang tua + anak sekaligus).
     Kartu yang tidak sedang diubah tetap mengirim nilai tersimpannya lewat input tersembunyi (x-show).
     Akun lama yang belum punya data primer (umur, riwayat ibu) langsung dibuka agar kolom wajibnya terlihat. --}}
<form method="post" action="{{ route('profile.update') }}" class="space-y-5"
      x-data="{ ubahAnak: @js(! $anak || $anak->anemia_hamil === null || $errors->has('anak.*')), ubahOrtu: @js($galatOrtu || blank($user->umur)) }">
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

            <fieldset>
                <legend class="font-bold text-ink">Kondisi saat lahir (boleh pilih lebih dari satu)</legend>
                <div class="mt-2 grid grid-cols-2 gap-3">
                    @foreach (\App\Models\Anak::KONDISI_LAHIR as $value => $label)
                        <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-krem-garis px-4 py-3.5 has-[:checked]:border-brand has-[:checked]:bg-brand-soft">
                            <input type="checkbox" name="anak[kondisi_lahir][]" value="{{ $value }}" class="h-5 w-5 rounded border-krem-garis text-brand focus:ring-brand" @checked(in_array($value, old('anak.kondisi_lahir', $anak?->kondisi_lahir ?? []), true))>
                            <span class="font-semibold">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('anak.kondisi_lahir')" />
            </fieldset>

            <div>
                <x-input-label for="anak_jenis_persalinan" value="Jenis persalinan" />
                <select id="anak_jenis_persalinan" name="anak[jenis_persalinan]" class="{{ $field }}" required>
                    <option value="">— Pilih —</option>
                    @foreach (\App\Models\Anak::JENIS_PERSALINAN as $value => $text)
                        <option value="{{ $value }}" @selected(old('anak.jenis_persalinan', $anak?->jenis_persalinan) === $value)>{{ $text }}</option>
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('anak.jenis_persalinan')" />
            </div>

            @foreach ([
                'bb_lahir_gram' => ['Berat lahir (gram)', '1', true],
                'pb_lahir_cm' => ['Panjang lahir (cm)', '0.1', true],
                'lingkar_kepala_cm' => ['Lingkar kepala saat lahir (cm, tidak wajib)', '0.1', false],
                'usia_gestasi_minggu' => ['Usia kehamilan saat lahir (minggu)', '1', true],
            ] as $name => [$label, $step, $wajib])
                <div>
                    <x-input-label for="anak_{{ $name }}" :value="$label" />
                    <x-text-input id="anak_{{ $name }}" name="anak[{{ $name }}]" type="number" inputmode="decimal" step="{{ $step }}" class="mt-2 block w-full" :value="old('anak.'.$name, $anak?->$name)" :required="$wajib" />
                    <x-input-error class="mt-2" :messages="$errors->get('anak.'.$name)" />
                </div>
            @endforeach

            <h3 class="border-t border-krem-garis pt-5 text-lg font-extrabold">Riwayat Kehamilan & Kesehatan Ibu</h3>

            @include('profile.partials.riwayat-ibu')

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
                <x-input-label for="umur" value="Umur (tahun)" />
                <x-text-input id="umur" name="umur" type="number" inputmode="numeric" min="15" max="70" class="mt-2 block w-full" :value="old('umur', $user->umur)" required />
                <x-input-error class="mt-2" :messages="$errors->get('umur')" />
            </div>

            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" name="email" type="email" class="mt-2 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />
            </div>

            <div>
                <x-input-label for="no_hp" value="Nomor HP / WhatsApp" />
                <x-text-input id="no_hp" name="no_hp" type="tel" inputmode="numeric" class="mt-2 block w-full" :value="old('no_hp', $user->no_hp)" autocomplete="tel" placeholder="08xxxxxxxxxx" required />
                <x-input-error class="mt-2" :messages="$errors->get('no_hp')" />
            </div>

            @foreach (['pendidikan_terakhir' => ['Pendidikan terakhir', $pendidikan], 'asuransi' => ['Kepemilikan asuransi kesehatan', $asuransi]] as $name => [$label, $opsi])
                <div>
                    <x-input-label for="{{ $name }}" :value="$label" />
                    <select id="{{ $name }}" name="{{ $name }}" class="{{ $field }}" required>
                        <option value="">— Pilih —</option>
                        @foreach ($opsi as $value)
                            <option value="{{ $value }}" @selected(old($name, $user->$name) === $value)>{{ $value }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get($name)" />
                </div>
            @endforeach

            @foreach (['pekerjaan' => 'Pekerjaan', 'kecamatan' => 'Kecamatan'] as $name => $label)
                <div>
                    <x-input-label for="{{ $name }}" :value="$label" />
                    <x-text-input id="{{ $name }}" name="{{ $name }}" type="text" class="mt-2 block w-full" :value="old($name, $user->$name)" required />
                    <x-input-error class="mt-2" :messages="$errors->get($name)" />
                </div>
            @endforeach

            <div>
                <x-input-label for="alamat" value="Alamat" />
                <textarea id="alamat" name="alamat" rows="2" class="{{ $field }}" autocomplete="street-address" required>{{ old('alamat', $user->alamat) }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('alamat')" />
            </div>

            <x-primary-button class="w-full">Simpan</x-primary-button>
        </div>
    </section>
</form>
