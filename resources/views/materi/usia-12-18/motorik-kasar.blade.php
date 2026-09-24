{{-- Sumber: dokumen klien "Stimulasi anak 12 - 24 bulan.docx" (usia 12 bulan) + "Materi Aplikasi.docx" (tabel 12–17 bulan). --}}
<x-highlight judul="Apa itu motorik kasar?">
    Motorik kasar adalah kemampuan anak menggunakan otot-otot besar tubuh, seperti otot tungkai, lengan, punggung, dan tubuh secara keseluruhan untuk melakukan gerakan.
    Kemampuan motorik kasar mendukung anak dalam bergerak, menjaga keseimbangan, berpindah tempat, dan melakukan aktivitas sehari-hari.
</x-highlight>

<h3 class="mt-6 mb-2 text-lg font-semibold text-gray-900">Berdiri dengan berpegangan pada kursi atau meja selama 30 detik atau lebih</h3>
<p>Pada usia 12 bulan, salah satu kemampuan motorik kasar yang dapat distimulasi adalah kemampuan anak untuk berdiri dengan berpegangan pada kursi atau meja.</p>

<x-checklist judul="Persiapan" :items="[
    'Siapkan kursi atau meja yang kokoh, stabil, dan aman bagi anak.',
    'Pastikan permukaan lantai tidak licin dan area sekitar anak bebas dari benda yang dapat membahayakan.',
]" />

<x-langkah-stimulasi judul="Pelaksanaan stimulasi" :langkah="[
    'Dudukkan anak di dekat kursi atau meja yang kokoh. Letakkan mainan atau benda yang menarik perhatian anak di atas permukaan meja atau kursi, pada posisi yang dapat dijangkau anak.',
    'Bantu anak untuk berpegangan pada tepi kursi atau meja menggunakan kedua tangannya. Berikan kesempatan kepada anak untuk menarik tubuhnya secara perlahan hingga berada dalam posisi berdiri.',
    'Setelah anak berdiri, berikan motivasi dengan mengajak berbicara atau memberikan pujian. Biarkan anak tetap berpegangan pada kursi atau meja dan pertahankan posisi berdiri sesuai kemampuan anak.',
]" />

<h3 class="mt-6 mb-2 text-lg font-semibold text-gray-900">Aktivitas stimulasi lain usia 12–17 bulan</h3>
<div class="space-y-3">
    @foreach ([
        ['Berjalan menuju ibu', 'Melatih keseimbangan dan koordinasi', 'Ibu memanggil anak dari jarak dekat'],
        ['Mendorong mainan', 'Melatih kekuatan otot dan keseimbangan', 'Gunakan mainan yang kokoh dan tidak mudah terguling'],
        ['Mengambil benda', 'Melatih perubahan posisi tubuh', 'Letakkan mainan pada tempat yang aman dan mudah dijangkau'],
        ['Menari mengikuti lagu', 'Melatih koordinasi gerak', 'Ajak anak bergerak mengikuti irama'],
        ['Bermain bola', 'Melatih koordinasi gerak', 'Ajak anak menggulirkan atau menendang bola secara sederhana'],
    ] as [$aktivitas, $tujuan, $cara])
        <div class="rounded-lg border border-gray-200 p-4">
            <p class="font-semibold">{{ $aktivitas }}</p>
            <p><span class="font-medium">Tujuan:</span> {{ $tujuan }}</p>
            <p><span class="font-medium">Cara melakukan:</span> {{ $cara }}</p>
        </div>
    @endforeach
</div>

@include('materi._peran-orang-tua')
