<?php

namespace Database\Seeders;

use App\Models\KuesionerSoal;
use Illuminate\Database\Seeder;

class KuesionerSoalSeeder extends Seeder
{
    // Klien 19 Okt 2026: soal pre/post-test sama dengan dokumen "Kuesioner Tumbuh Kembang utk web",
    // dijawab Ya/Tidak. Disalin ke bank soal sendiri supaya Kelola Soal tidak mengubah checklist Perkembangan.
    public function run(): void
    {
        // Jangan hapus-isi ulang: FK hasil_kuesioner_detail.soal_id cascadeOnDelete
        // akan ikut menghapus jawaban responden. Setelah data masuk, soal dikelola via Kelola Soal.
        if (KuesionerSoal::exists()) {
            return;
        }

        foreach (ItemPerkembanganSeeder::ITEM as $kelompok => $daftar) {
            foreach ($daftar as $i => [$aspek, $pertanyaan]) {
                KuesionerSoal::create([
                    'kelompok_usia' => $kelompok,
                    'aspek' => $aspek,
                    'pertanyaan' => $pertanyaan,
                    'urutan' => $i + 1,
                ]);
            }
        }
    }
}
