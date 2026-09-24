<?php

namespace Database\Seeders;

use App\Models\Materi;
use Illuminate\Database\Seeder;

class MateriSeeder extends Seeder
{
    // ponytail: 0–12 bulan masih PLACEHOLDER; link video semua kelompok belum ada. Saat masuk:
    // ganti judul di sini, isi view di resources/views/materi/usia-*/, isi video (ID YouTube, bukan URL).
    // [slug, judul, aspek, kelompok_usia, urutan, konten_view, video_youtube_id]
    private const MATERI = [
        ['0-3-motorik-kasar', '[PLACEHOLDER] Motorik Kasar 0–3 bulan', 'motorik_kasar', '0-3', 1, 'materi.usia-0-3.motorik-kasar', null],
        ['0-3-motorik-halus', '[PLACEHOLDER] Motorik Halus 0–3 bulan', 'motorik_halus', '0-3', 1, 'materi.usia-0-3.motorik-halus', null],
        ['0-3-bicara-bahasa', '[PLACEHOLDER] Bicara & Bahasa 0–3 bulan', 'bicara_bahasa', '0-3', 1, 'materi.usia-0-3.bicara-bahasa', null],
        ['0-3-sosial-emosional', '[PLACEHOLDER] Sosial & Emosional 0–3 bulan', 'sosial_emosional', '0-3', 1, 'materi.usia-0-3.sosial-emosional', null],

        ['3-6-motorik-kasar', '[PLACEHOLDER] Motorik Kasar 3–6 bulan', 'motorik_kasar', '3-6', 1, 'materi.usia-3-6.motorik-kasar', null],
        ['3-6-motorik-halus', '[PLACEHOLDER] Motorik Halus 3–6 bulan', 'motorik_halus', '3-6', 1, 'materi.usia-3-6.motorik-halus', null],
        ['3-6-bicara-bahasa', '[PLACEHOLDER] Bicara & Bahasa 3–6 bulan', 'bicara_bahasa', '3-6', 1, 'materi.usia-3-6.bicara-bahasa', null],
        ['3-6-sosial-emosional', '[PLACEHOLDER] Sosial & Emosional 3–6 bulan', 'sosial_emosional', '3-6', 1, 'materi.usia-3-6.sosial-emosional', null],

        ['6-9-motorik-kasar', '[PLACEHOLDER] Motorik Kasar 6–9 bulan', 'motorik_kasar', '6-9', 1, 'materi.usia-6-9.motorik-kasar', null],
        ['6-9-motorik-halus', '[PLACEHOLDER] Motorik Halus 6–9 bulan', 'motorik_halus', '6-9', 1, 'materi.usia-6-9.motorik-halus', null],
        ['6-9-bicara-bahasa', '[PLACEHOLDER] Bicara & Bahasa 6–9 bulan', 'bicara_bahasa', '6-9', 1, 'materi.usia-6-9.bicara-bahasa', null],
        ['6-9-sosial-emosional', '[PLACEHOLDER] Sosial & Emosional 6–9 bulan', 'sosial_emosional', '6-9', 1, 'materi.usia-6-9.sosial-emosional', null],

        ['9-12-motorik-kasar', '[PLACEHOLDER] Motorik Kasar 9–12 bulan', 'motorik_kasar', '9-12', 1, 'materi.usia-9-12.motorik-kasar', null],
        ['9-12-motorik-halus', '[PLACEHOLDER] Motorik Halus 9–12 bulan', 'motorik_halus', '9-12', 1, 'materi.usia-9-12.motorik-halus', null],
        ['9-12-bicara-bahasa', '[PLACEHOLDER] Bicara & Bahasa 9–12 bulan', 'bicara_bahasa', '9-12', 1, 'materi.usia-9-12.bicara-bahasa', null],
        ['9-12-sosial-emosional', '[PLACEHOLDER] Sosial & Emosional 9–12 bulan', 'sosial_emosional', '9-12', 1, 'materi.usia-9-12.sosial-emosional', null],

        // 12–18, 18–24, 24–36: konten asli dari "Stimulasi anak 12 - 24 bulan.docx" + "Materi Aplikasi.docx" (Sesi 18a).
        ['12-18-motorik-kasar', 'Berdiri dengan Berpegangan pada Kursi atau Meja', 'motorik_kasar', '12-18', 1, 'materi.usia-12-18.motorik-kasar', null],
        ['12-18-motorik-halus', 'Mempertemukan 2 Kubus Kecil yang Dipegang', 'motorik_halus', '12-18', 1, 'materi.usia-12-18.motorik-halus', null],
        ['12-18-bicara-bahasa', 'Meniru 2–3 Kata Sederhana', 'bicara_bahasa', '12-18', 1, 'materi.usia-12-18.bicara-bahasa', null],
        ['12-18-sosial-emosional', 'Mencari Ibu Saat Bermain Cilukba', 'sosial_emosional', '12-18', 1, 'materi.usia-12-18.sosial-emosional', null],

        ['18-24-motorik-kasar', 'Berjalan tanpa Terjatuh', 'motorik_kasar', '18-24', 1, 'materi.usia-18-24.motorik-kasar', null],
        ['18-24-motorik-halus', 'Menggelindingkan atau Melempar Bola', 'motorik_halus', '18-24', 1, 'materi.usia-18-24.motorik-halus', null],
        ['18-24-bicara-bahasa', 'Menyebutkan Sedikitnya 3 Kata Bermakna', 'bicara_bahasa', '18-24', 1, 'materi.usia-18-24.bicara-bahasa', null],
        ['18-24-sosial-emosional', 'Menunjukkan Keinginan tanpa Menangis atau Merengek', 'sosial_emosional', '18-24', 1, 'materi.usia-18-24.sosial-emosional', null],

        ['24-36-motorik-kasar', 'Berlari tanpa Terjatuh', 'motorik_kasar', '24-36', 1, 'materi.usia-24-36.motorik-kasar', null],
        ['24-36-motorik-halus', 'Mencoret-coret Kertas tanpa Bantuan', 'motorik_halus', '24-36', 1, 'materi.usia-24-36.motorik-halus', null],
        ['24-36-bicara-bahasa', 'Menunjukkan Paling Sedikit 2 Bagian Tubuh', 'bicara_bahasa', '24-36', 1, 'materi.usia-24-36.bicara-bahasa', null],
        ['24-36-sosial-emosional', 'Makan Menggunakan Sendok Sendiri', 'sosial_emosional', '24-36', 1, 'materi.usia-24-36.sosial-emosional', null],
    ];

    public function run(): void
    {
        // Per slug (bukan hapus-isi ulang): FK progress_materi cascadeOnDelete
        // akan ikut menghapus progres responden. Seeding ulang cukup memperbarui judul/view.
        foreach (self::MATERI as [$slug, $judul, $aspek, $kelompok, $urutan, $view, $video]) {
            $materi = Materi::firstOrNew(['slug' => $slug]);

            $materi->fill([
                'judul' => $judul,
                'aspek' => $aspek,
                'kelompok_usia' => $kelompok,
                'urutan' => $urutan,
                'konten_view' => $view,
            ]);

            // Video hanya diisi bila masih kosong: tautan yang diganti admin lewat
            // Kelola Materi (Sesi 34) tidak boleh ditimpa saat seeder dijalankan ulang.
            $materi->video_youtube_id ??= $video;

            $materi->save();
        }
    }
}
