<?php

namespace Database\Seeders;

use App\Models\ItemPerkembangan;
use App\Models\Materi;
use App\Services\UsiaAnakService;
use Illuminate\Database\Seeder;

class ItemPerkembanganSeeder extends Seeder
{
    // ponytail: PLACEHOLDER — dokumen checklist perkembangan klien belum masuk. Ganti dengan
    // array [kelompok_usia => [aspek => [pertanyaan, ...]]] resmi, lalu migrate:fresh --seed.
    private const JUMLAH_PLACEHOLDER = 3;

    public function run(): void
    {
        foreach (UsiaAnakService::KELOMPOK_USIA as $kelompok) {
            foreach (Materi::ASPEK as $aspek => $label) {
                // Jangan hapus-isi ulang: FK penilaian_detail.item_id akan ikut menghapus
                // riwayat penilaian responden. Dicek per kombinasi, supaya yang kosong tetap bisa diisi.
                if (ItemPerkembangan::where(['kelompok_usia' => $kelompok, 'aspek' => $aspek])->exists()) {
                    continue;
                }

                for ($i = 1; $i <= self::JUMLAH_PLACEHOLDER; $i++) {
                    ItemPerkembangan::create([
                        'kelompok_usia' => $kelompok,
                        'aspek' => $aspek,
                        'pertanyaan' => "[PLACEHOLDER] Item {$label} {$kelompok} bulan no. {$i}",
                        'urutan' => $i,
                    ]);
                }
            }
        }
    }
}
