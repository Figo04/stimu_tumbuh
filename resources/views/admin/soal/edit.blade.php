<x-admin-layout judul="Ubah Soal">
    <a href="{{ route('admin.soal.index') }}" class="font-bold text-brand hover:underline">&larr; Kelola Soal</a>

    <section class="mt-4 max-w-2xl rounded-3xl bg-white p-6 shadow-sm">
        @if ($soal->detail_count)
            <p class="mb-5 rounded-2xl bg-hangat-bg px-4 py-3 text-sm">
                Soal ini sudah dijawab <strong>{{ $soal->detail_count }} responden</strong>. Jawaban dan skor yang
                terlanjur tersimpan <strong>tidak dihitung ulang</strong> setelah soal diubah — aman untuk
                memperbaiki ejaan, tapi mengubah makna pertanyaan atau jawaban benarnya membuat hasil lama
                tidak lagi sebanding dengan hasil baru.
            </p>
        @endif

        <form method="POST" action="{{ route('admin.soal.update', $soal) }}" class="space-y-5">
            @csrf
            @method('PUT')
            @include('admin.soal._form')

            <div class="flex items-center gap-4 border-t border-krem-garis pt-5">
                <x-primary-button>Simpan perubahan</x-primary-button>
                <a href="{{ route('admin.soal.index') }}" class="font-bold text-ink-muted hover:text-ink">Batal</a>
            </div>
        </form>
    </section>
</x-admin-layout>
