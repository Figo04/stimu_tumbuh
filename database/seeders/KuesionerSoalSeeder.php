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

    // ponytail: PLACEHOLDER — [pertanyaan, reverse_scored]. Soal genap reverse supaya
    // skor Sesi 10 teruji dua arah; ganti dengan soal + penanda resmi dari dokumen klien.
    private const SIKAP = [
        ['[PLACEHOLDER] Soal sikap 1', false],
        ['[PLACEHOLDER] Soal sikap 2', true],
        ['[PLACEHOLDER] Soal sikap 3', false],
        ['[PLACEHOLDER] Soal sikap 4', true],
        ['[PLACEHOLDER] Soal sikap 5', false],
        ['[PLACEHOLDER] Soal sikap 6', true],
        ['[PLACEHOLDER] Soal sikap 7', false],
        ['[PLACEHOLDER] Soal sikap 8', true],
        ['[PLACEHOLDER] Soal sikap 9', false],
        ['[PLACEHOLDER] Soal sikap 10', true],
    ];

    public function run(): void
    {
        // Jangan hapus-isi ulang: FK hasil_kuesioner_detail.soal_id cascadeOnDelete
        // akan ikut menghapus jawaban responden. Setelah data masuk, soal dikelola via Kelola Soal.
        // Dicek per tipe, supaya tipe yang belum ada tetap bisa diisi di DB yang sudah berjalan.
        if (! KuesionerSoal::where('tipe', 'pengetahuan')->exists()) {
            foreach (self::PENGETAHUAN as $i => [$pertanyaan, $jawaban]) {
                KuesionerSoal::create([
                    'tipe' => 'pengetahuan',
                    'pertanyaan' => $pertanyaan,
                    'jawaban_benar' => $jawaban,
                    'urutan' => $i + 1,
                ]);
            }
        }

        if (! KuesionerSoal::where('tipe', 'sikap')->exists()) {
            foreach (self::SIKAP as $i => [$pertanyaan, $reverse]) {
                KuesionerSoal::create([
                    'tipe' => 'sikap',
                    'pertanyaan' => $pertanyaan,
                    'reverse_scored' => $reverse,
                    'urutan' => $i + 1,
                ]);
            }
        }
    }
}
