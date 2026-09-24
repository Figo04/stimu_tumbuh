{{-- Sumber: dokumen klien "Stimulasi anak 12 - 24 bulan.docx" (usia 18 bulan) + "Materi Aplikasi.docx" (tabel 18–23 bulan). --}}
<x-highlight judul="Apa itu motorik halus?">
    Motorik halus adalah kemampuan menggunakan otot-otot kecil, terutama tangan dan jari, yang melibatkan koordinasi mata dan tangan.
    Kemampuan ini diperlukan untuk mengambil benda, memegang alat makan, menyusun mainan, serta melakukan aktivitas yang membutuhkan ketelitian.
</x-highlight>

<h3 class="mt-6 mb-2 text-lg font-semibold text-gray-900">Menggelindingkan atau melempar bola kepada orang tua</h3>
<p>Pada usia 18 bulan, anak mulai mengembangkan koordinasi mata dan tangan yang semakin baik. Salah satu kemampuan gerak halus yang penting untuk distimulasi adalah kemampuan anak untuk menggelindingkan atau melempar bola kepada orang tuanya. Aktivitas sederhana ini tidak hanya menyenangkan, tetapi juga bermanfaat untuk melatih koordinasi, kekuatan otot tangan, serta kemampuan fokus anak.</p>

<x-checklist judul="Persiapan" :items="[
    'Siapkan bola berukuran sedang, ringan, dan aman untuk anak.',
    'Pastikan area bermain bersih, luas, dan bebas dari benda berbahaya.',
]" />

<x-langkah-stimulasi judul="Cara melakukan stimulasi" :langkah="[
    'Duduklah berhadapan dengan anak pada jarak yang sesuai agar anak merasa nyaman.',
    'Tunjukkan bola kepada anak sambil berkata dengan nada ceria, “Ini bola, yuk kita main bola.”',
    'Ajak anak untuk menggelindingkan atau melempar bola kepada Anda. Anda dapat memberi contoh terlebih dahulu.',
    'Setelah anak menggelindingkan atau melempar bola, terima bola dengan antusias dan kembalikan kepada anak.',
    'Berikan pujian setiap kali anak berhasil melakukan gerakan, “Hebat! Kamu sudah bisa lempar bola.”',
    'Ulangi permainan beberapa kali secara menyenangkan dan tidak memaksa.',
]" />

<h3 class="mt-6 mb-2 text-lg font-semibold text-gray-900">Aktivitas stimulasi lain usia 18–23 bulan</h3>
<div class="space-y-3">
    @foreach ([
        ['Menyusun balok', 'Susun balok bersama anak', 'Koordinasi tangan'],
        ['Memasukkan benda ke wadah', 'Gunakan benda berukuran besar dan aman', 'Koordinasi mata dan tangan'],
        ['Mencoret kertas', 'Berikan krayon yang sesuai', 'Kekuatan jari'],
        ['Membalik halaman buku', 'Gunakan buku anak', 'Koordinasi jari'],
        ['Bermain dengan sendok', 'Dampingi anak saat makan', 'Keterampilan bantu diri'],
    ] as [$aktivitas, $cara, $manfaat])
        <div class="rounded-lg border border-gray-200 p-4">
            <p class="font-semibold">{{ $aktivitas }}</p>
            <p><span class="font-medium">Cara melakukan:</span> {{ $cara }}</p>
            <p><span class="font-medium">Manfaat:</span> {{ $manfaat }}</p>
        </div>
    @endforeach
</div>

@include('materi._peran-orang-tua')
