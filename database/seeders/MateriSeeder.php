<?php

namespace Database\Seeders;

use App\Models\Materi;
use Illuminate\Database\Seeder;

class MateriSeeder extends Seeder
{
    // ponytail: PLACEHOLDER — dokumen materi & link video klien belum masuk. Saat masuk:
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
    ];

    public function run(): void
    {
        // updateOrCreate per slug (bukan hapus-isi ulang): FK progress_materi cascadeOnDelete
        // akan ikut menghapus progres responden. Seeding ulang cukup memperbarui judul/view/video.
        foreach (self::MATERI as [$slug, $judul, $aspek, $kelompok, $urutan, $view, $video]) {
            Materi::updateOrCreate(['slug' => $slug], [
                'judul' => $judul,
                'aspek' => $aspek,
                'kelompok_usia' => $kelompok,
                'urutan' => $urutan,
                'konten_view' => $view,
                'video_youtube_id' => $video,
            ]);
        }
    }
}
