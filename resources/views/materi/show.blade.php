<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('materi.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&lsaquo; Semua materi</a>
        <h2 class="mt-1 font-semibold text-xl text-gray-800 leading-tight">{{ $materi->judul }}</h2>
        <p class="text-sm text-gray-500">{{ \App\Models\Materi::ASPEK[$materi->aspek] }}</p>
    </x-slot>

    <div class="py-8" x-data="{ tab: 'materi' }">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-4 grid grid-cols-2 gap-2" role="tablist">
                <button type="button" role="tab" @click="tab = 'materi'" :aria-selected="tab === 'materi'"
                        :class="tab === 'materi' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700'"
                        class="rounded-md px-4 py-3 text-base font-medium shadow-sm">Materi</button>
                <button type="button" role="tab" @click="tab = 'praktik'" :aria-selected="tab === 'praktik'"
                        :class="tab === 'praktik' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700'"
                        class="rounded-md px-4 py-3 text-base font-medium shadow-sm">Praktik 🔒</button>
            </div>

            <article x-show="tab === 'materi'" class="bg-white shadow-sm sm:rounded-lg p-6 text-base leading-relaxed text-gray-800">
                @include($materi->konten_view)
            </article>

            {{-- Isi & gating tab Praktik dikerjakan di Sesi 19 (setelah progress materi Sesi 13). --}}
            <div x-show="tab === 'praktik'" style="display: none" class="bg-white shadow-sm sm:rounded-lg p-6 text-gray-600">
                Tab Praktik terbuka setelah materi ini selesai dibaca.
            </div>
        </div>
    </div>
</x-app-layout>
