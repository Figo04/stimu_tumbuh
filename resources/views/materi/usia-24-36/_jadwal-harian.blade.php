{{-- Sumber: dokumen klien "Materi Aplikasi.docx" — dipakai di keempat materi 24–36 bulan. --}}
<h3 class="mt-6 mb-2 text-lg font-semibold text-gray-900">Jadwal stimulasi sederhana anak usia 24 bulan</h3>
<div class="space-y-3">
    @foreach ([
        ['Pagi', 'Berjalan dan bermain bola', 'Motorik kasar'],
        ['Setelah makan', 'Menggunakan sendok sendiri', 'Motorik halus dan kemandirian'],
        ['Siang', 'Membaca buku bergambar', 'Bahasa'],
        ['Sore', 'Bermain balok dan menyusun benda', 'Motorik halus dan kognitif'],
        ['Sore', 'Bermain bersama orang tua', 'Sosial emosional'],
        ['Sebelum tidur', 'Bercerita dan bernyanyi', 'Bahasa dan ikatan emosional'],
    ] as [$waktu, $kegiatan, $aspek])
        <div class="rounded-lg border border-gray-200 p-4">
            <p class="text-sm font-medium text-indigo-600">{{ $waktu }}</p>
            <p class="font-semibold">{{ $kegiatan }}</p>
            <p><span class="font-medium">Aspek yang distimulasi:</span> {{ $aspek }}</p>
        </div>
    @endforeach
</div>
