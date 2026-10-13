<?php

namespace Database\Seeders;

use App\Models\Materi;
use Illuminate\Database\Seeder;

class MateriSeeder extends Seeder
{
    // Materi = video saja. Rentang penelitian 12–24 bulan: 3 kelompok × 4 aspek = 12 materi.
    // Judul dari "Stimulasi anak 12 - 24 bulan.docx" + "Materi Aplikasi.docx" (Sesi 18a).
    // Video dari klien 13 Okt 2026 (kanal Jurusan Kebidanan Poltekkes Jakarta I), dicocokkan lewat judul video.
    // 18-24 motorik kasar: video "Gerak Kasar 18–23 Bulan" belum diberikan klien (null).
    // [slug, judul, aspek, kelompok_usia, video_youtube_id]
    private const MATERI = [
        ['12-18-motorik-kasar', 'Berdiri dengan Berpegangan pada Kursi atau Meja', 'motorik_kasar', '12-18', 'jNXupLa2SNA'],
        ['12-18-motorik-halus', 'Mempertemukan 2 Kubus Kecil yang Dipegang', 'motorik_halus', '12-18', 'v7-eYmJQ4ok'],
        ['12-18-bicara-bahasa', 'Meniru 2–3 Kata Sederhana', 'bicara_bahasa', '12-18', 'zOHEf4UB0ko'],
        ['12-18-sosial-emosional', 'Mencari Ibu Saat Bermain Cilukba', 'sosial_emosional', '12-18', 'l4tmbB1LsKc'],

        ['18-24-motorik-kasar', 'Berjalan tanpa Terjatuh', 'motorik_kasar', '18-24', null],
        ['18-24-motorik-halus', 'Menggelindingkan atau Melempar Bola', 'motorik_halus', '18-24', 'qECPDnKNx28'],
        ['18-24-bicara-bahasa', 'Menyebutkan Sedikitnya 3 Kata Bermakna', 'bicara_bahasa', '18-24', 'UBT_62llJZ8'],
        ['18-24-sosial-emosional', 'Menunjukkan Keinginan tanpa Menangis atau Merengek', 'sosial_emosional', '18-24', 'jt15KTqVUs8'],

        ['24-36-motorik-kasar', 'Berlari tanpa Terjatuh', 'motorik_kasar', '24-36', 'O_CSDsKQ1Tk'],
        ['24-36-motorik-halus', 'Mencoret-coret Kertas tanpa Bantuan', 'motorik_halus', '24-36', 'S6Zc3pM_NS4'],
        ['24-36-bicara-bahasa', 'Menunjukkan Paling Sedikit 2 Bagian Tubuh', 'bicara_bahasa', '24-36', '91Otfj9lfBo'],
        ['24-36-sosial-emosional', 'Makan Menggunakan Sendok Sendiri', 'sosial_emosional', '24-36', '6iMotmAAtWA'],
    ];

    public function run(): void
    {
        // Per slug (bukan hapus-isi ulang): FK progress_materi cascadeOnDelete
        // akan ikut menghapus progres responden. Video hanya mengisi yang masih kosong
        // supaya tautan yang diganti admin (Kelola Materi) tidak tertimpa.
        foreach (self::MATERI as [$slug, $judul, $aspek, $kelompok, $video]) {
            $materi = Materi::updateOrCreate(['slug' => $slug], [
                'judul' => $judul,
                'aspek' => $aspek,
                'kelompok_usia' => $kelompok,
                'urutan' => 1,
            ]);
            $materi->video_youtube_id ??= $video;
            $materi->save();
        }
    }
}
