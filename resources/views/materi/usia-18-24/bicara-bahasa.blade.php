{{-- Sumber: dokumen klien "Stimulasi anak 12 - 24 bulan.docx" (usia 18 bulan) + "Materi Aplikasi.docx" (tabel 18–23 bulan). --}}
<x-highlight judul="Apa itu perkembangan bahasa?">
    Perkembangan bahasa adalah proses anak memahami dan menggunakan simbol komunikasi, baik melalui suara, kata, gestur, maupun ekspresi. Bahasa terdiri atas dua kemampuan utama:
    <ul class="mt-2 list-disc space-y-1 pl-5">
        <li><span class="font-medium">Bahasa reseptif:</span> kemampuan memahami bahasa yang didengar.</li>
        <li><span class="font-medium">Bahasa ekspresif:</span> kemampuan menyampaikan keinginan, pikiran, atau perasaan melalui suara, kata, dan gestur.</li>
    </ul>
</x-highlight>

<h3 class="mt-6 mb-2 text-lg font-semibold text-gray-900">Menyebutkan sedikitnya 3 kata yang bermakna</h3>
<p>Pada usia 18 bulan, kemampuan bicara dan bahasa anak mulai berkembang dengan semakin baik. Anak mulai memahami kata-kata yang sering didengar dan mencoba menggunakannya untuk berkomunikasi.</p>
<p class="mt-2">Salah satu kemampuan yang perlu distimulasi adalah kemampuan anak untuk menyebutkan sedikitnya tiga kata yang memiliki makna, misalnya “mama”, “papa”, “bola”, “makan”, atau “minum”.</p>
<p class="mt-2">Stimulasi dapat dilakukan melalui percakapan dan aktivitas sehari-hari.</p>

<x-langkah-stimulasi judul="Cara melakukan stimulasi" :langkah="[
    'Pilih benda atau aktivitas yang dekat dengan anak, misalnya bola. Tunjukkan benda tersebut sambil menyebutkan namanya dengan jelas dan perlahan, “Ini bola.”',
    'Ajak anak untuk mencoba mengucapkan kata tersebut. Berikan waktu kepada anak untuk merespons dan jangan terburu-buru membantu.',
    'Setelah anak mencoba mengucapkan “bola”, berikan respons positif, misalnya, “Iya, bola. Pintar!”',
    'Lakukan hal yang sama dengan kata lain yang sering digunakan dalam kehidupan sehari-hari, seperti “mama”, “papa”, “makan”, atau “minum”.',
]" />

<p class="mt-6 mb-2 font-semibold">Contoh interaksi</p>
<div class="space-y-1 rounded-lg bg-gray-50 p-4">
    <p><span class="font-medium">Orang tua</span> (menunjukkan bola): “Ini apa? Bola.”</p>
    <p class="text-gray-600">Berikan kesempatan kepada anak untuk menirukan.</p>
    <p><span class="font-medium">Anak:</span> “Bola.”</p>
    <p><span class="font-medium">Orang tua:</span> “Iya, bola. Pintar!”</p>
    <p><span class="font-medium">Orang tua</span> (menunjukkan makanan): “Makan. Ayo makan.”</p>
    <p class="text-gray-600">Saat anak mencoba mengatakan “makan”, berikan pujian. Dengan cara yang sama, orang tua dapat mengenalkan kata “mama” dan “papa” melalui interaksi sehari-hari.</p>
</div>

<h3 class="mt-6 mb-2 text-lg font-semibold text-gray-900">Aktivitas stimulasi lain usia 18–23 bulan</h3>
<div class="space-y-3">
    @foreach ([
        ['Membaca buku bergambar', 'Sebutkan nama benda pada gambar', 'Menambah kosakata'],
        ['Berbicara saat bermain', 'Gunakan kalimat sederhana', 'Meningkatkan pemahaman bahasa'],
        ['Bernyanyi', 'Nyanyikan lagu anak dengan gerakan', 'Melatih pendengaran dan komunikasi'],
        ['Menamai benda', 'Sebutkan benda yang digunakan sehari-hari', 'Mengembangkan kosakata'],
        ['Mengajak anak memilih', 'Berikan dua pilihan sederhana', 'Mendukung komunikasi dan kemandirian'],
    ] as [$aktivitas, $cara, $manfaat])
        <div class="rounded-lg border border-gray-200 p-4">
            <p class="font-semibold">{{ $aktivitas }}</p>
            <p><span class="font-medium">Cara melakukan:</span> {{ $cara }}</p>
            <p><span class="font-medium">Manfaat:</span> {{ $manfaat }}</p>
        </div>
    @endforeach
</div>

@include('materi._peran-orang-tua')
