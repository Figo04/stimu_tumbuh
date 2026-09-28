<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profil') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Keluar pindah ke sini dari dropdown navigasi lama (mockup: bawah halaman Profil). --}}
            <form method="POST" action="{{ route('logout') }}" class="px-4 sm:px-0">
                @csrf
                <button type="submit" class="w-full rounded-2xl border border-krem-garis bg-white py-4 font-bold text-ink-muted hover:text-ink">
                    Keluar
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
