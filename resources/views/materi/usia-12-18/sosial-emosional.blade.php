{{-- Sumber: dokumen klien "Stimulasi anak 12 - 24 bulan.docx" (usia 12 bulan) + "Materi Aplikasi.docx" (tabel 12–17 bulan). --}}
<x-highlight judul="Apa itu perkembangan sosial emosional?">
    Perkembangan sosial emosional adalah proses anak belajar mengenali dan mengekspresikan emosi, membangun hubungan dengan orang lain, serta beradaptasi dengan lingkungan.
</x-highlight>

<h3 class="mt-6 mb-2 text-lg font-semibold text-gray-900">Mencari atau mengharapkan ibu muncul kembali saat bersembunyi</h3>
<p>Pada usia 12 bulan, anak mulai menunjukkan kemampuan sosial dan emosional yang semakin berkembang. Salah satu kemampuan yang dapat diamati adalah ketika anak mencari atau menunjukkan harapan bahwa ibu akan muncul kembali saat ibu bersembunyi.</p>
<p class="mt-2">Stimulasi dapat dilakukan dengan cara yang sederhana dan menyenangkan melalui permainan cilukba.</p>

<x-langkah-stimulasi judul="Pelaksanaan stimulasi" :langkah="[
    'Duduklah berhadapan dengan anak dalam suasana yang nyaman dan aman. Pastikan anak dapat melihat wajah ibu dengan jelas.',
    'Ajak anak berinteraksi, kemudian tutup wajah ibu menggunakan kedua tangan atau bersembunyi sebentar di balik kain atau benda yang aman.',
    'Setelah beberapa detik, bukalah tangan atau muncul kembali sambil mengatakan, “Cilukba! Mama ada!”',
    'Ulangi permainan beberapa kali. Berikan kesempatan kepada anak untuk menunjukkan respons. Perhatikan apakah anak mencari wajah ibu, melihat ke arah tempat ibu bersembunyi, tersenyum, bersuara, atau menunjukkan tanda-tanda menunggu ibu muncul kembali.',
]" />

<p class="mt-6 mb-2 font-semibold">Contoh interaksi</p>
<div class="space-y-1 rounded-lg bg-gray-50 p-4">
    <p><span class="font-medium">Ibu:</span> “Mana Mama? Mama di mana?”</p>
    <p class="text-gray-600">Ibu bersembunyi sebentar. Anak melihat ke arah tempat ibu bersembunyi.</p>
    <p><span class="font-medium">Ibu muncul kembali:</span> “Cilukba! Mama ada!”</p>
    <p><span class="font-medium">Ibu memberikan senyuman dan pujian:</span> “Pintar! Kamu mencari Mama.”</p>
</div>

<h3 class="mt-6 mb-2 text-lg font-semibold text-gray-900">Aktivitas stimulasi lain usia 12–17 bulan</h3>
<div class="space-y-3">
    @foreach ([
        ['Bermain bersama orang tua', 'Membangun kelekatan dan interaksi'],
        ['Bermain cilukba', 'Mengembangkan interaksi sosial'],
        ['Meniru gerakan', 'Mengembangkan kemampuan meniru'],
        ['Memberikan pilihan sederhana', 'Mendukung kemandirian'],
        ['Memberikan pelukan dan respons positif', 'Membantu anak merasa aman'],
        ['Menamai emosi anak', 'Membantu anak mengenali perasaan'],
    ] as [$aktivitas, $tujuan])
        <div class="rounded-lg border border-gray-200 p-4">
            <p class="font-semibold">{{ $aktivitas }}</p>
            <p><span class="font-medium">Tujuan:</span> {{ $tujuan }}</p>
        </div>
    @endforeach
</div>

@include('materi._peran-orang-tua')
