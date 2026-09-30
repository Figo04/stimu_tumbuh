<x-guest-layout>
    <h1 class="text-3xl font-extrabold">Konfirmasi kata sandi</h1>
    <p class="mt-2 text-lg text-ink-muted">Demi keamanan, masukkan kata sandi Anda sekali lagi sebelum melanjutkan.</p>

    <form method="POST" action="{{ route('password.confirm') }}" class="mt-6 space-y-5">
        @csrf

        <div>
            <x-input-label for="password" value="Kata sandi" />
            <x-text-input id="password" class="mt-2 block w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <x-primary-button class="w-full">Lanjutkan</x-primary-button>
    </form>
</x-guest-layout>
