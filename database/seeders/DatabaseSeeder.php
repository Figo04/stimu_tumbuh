<?php

namespace Database\Seeders;

use App\Models\Anak;
use App\Models\Materi;
use App\Services\UsiaAnakService;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([AdminSeeder::class, KuesionerSoalSeeder::class]);

        User::factory()->has(Anak::factory(), 'anak')->create([
            'nama' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // [PLACEHOLDER] satu materi per aspek per kelompok usia untuk uji tampilan; hapus saat MateriSeeder (Sesi 15) masuk.
        foreach (UsiaAnakService::KELOMPOK_USIA as $kelompok) {
            foreach (Materi::ASPEK as $aspek => $label) {
                Materi::firstOrCreate(['slug' => "placeholder-{$kelompok}-{$aspek}"], [
                    'judul' => "[PLACEHOLDER] {$label} {$kelompok} bulan",
                    'aspek' => $aspek,
                    'kelompok_usia' => $kelompok,
                    'urutan' => 1,
                    'konten_view' => 'materi.placeholder',
                ]);
            }
        }
    }
}
