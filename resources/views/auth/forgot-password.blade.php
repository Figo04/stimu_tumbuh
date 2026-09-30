<x-guest-layout>
    <h1 class="text-3xl font-extrabold">Lupa kata sandi?</h1>
    <p class="mt-2 text-lg text-ink-muted">Tulis email yang Anda pakai saat mendaftar. Kami kirimkan tautan untuk membuat kata sandi baru.</p>

    <x-auth-session-status class="mt-6" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-5">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <x-primary-button class="w-full">Kirim Tautan</x-primary-button>
    </form>

    <p class="mt-6 text-center">
        <a href="{{ route('login') }}" class="font-bold text-brand hover:underline">Kembali ke halaman masuk</a>
    </p>
</x-guest-layout>
