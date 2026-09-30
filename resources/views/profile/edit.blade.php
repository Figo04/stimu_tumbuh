<x-app-layout>
    @php
        $anak = $user->anak;
        if ($anak) {
            $kata = preg_split('/\s+/', trim($anak->nama_inisial));
            $inisial = mb_strtoupper(count($kata) > 1 ? mb_substr($kata[0], 0, 1).mb_substr($kata[1], 0, 1) : mb_substr($kata[0], 0, 2));
        }
    @endphp

    <div class="mx-auto max-w-2xl space-y-5 px-4 py-6">
        <h1 class="text-3xl font-extrabold">Profil Keluarga</h1>

        @if (session('status') === 'profile-updated')
            <x-auth-session-status status="✓ Profil tersimpan." />
        @endif

        @if ($anak)
            <section class="flex items-center gap-5 rounded-3xl bg-brand p-6 text-white shadow-lg shadow-brand/20">
                <span class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-white/20 text-xl font-extrabold" aria-hidden="true">{{ $inisial }}</span>
                <div class="min-w-0">
                    <p class="truncate text-2xl font-extrabold">{{ $anak->nama_inisial }}</p>
                    <p class="text-lg">{{ \App\Services\UsiaAnakService::usiaBulan($anak->tanggal_lahir) }} bulan · {{ $anak->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                    <p class="text-white/90">Lahir {{ $anak->tanggal_lahir->translatedFormat('j F Y') }}</p>
                </div>
            </section>
        @endif

        @include('profile.partials.update-profile-information-form')

        <section class="rounded-3xl bg-white p-6 shadow-sm">
            <h2 class="text-sm font-bold text-ink-muted">Kode Responden</h2>
            <div class="mt-3 flex items-center justify-between gap-4 rounded-2xl bg-krem-tua px-4 py-3" x-data="{ tersalin: false }">
                <span class="select-all text-lg font-extrabold">{{ $user->kode_responden }}</span>
                {{-- Clipboard API hanya ada di HTTPS/localhost; tanpa itu kode tetap bisa diseleksi manual. --}}
                <button type="button" x-show="navigator.clipboard" x-cloak aria-label="Salin kode responden"
                        x-on:click="navigator.clipboard.writeText(@js($user->kode_responden)).then(() => { tersalin = true; setTimeout(() => tersalin = false, 2000) })"
                        class="flex items-center gap-1 rounded-xl p-2 text-sm font-bold text-ink hover:bg-krem-garis">
                    <span x-show="tersalin" class="text-sukses">Tersalin</span>
                    <svg x-show="! tersalin" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="14" height="14" x="8" y="8" rx="2" /><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2" /></svg>
                </button>
            </div>
        </section>

        @if (config('services.wa_konsultasi'))
            <section class="rounded-3xl bg-brand-soft p-6">
                <h2 class="text-lg font-extrabold">Perlu bantuan?</h2>
                <p class="mt-1 text-ink-muted">Tim peneliti membalas pesan pada jam kerja.</p>
                <x-nav-konsultasi class="mt-4 flex w-full items-center justify-center gap-2 rounded-2xl bg-brand px-6 py-4 text-base font-bold text-white shadow-lg shadow-brand/20 hover:bg-brand/90">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" /><circle cx="12" cy="7" r="4" /></svg>
                    Tanya ke Tim Peneliti
                </x-nav-konsultasi>
            </section>
        @endif

        @include('profile.partials.update-password-form')

        {{-- Keluar pindah ke sini dari dropdown navigasi lama (mockup: bawah halaman Profil). --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full rounded-2xl border border-krem-garis bg-white py-4 font-bold text-ink-muted hover:text-ink">
                Keluar
            </button>
        </form>
    </div>
</x-app-layout>
