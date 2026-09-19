<?php

namespace Database\Seeders;

use App\Models\KuesionerSoal;
use Illuminate\Database\Seeder;

class KuesionerSoalSeeder extends Seeder
{
    // ponytail: PLACEHOLDER — dokumen instrumen klien belum masuk. Ganti isi array ini
    // dengan soal + kunci resmi, lalu jalankan ulang di DB kosong (migrate:fresh --seed).
    private const PENGETAHUAN = [
        ['[PLACEHOLDER] Soal pengetahuan 1', 'B'],
        ['[PLACEHOLDER] Soal pengetahuan 2', 'S'],
        ['[PLACEHOLDER] Soal pengetahuan 3', 'B'],
        ['[PLACEHOLDER] Soal pengetahuan 4', 'S'],
        ['[PLACEHOLDER] Soal pengetahuan 5', 'B'],
        ['[PLACEHOLDER] Soal pengetahuan 6', 'S'],
        ['[PLACEHOLDER] Soal pengetahuan 7', 'B'],
        ['[PLACEHOLDER] Soal pengetahuan 8', 'S'],
        ['[PLACEHOLDER] Soal pengetahuan 9', 'B'],
        ['[PLACEHOLDER] Soal pengetahuan 10', 'S'],
    ];

    public function run(): void
    {
        // Jangan hapus-isi ulang: FK hasil_kuesioner_detail.soal_id cascadeOnDelete
        // akan ikut menghapus jawaban responden. Setelah data masuk, soal dikelola via Kelola Soal.
        if (KuesionerSoal::where('tipe', 'pengetahuan')->exists()) {
            return;
        }

        foreach (self::PENGETAHUAN as $i => [$pertanyaan, $jawaban]) {
            KuesionerSoal::create([
                'tipe' => 'pengetahuan',
                'pertanyaan' => $pertanyaan,
                'jawaban_benar' => $jawaban,
                'urutan' => $i + 1,
            ]);
        }
    }
}
