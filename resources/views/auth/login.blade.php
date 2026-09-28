<x-guest-layout>
    <h1 class="text-3xl font-extrabold">Selamat datang kembali</h1>
    <p class="mt-2 text-lg text-ink-muted">Masuk untuk melanjutkan kegiatan tumbuh kembang Ananda.</p>

    <x-auth-session-status class="mt-6" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Kata sandi" />
            <x-text-input id="password" class="mt-2 block w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between gap-4 text-sm">
            <label for="remember_me" class="inline-flex items-center gap-2 text-ink-muted">
                <input id="remember_me" type="checkbox" name="remember" class="h-5 w-5 rounded border-krem-garis text-brand focus:ring-brand">
                Ingat saya
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="font-bold text-brand hover:underline">Lupa kata sandi?</a>
            @endif
        </div>

        <x-primary-button class="w-full">Masuk</x-primary-button>
    </form>

    <p class="mt-6 text-center">
        <a href="{{ route('register') }}" class="font-bold text-brand hover:underline">Belum punya akun? Daftar</a>
    </p>
</x-guest-layout>
