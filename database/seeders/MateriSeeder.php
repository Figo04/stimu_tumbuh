<?php

namespace Database\Seeders;

use App\Models\Materi;
use Illuminate\Database\Seeder;

class MateriSeeder extends Seeder
{
    // Materi = video saja; tautan video diisi admin lewat Kelola Materi (bukan di sini).
    // Rentang penelitian 12–24 bulan: 3 kelompok × 4 aspek = 12 materi (= 12 video klien).
    // Judul dari "Stimulasi anak 12 - 24 bulan.docx" + "Materi Aplikasi.docx" (Sesi 18a).
    // [slug, judul, aspek, kelompok_usia]
    private const MATERI = [
        ['12-18-motorik-kasar', 'Berdiri dengan Berpegangan pada Kursi atau Meja', 'motorik_kasar', '12-18'],
        ['12-18-motorik-halus', 'Mempertemukan 2 Kubus Kecil yang Dipegang', 'motorik_halus', '12-18'],
        ['12-18-bicara-bahasa', 'Meniru 2–3 Kata Sederhana', 'bicara_bahasa', '12-18'],
        ['12-18-sosial-emosional', 'Mencari Ibu Saat Bermain Cilukba', 'sosial_emosional', '12-18'],

        ['18-24-motorik-kasar', 'Berjalan tanpa Terjatuh', 'motorik_kasar', '18-24'],
        ['18-24-motorik-halus', 'Menggelindingkan atau Melempar Bola', 'motorik_halus', '18-24'],
        ['18-24-bicara-bahasa', 'Menyebutkan Sedikitnya 3 Kata Bermakna', 'bicara_bahasa', '18-24'],
        ['18-24-sosial-emosional', 'Menunjukkan Keinginan tanpa Menangis atau Merengek', 'sosial_emosional', '18-24'],

        ['24-36-motorik-kasar', 'Berlari tanpa Terjatuh', 'motorik_kasar', '24-36'],
        ['24-36-motorik-halus', 'Mencoret-coret Kertas tanpa Bantuan', 'motorik_halus', '24-36'],
        ['24-36-bicara-bahasa', 'Menunjukkan Paling Sedikit 2 Bagian Tubuh', 'bicara_bahasa', '24-36'],
        ['24-36-sosial-emosional', 'Makan Menggunakan Sendok Sendiri', 'sosial_emosional', '24-36'],
    ];

    public function run(): void
    {
        // Per slug (bukan hapus-isi ulang): FK progress_materi cascadeOnDelete
        // akan ikut menghapus progres responden. `video_youtube_id` tidak disentuh
        // supaya tautan yang diisi admin (Kelola Materi) tidak tertimpa.
        foreach (self::MATERI as [$slug, $judul, $aspek, $kelompok]) {
            Materi::updateOrCreate(['slug' => $slug], [
                'judul' => $judul,
                'aspek' => $aspek,
                'kelompok_usia' => $kelompok,
                'urutan' => 1,
            ]);
        }
    }
}
