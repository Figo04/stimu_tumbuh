<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kalender Stimulasi</h2>
        <p class="text-sm text-gray-500">Catat setiap kali Anda melakukan stimulasi. Boleh diisi berkali-kali.</p>
    </x-slot>

    {{-- Tampilan kalender bulanan menyusul (Sesi 21). --}}
    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <p class="rounded-md bg-emerald-50 px-4 py-3 text-emerald-800">✓ {{ session('status') }}</p>
            @endif

            <section class="bg-white shadow-sm sm:rounded-lg p-6 text-base text-gray-800">
                <h3 class="font-semibold text-gray-800">Tambah catatan stimulasi</h3>
                <form method="POST" action="{{ route('aktivitas.store') }}" class="mt-4 space-y-4">
                    @csrf
                    @include('aktivitas._form', ['entri' => null])
                    <button type="submit" class="w-full rounded-md bg-indigo-600 px-4 py-3 text-base font-medium text-white shadow-sm hover:bg-indigo-700">
                        Simpan catatan
                    </button>
                </form>
            </section>

            <section class="bg-white shadow-sm sm:rounded-lg p-6 text-base text-gray-800">
                <h3 class="font-semibold text-gray-800">Riwayat stimulasi ({{ $riwayat->count() }})</h3>
                @forelse ($riwayat as $entri)
                    <div class="mt-3 border-t border-gray-100 pt-3">
                        <p class="text-sm font-medium text-gray-600">{{ $entri->tanggal->translatedFormat('l, d F Y') }}</p>
                        <p class="mt-1 font-medium">
                            {{ \App\Models\Materi::ASPEK[$entri->aspek] }}{{ $entri->jenis_stimulasi ? ' — '.$entri->jenis_stimulasi : '' }}
                        </p>
                        <p class="text-sm text-gray-600">
                            Oleh {{ \App\Models\AktivitasStimulasi::PELAKU[$entri->pelaku] }}{{ $entri->durasi_menit ? ' · '.$entri->durasi_menit.' menit' : '' }}{{ $entri->materi ? ' · dari Praktik: '.$entri->materi->judul : '' }}
                        </p>
                        @if ($entri->respons_anak)
                            <p class="mt-1 whitespace-pre-line">{{ $entri->respons_anak }}</p>
                        @endif
                        <div class="mt-2 flex gap-2">
                            <a href="{{ route('aktivitas.edit', $entri) }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Ubah</a>
                            <form method="POST" action="{{ route('aktivitas.destroy', $entri) }}" onsubmit="return confirm('Hapus catatan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-md border border-red-300 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-50">Hapus</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="mt-2 text-gray-500">Belum ada catatan stimulasi.</p>
                @endforelse
            </section>
        </div>
    </div>
</x-app-layout>
