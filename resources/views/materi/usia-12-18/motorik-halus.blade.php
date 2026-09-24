{{-- Sumber: dokumen klien "Stimulasi anak 12 - 24 bulan.docx" (usia 12 bulan) + "Materi Aplikasi.docx" (tabel 12–17 bulan). --}}
<x-highlight judul="Apa itu motorik halus?">
    Motorik halus adalah kemampuan menggunakan otot-otot kecil, terutama tangan dan jari, yang melibatkan koordinasi mata dan tangan.
    Kemampuan ini diperlukan untuk mengambil benda, memegang alat makan, menyusun mainan, serta melakukan aktivitas yang membutuhkan ketelitian.
</x-highlight>

<h3 class="mt-6 mb-2 text-lg font-semibold text-gray-900">Mempertemukan 2 kubus kecil yang dipegang</h3>
<p>Pada usia 12 bulan, salah satu kemampuan motorik halus yang dapat distimulasi adalah kemampuan anak mempertemukan dua benda yang dipegang menggunakan kedua tangannya.</p>

<x-checklist judul="Persiapan" :items="[
    'Siapkan dua kubus kecil yang aman untuk anak, berukuran cukup besar sehingga tidak mudah tertelan, serta memiliki permukaan yang mudah digenggam.',
]" />

<x-langkah-stimulasi judul="Pelaksanaan stimulasi" :langkah="[
    'Dudukkan anak dengan posisi yang nyaman dan aman. Berikan satu kubus pada tangan kanan dan satu kubus pada tangan kiri anak.',
    'Berikan contoh dengan cara perlahan. Arahkan kedua tangan anak untuk bergerak mendekat, kemudian pertemukan kedua kubus hingga saling bersentuhan.',
    'Berikan kesempatan kepada anak untuk mencoba secara mandiri. Orang tua atau pendamping dapat memberikan contoh dan bantuan ringan bila diperlukan, tanpa memaksa gerakan anak.',
]" />

<h3 class="mt-6 mb-2 text-lg font-semibold text-gray-900">Aktivitas stimulasi lain usia 12–17 bulan</h3>
<div class="space-y-3">
    @foreach ([
        ['Memasukkan benda ke wadah', 'Koordinasi mata dan tangan', 'Wadah dan benda besar yang aman'],
        ['Menyusun balok', 'Koordinasi jari dan tangan', 'Balok berukuran besar'],
        ['Mencoret', 'Melatih kontrol tangan', 'Krayon besar dan kertas'],
        ['Membalik halaman buku', 'Melatih koordinasi jari', 'Buku karton'],
        ['Makan menggunakan sendok', 'Melatih kemandirian', 'Sendok anak dan makanan sesuai usia'],
    ] as [$aktivitas, $tujuan, $media])
        <div class="rounded-lg border border-gray-200 p-4">
            <p class="font-semibold">{{ $aktivitas }}</p>
            <p><span class="font-medium">Tujuan:</span> {{ $tujuan }}</p>
            <p><span class="font-medium">Media:</span> {{ $media }}</p>
        </div>
    @endforeach
</div>

@include('materi._peran-orang-tua')
