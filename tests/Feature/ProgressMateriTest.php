<?php

namespace Tests\Feature;

use App\Models\Anak;
use App\Models\HasilKuesioner;
use App\Models\Materi;
use App\Models\ProgressMateri;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgressMateriTest extends TestCase
{
    use RefreshDatabase;

    /** Responden yang sudah pre-test, anaknya berusia 7 bulan (kelompok 6-9). */
    private function responden(): User
    {
        $user = User::factory()->create();
        Anak::factory()->create(['user_id' => $user->id, 'tanggal_lahir' => now()->subMonths(7)->toDateString()]);
        HasilKuesioner::create(['user_id' => $user->id, 'tipe_sesi' => 'pre', 'submitted_at' => now()]);

        return $user;
    }

    public function test_membuka_materi_mencatat_progress_tanpa_selesai(): void
    {
        $user = $this->responden();
        $materi = Materi::factory()->create(['kelompok_usia' => '6-9']);

        $this->actingAs($user)->get(route('materi.show', $materi))->assertOk()->assertSee('Tandai selesai dibaca');
        $this->actingAs($user)->get(route('materi.show', $materi))->assertOk();

        $this->assertSame(1, ProgressMateri::count());
        $this->assertFalse($user->materiSelesai($materi));
    }

    public function test_tandai_selesai_satu_arah_dan_tidak_menggandakan_baris(): void
    {
        $user = $this->responden();
        $materi = Materi::factory()->create(['kelompok_usia' => '6-9']);

        $this->actingAs($user)->post(route('materi.selesai', $materi))->assertRedirect(route('materi.show', $materi));
        $pertama = ProgressMateri::sole()->materi_selesai_at;

        $this->travel(1)->days();
        $this->actingAs($user)->post(route('materi.selesai', $materi));

        $this->assertTrue($user->materiSelesai($materi));
        $this->assertEquals($pertama, ProgressMateri::sole()->materi_selesai_at);

        $this->actingAs($user)->get(route('materi.index'))->assertSee('1 dari 1', false)->assertSee('✓ Selesai');
    }

    public function test_tidak_bisa_menandai_materi_kelompok_usia_lain(): void
    {
        $user = $this->responden();
        $lain = Materi::factory()->create(['kelompok_usia' => '12-18']);

        $this->actingAs($user)->post(route('materi.selesai', $lain))->assertNotFound();
        $this->assertSame(0, ProgressMateri::count());
    }

    public function test_belum_pretest_tidak_bisa_menandai_selesai(): void
    {
        $user = User::factory()->has(Anak::factory(), 'anak')->create();
        $materi = Materi::factory()->create(['kelompok_usia' => $user->anak->kelompok_usia]);

        $this->actingAs($user)->post(route('materi.selesai', $materi))->assertRedirect(route('pretest'));
        $this->assertSame(0, ProgressMateri::count());
    }

    public function test_membuka_video_mencatat_ditonton_sekali(): void
    {
        $user = $this->responden();
        $materi = Materi::factory()->create(['kelompok_usia' => '6-9', 'video_youtube_id' => 'abc123XYZ_-']);

        $this->actingAs($user)->get(route('materi.show', $materi))->assertSee('Tonton video')->assertSee('youtube-nocookie.com/embed/abc123XYZ_-', false);

        $this->actingAs($user)->post(route('materi.video', $materi))->assertNoContent();
        $pertama = ProgressMateri::sole()->video_ditonton_at;

        $this->travel(1)->days();
        $this->actingAs($user)->post(route('materi.video', $materi))->assertNoContent();

        $progress = ProgressMateri::sole();
        $this->assertTrue($progress->video_ditonton);
        $this->assertEquals($pertama, $progress->video_ditonton_at);
        $this->assertFalse($progress->materi_selesai, 'video tidak menandai materi selesai');
    }

    public function test_video_tidak_dicatat_untuk_materi_usia_lain_atau_tanpa_video(): void
    {
        $user = $this->responden();
        $lain = Materi::factory()->create(['kelompok_usia' => '12-18', 'video_youtube_id' => 'abc123XYZ_-']);
        $tanpaVideo = Materi::factory()->create(['kelompok_usia' => '6-9', 'video_youtube_id' => null]);

        $this->actingAs($user)->post(route('materi.video', $lain))->assertNotFound();
        $this->actingAs($user)->post(route('materi.video', $tanpaVideo))->assertNotFound();
        $this->actingAs($user)->get(route('materi.show', $tanpaVideo))->assertDontSee('Tonton video');

        $this->assertSame(0, ProgressMateri::where('video_ditonton', true)->count());
    }

    public function test_semua_materi_selesai_hanya_bila_seluruh_materi_kelompok_usia_selesai(): void
    {
        $user = $this->responden();
        $this->assertFalse($user->semuaMateriSelesai(), 'tanpa materi tidak dianggap selesai');

        [$a, $b] = Materi::factory()->count(2)->create(['kelompok_usia' => '6-9']);
        $lain = Materi::factory()->create(['kelompok_usia' => '12-18']);
        ProgressMateri::create(['user_id' => $user->id, 'materi_id' => $lain->id, 'materi_selesai' => true]);

        $this->actingAs($user)->post(route('materi.selesai', $a));
        $this->assertFalse($user->semuaMateriSelesai());

        $this->actingAs($user)->post(route('materi.selesai', $b));
        $this->assertTrue($user->semuaMateriSelesai());
    }
}
