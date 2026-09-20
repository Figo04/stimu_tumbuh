<x-admin-layout judul="Ubah Soal">
    <a href="{{ route('admin.soal.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&lsaquo; Kelola Soal</a>

    <section class="mt-2 max-w-2xl rounded-lg border border-gray-200 bg-white p-6">
        @if ($soal->detail_count)
            <p class="mb-5 rounded-md bg-amber-50 p-4 text-sm text-amber-800">
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

            <div class="flex items-center gap-3 border-t border-gray-200 pt-5">
                <button type="submit" class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                    Simpan perubahan
                </button>
                <a href="{{ route('admin.soal.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Batal</a>
            </div>
        </form>
    </section>
</x-admin-layout>
