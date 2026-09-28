<x-app-layout>
    <div class="mx-auto max-w-2xl px-4 py-6">
        <a href="{{ route('aktivitas.index') }}" class="inline-flex items-center gap-1 font-bold text-brand hover:underline">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6" /></svg>
            Kalender Stimulasi
        </a>
        <h1 class="mt-3 text-3xl font-extrabold">Ubah catatan stimulasi</h1>

        <section class="mt-6 rounded-3xl bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('aktivitas.update', $entri) }}" class="space-y-5">
                @csrf
                @method('PUT')
                @include('aktivitas._form')
                <x-primary-button class="w-full">Simpan perubahan</x-primary-button>
            </form>
        </section>
    </div>
</x-app-layout>
