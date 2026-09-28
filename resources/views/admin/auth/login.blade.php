<x-guest-layout>
    <p class="text-sm font-bold uppercase tracking-wide text-brand">Area Peneliti</p>
    <h1 class="mt-1 text-3xl font-extrabold">Masuk Admin</h1>

    <form method="POST" action="{{ route('admin.login') }}" class="mt-6 space-y-5">
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

        <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-ink-muted">
            <input id="remember_me" type="checkbox" name="remember" class="h-5 w-5 rounded border-krem-garis text-brand focus:ring-brand">
            Ingat saya
        </label>

        <x-primary-button class="w-full">Masuk</x-primary-button>
    </form>
</x-guest-layout>
