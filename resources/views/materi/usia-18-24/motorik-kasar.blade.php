{{-- Sumber: dokumen klien "Stimulasi anak 12 - 24 bulan.docx" (usia 18 bulan) + "Materi Aplikasi.docx" (tabel 18–23 bulan). --}}
<x-highlight judul="Apa itu motorik kasar?">
    Motorik kasar adalah kemampuan anak menggunakan otot-otot besar tubuh, seperti otot tungkai, lengan, punggung, dan tubuh secara keseluruhan untuk melakukan gerakan.
    Kemampuan motorik kasar mendukung anak dalam bergerak, menjaga keseimbangan, berpindah tempat, dan melakukan aktivitas sehari-hari.
</x-highlight>

<h3 class="mt-6 mb-2 text-lg font-semibold text-gray-900">Berjalan tanpa terjatuh</h3>
<p>Pada usia 18 bulan, kemampuan motorik kasar anak semakin berkembang. Salah satu kemampuan yang perlu distimulasi adalah kemampuan berjalan secara mandiri dengan lebih stabil dan mampu mempertahankan keseimbangan tubuh sehingga tidak mudah terjatuh.</p>
<p class="mt-2">Stimulasi dapat dilakukan melalui aktivitas berjalan sederhana bersama orang tua.</p>

<x-langkah-stimulasi judul="Pelaksanaan stimulasi" :langkah="[
    'Pastikan anak berada di lingkungan yang aman, datar, dan bebas dari benda yang dapat membuat anak tersandung. Orang tua dapat berdiri beberapa langkah di depan anak.',
    'Ajak anak untuk berjalan menuju orang tua dengan memberikan panggilan atau menunjukkan mainan yang menarik. Berikan kesempatan kepada anak untuk berjalan secara mandiri.',
    'Saat anak berjalan, orang tua dapat mendampingi dari dekat untuk menjaga keamanan tanpa terus-menerus memegang tubuh anak. Berikan pujian dan dorongan ketika anak berhasil melangkah dengan stabil.',
]" />

<p class="mt-6 mb-2 font-semibold">Contoh aktivitas</p>
<div class="space-y-1 rounded-lg bg-gray-50 p-4">
    <p>Letakkan mainan kesukaan anak pada jarak yang tidak terlalu jauh. Kemudian katakan, “Ayo, jalan ke Mama.”</p>
    <p>Biarkan anak mencoba berjalan menuju orang tua. Jika anak kehilangan keseimbangan, berikan bantuan secukupnya agar anak tetap aman.</p>
    <p>Setelah anak berhasil mencapai orang tua, berikan apresiasi, seperti “Hebat! Kamu sudah bisa berjalan.”</p>
</div>

<h3 class="mt-6 mb-2 text-lg font-semibold text-gray-900">Aktivitas stimulasi lain usia 18–23 bulan</h3>
<div class="space-y-3">
    @foreach ([
        ['Berjalan mengikuti arah', 'Ajak anak berjalan menuju orang tua atau mainan', 'Melatih keseimbangan'],
        ['Menendang bola', 'Letakkan bola di depan anak dan ajak menendang', 'Koordinasi mata dan kaki'],
        ['Berjalan membawa benda ringan', 'Ajak anak membawa mainan ringan sambil berjalan', 'Koordinasi dan keseimbangan'],
        ['Membungkuk mengambil mainan', 'Letakkan mainan di lantai dan minta anak mengambilnya', 'Kekuatan otot dan keseimbangan'],
        ['Melangkahi benda rendah', 'Gunakan benda yang aman dan sangat rendah dengan bantuan', 'Koordinasi gerak'],
    ] as [$aktivitas, $cara, $manfaat])
        <div class="rounded-lg border border-gray-200 p-4">
            <p class="font-semibold">{{ $aktivitas }}</p>
            <p><span class="font-medium">Cara melakukan:</span> {{ $cara }}</p>
            <p><span class="font-medium">Manfaat:</span> {{ $manfaat }}</p>
        </div>
    @endforeach
</div>

@include('materi._peran-orang-tua')
