<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('aktivitas.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&lsaquo; Kalender Stimulasi</a>
        <h2 class="mt-1 font-semibold text-xl text-gray-800 leading-tight">Ubah catatan stimulasi</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <section class="bg-white shadow-sm sm:rounded-lg p-6 text-base text-gray-800">
                <form method="POST" action="{{ route('aktivitas.update', $entri) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    @include('aktivitas._form')
                    <button type="submit" class="w-full rounded-md bg-indigo-600 px-4 py-3 text-base font-medium text-white shadow-sm hover:bg-indigo-700">
                        Simpan perubahan
                    </button>
                </form>
            </section>
        </div>
    </div>
</x-app-layout>
