<?php

namespace Tests\Feature;

use App\Models\Anak;
use App\Models\HasilKuesioner;
use App\Models\Materi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MateriTest extends TestCase
{
    use RefreshDatabase;

    /** Responden yang sudah pre-test, anaknya berusia $usiaBulan bulan. */
    private function responden(int $usiaBulan): User
    {
        $user = User::factory()->create();
        Anak::factory()->create(['user_id' => $user->id, 'tanggal_lahir' => now()->subMonths($usiaBulan)->toDateString()]);
        HasilKuesioner::create(['user_id' => $user->id, 'tipe_sesi' => 'pre', 'submitted_at' => now()]);

        return $user;
    }

    public function test_materi_terkunci_sampai_pretest_dikirim(): void
    {
        $user = User::factory()->has(Anak::factory(), 'anak')->create();

        $this->actingAs($user)->get('/materi')->assertRedirect(route('pretest'));
    }

    public function test_hanya_materi_kelompok_usia_anak_yang_tampil_per_aspek(): void
    {
        $user = $this->responden(7);
        Materi::factory()->create(['judul' => 'Belajar Duduk', 'aspek' => 'motorik_kasar', 'kelompok_usia' => '6-9']);
        Materi::factory()->create(['judul' => 'Meraih Mainan', 'aspek' => 'motorik_halus', 'kelompok_usia' => '6-9']);
        Materi::factory()->create(['judul' => 'Belajar Berjalan', 'aspek' => 'motorik_kasar', 'kelompok_usia' => '12-18']);

        $this->actingAs($user)->get('/materi')->assertOk()
            ->assertSeeInOrder(['Motorik Kasar', 'Belajar Duduk', 'Motorik Halus', 'Meraih Mainan'])
            ->assertDontSee('Belajar Berjalan')
            ->assertDontSee('Bicara &amp; Bahasa', false);
    }

    public function test_materi_kelompok_usia_lain_404(): void
    {
        $user = $this->responden(7);
        $sendiri = Materi::factory()->create(['kelompok_usia' => '6-9']);
        $lain = Materi::factory()->create(['kelompok_usia' => '12-18']);

        $this->actingAs($user)->get(route('materi.show', $sendiri))->assertOk()
            ->assertSee($sendiri->judul)->assertSee('[PLACEHOLDER]', false);
        $this->actingAs($user)->get(route('materi.show', $lain))->assertNotFound();
    }

    public function test_materi_ikut_berganti_saat_anak_bertambah_usia(): void
    {
        $user = $this->responden(8);
        $awal = Materi::factory()->create(['kelompok_usia' => '6-9']);
        $berikut = Materi::factory()->create(['kelompok_usia' => '9-12']);

        $this->actingAs($user)->get('/materi')->assertSee($awal->judul)->assertDontSee($berikut->judul);

        $this->travel(1)->months();

        $this->actingAs($user)->get('/materi')->assertSee($berikut->judul)->assertDontSee($awal->judul);
    }
}
