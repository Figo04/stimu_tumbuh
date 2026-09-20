<x-admin-layout judul="Tambah Soal">
    <a href="{{ route('admin.soal.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&lsaquo; Kelola Soal</a>

    <section class="mt-2 max-w-2xl rounded-lg border border-gray-200 bg-white p-6">
        <form method="POST" action="{{ route('admin.soal.store') }}" class="space-y-5">
            @csrf
            @include('admin.soal._form')

            <div class="flex items-center gap-3 border-t border-gray-200 pt-5">
                <button type="submit" class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                    Simpan soal
                </button>
                <a href="{{ route('admin.soal.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Batal</a>
            </div>
        </form>
    </section>
</x-admin-layout>
