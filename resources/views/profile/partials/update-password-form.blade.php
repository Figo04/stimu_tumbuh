{{-- Tidak ada di mockup; dipertahankan (fitur Breeze yang sudah jalan) sebagai kartu lipat. --}}
<details class="group rounded-3xl bg-white shadow-sm" @if ($errors->updatePassword->isNotEmpty() || session('status') === 'password-updated') open @endif>
    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 p-6">
        <span class="text-xl font-extrabold">Ganti Kata Sandi</span>
        <svg class="h-5 w-5 shrink-0 transition group-open:rotate-90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6" /></svg>
    </summary>

    <form method="post" action="{{ route('password.update') }}" class="space-y-5 border-t border-krem-garis px-6 pb-6 pt-5">
        @csrf
        @method('put')

        @if (session('status') === 'password-updated')
            <x-auth-session-status status="✓ Kata sandi tersimpan." />
        @endif

        <div>
            <x-input-label for="update_password_current_password" value="Kata sandi saat ini" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-2 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" value="Kata sandi baru" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-2 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" value="Ulangi kata sandi baru" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-2 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <x-primary-button class="w-full">Simpan Kata Sandi</x-primary-button>
    </form>
</details>
