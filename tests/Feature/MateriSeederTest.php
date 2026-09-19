<?php

namespace Tests\Feature;

use App\Models\Materi;
use App\Models\ProgressMateri;
use App\Models\User;
use Database\Seeders\MateriSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MateriSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_tiap_kelompok_usia_terisi_4_aspek_dan_semua_view_bisa_dirender(): void
    {
        $this->seed(MateriSeeder::class);

        foreach (['0-3', '3-6'] as $kelompok) {
            $this->assertEqualsCanonicalizing(
                array_keys(Materi::ASPEK),
                Materi::where('kelompok_usia', $kelompok)->distinct()->pluck('aspek')->all(),
                "Kelompok {$kelompok} belum lengkap 4 aspek"
            );
        }

        Materi::all()->each(fn (Materi $m) => $this->assertNotEmpty(view($m->konten_view)->render(), $m->slug));
    }

    public function test_seeding_ulang_tidak_menggandakan_materi_maupun_menghapus_progres(): void
    {
        $this->seed(MateriSeeder::class);
        $jumlah = Materi::count();
        $progress = ProgressMateri::create(['user_id' => User::factory()->create()->id, 'materi_id' => Materi::first()->id]);

        $this->seed(MateriSeeder::class);

        $this->assertSame($jumlah, Materi::count());
        $this->assertModelExists($progress);
    }
}
