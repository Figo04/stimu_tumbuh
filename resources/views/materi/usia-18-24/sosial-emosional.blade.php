{{-- Sumber: dokumen klien "Stimulasi anak 12 - 24 bulan.docx" (usia 18 bulan) + "Materi Aplikasi.docx" (tabel 18–23 bulan). --}}
<x-highlight judul="Apa itu perkembangan sosial emosional?">
    Perkembangan sosial emosional adalah proses anak belajar mengenali dan mengekspresikan emosi, membangun hubungan dengan orang lain, serta beradaptasi dengan lingkungan.
</x-highlight>

<h3 class="mt-6 mb-2 text-lg font-semibold text-gray-900">Menunjukkan apa yang diinginkan tanpa menangis dan merengek</h3>
<p>Pada usia 18 bulan, anak mulai mampu menyampaikan keinginan dan kebutuhannya dengan cara yang lebih jelas. Anak dapat menggunakan gerakan tubuh, menunjuk, mengambil benda, atau mengucapkan kata sederhana untuk menunjukkan apa yang diinginkannya.</p>
<p class="mt-2">Kemampuan ini merupakan bagian dari perkembangan sosialisasi dan kemandirian yang perlu terus distimulasi dalam aktivitas sehari-hari. Stimulasi dapat dilakukan melalui kegiatan sederhana dengan melibatkan anak dalam pilihan sehari-hari.</p>

<x-langkah-stimulasi judul="Cara melakukan stimulasi" :langkah="[
    'Siapkan dua benda yang aman dan disukai anak, misalnya bola dan boneka. Letakkan kedua benda tersebut di depan anak, tetapi tidak langsung diberikan.',
    'Tanyakan dengan kalimat sederhana, “Adik mau bola atau boneka?”',
    'Berikan waktu kepada anak untuk memilih. Amati respons anak. Anak dapat menunjuk, mengambil, mendekati, atau menyebutkan benda yang diinginkannya.',
    'Ketika anak menunjukkan pilihannya, orang tua dapat merespons dengan positif. Misalnya, “Oh, Adik mau bola. Ini bolanya.”',
    'Berikan benda yang dipilih sambil memberikan pujian, “Pintar, Adik sudah bisa menunjukkan yang Adik mau.”',
]" />

<p class="mt-6 mb-2 font-semibold">Contoh interaksi</p>
<div class="space-y-1 rounded-lg bg-gray-50 p-4">
    <p><span class="font-medium">Ibu:</span> “Adik mau bola atau boneka?”</p>
    <p class="text-gray-600">Anak menunjuk bola.</p>
    <p><span class="font-medium">Ibu:</span> “Mau bola? Baik, ini bolanya.”</p>
    <p class="text-gray-600">Anak menerima bola.</p>
    <p><span class="font-medium">Ibu:</span> “Hebat! Adik sudah bisa menunjukkan keinginan.”</p>
</div>

<h3 class="mt-6 mb-2 text-lg font-semibold text-gray-900">Aktivitas stimulasi lain usia 18–23 bulan</h3>
<div class="space-y-3">
    @foreach ([
        ['Bermain bersama orang tua', 'Ajak bermain sederhana', 'Membangun kedekatan'],
        ['Bermain peran', 'Gunakan boneka atau alat mainan', 'Mengembangkan imitasi dan interaksi'],
        ['Memberikan pilihan', 'Tawarkan dua pilihan yang aman', 'Mendukung kemandirian'],
        ['Mengenali emosi', 'Beri nama emosi anak', 'Mendukung pemahaman emosi'],
        ['Bermain bergiliran', 'Gunakan permainan sederhana dengan bantuan', 'Mengenalkan konsep bergiliran'],
    ] as [$aktivitas, $cara, $manfaat])
        <div class="rounded-lg border border-gray-200 p-4">
            <p class="font-semibold">{{ $aktivitas }}</p>
            <p><span class="font-medium">Cara melakukan:</span> {{ $cara }}</p>
            <p><span class="font-medium">Manfaat:</span> {{ $manfaat }}</p>
        </div>
    @endforeach
</div>

@include('materi._peran-orang-tua')
