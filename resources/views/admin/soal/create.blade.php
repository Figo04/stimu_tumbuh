<x-admin-layout judul="Tambah Soal">
    <a href="{{ route('admin.soal.index') }}" class="font-bold text-brand hover:underline">&larr; Kelola Soal</a>

    <section class="mt-4 max-w-2xl rounded-3xl bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('admin.soal.store') }}" class="space-y-5">
            @csrf
            @include('admin.soal._form')

            <div class="flex items-center gap-4 border-t border-krem-garis pt-5">
                <x-primary-button>Simpan soal</x-primary-button>
                <a href="{{ route('admin.soal.index') }}" class="font-bold text-ink-muted hover:text-ink">Batal</a>
            </div>
        </form>
    </section>
</x-admin-layout>
