<x-guest-layout>
    <h1 class="text-3xl font-extrabold">Periksa email Anda</h1>
    <p class="mt-2 text-lg text-ink-muted">Terima kasih sudah mendaftar! Klik tautan yang kami kirim ke email Anda untuk memastikan alamatnya benar. Belum menerima? Kami bisa mengirim ulang.</p>

    @if (session('status') == 'verification-link-sent')
        <x-auth-session-status class="mt-6" status="Tautan baru sudah dikirim ke email yang Anda daftarkan." />
    @endif

    <form method="POST" action="{{ route('verification.send') }}" class="mt-6">
        @csrf
        <x-primary-button class="w-full">Kirim Ulang Email</x-primary-button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-4">
        @csrf
        <button type="submit" class="w-full rounded-2xl border border-krem-garis bg-white py-4 font-bold text-ink-muted hover:text-ink">Keluar</button>
    </form>
</x-guest-layout>
