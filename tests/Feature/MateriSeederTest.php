<?php

namespace Tests\Feature;

use App\Models\Materi;
use App\Models\ProgressMateri;
use App\Models\User;
use App\Services\UsiaAnakService;
use Database\Seeders\MateriSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MateriSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_tiap_kelompok_usia_terisi_4_aspek(): void
    {
        $this->seed(MateriSeeder::class);

        foreach (UsiaAnakService::KELOMPOK_USIA as $kelompok) {
            $this->assertEqualsCanonicalizing(
                array_keys(Materi::ASPEK),
                Materi::where('kelompok_usia', $kelompok)->distinct()->pluck('aspek')->all(),
                "Kelompok {$kelompok} belum lengkap 4 aspek"
            );
        }

        $this->assertSame(12, Materi::count());
    }

    public function test_seeding_ulang_tidak_menggandakan_materi_maupun_menghapus_progres(): void
    {
        $this->seed(MateriSeeder::class);
        $jumlah = Materi::count();
        $progress = ProgressMateri::create(['user_id' => User::factory()->create()->id, 'materi_id' => Materi::first()->id]);
        $diganti = Materi::where('slug', '12-18-motorik-kasar')->first();
        $this->assertSame('jNXupLa2SNA', $diganti->video_youtube_id);
        $diganti->update(['video_youtube_id' => 'AAAAAAAAAAA']); // diganti admin

        $this->seed(MateriSeeder::class);

        $this->assertSame($jumlah, Materi::count());
        $this->assertModelExists($progress);
        $this->assertSame('AAAAAAAAAAA', $diganti->fresh()->video_youtube_id);
        $this->assertNull(Materi::where('slug', '18-24-motorik-kasar')->value('video_youtube_id'));
    }
}
