<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Materi;
use App\Models\ProgressMateri;
use App\Models\User;
use Database\Seeders\MateriSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MateriTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): Admin
    {
        return Admin::create(['nama' => 'Admin', 'email' => 'admin@example.com', 'password' => 'rahasia123']);
    }

    public function test_daftar_materi_dikelompokkan_per_usia_dengan_status_video_dan_progres(): void
    {
        $materi = Materi::factory()->create([
            'judul' => 'Tengkurap Bergantian', 'kelompok_usia' => '3-6', 'aspek' => 'motorik_kasar',
            'video_youtube_id' => null,
        ]);
        $user = User::factory()->create();
        ProgressMateri::create(['user_id' => $user->id, 'materi_id' => $materi->id, 'materi_selesai' => false]);

        $this->actingAs($this->admin(), 'admin')->get(route('admin.materi.index'))
            ->assertOk()
            ->assertSeeInOrder(['Usia 3–6 bulan', 'Motorik Kasar', 'Tengkurap Bergantian', 'belum diisi', '1 responden']);
    }

    public function test_detail_materi_menampilkan_isi_dan_form_video(): void
    {
        $materi = Materi::factory()->create(['video_youtube_id' => 'M7lc1UVf-VE']);

        $this->actingAs($this->admin(), 'admin')->get(route('admin.materi.show', $materi))
            ->assertOk()
            ->assertSee($materi->judul)
            ->assertSee('Isi materi (tampilan bagi orang tua)')
            ->assertSee('youtube-nocookie.com/embed/M7lc1UVf-VE', false);
    }

    public function test_admin_dapat_menyimpan_tautan_youtube_penuh_sebagai_id(): void
    {
        $materi = Materi::factory()->create(['video_youtube_id' => null]);

        $this->actingAs($this->admin(), 'admin')
            ->patch(route('admin.materi.update', $materi), ['video_youtube_id' => 'https://www.youtube.com/watch?v=M7lc1UVf-VE&t=30s'])
            ->assertRedirect(route('admin.materi.show', $materi));

        $this->assertSame('M7lc1UVf-VE', $materi->fresh()->video_youtube_id);
    }

    public function test_video_dapat_dikosongkan_dan_tautan_asing_ditolak(): void
    {
        $materi = Materi::factory()->create(['video_youtube_id' => 'M7lc1UVf-VE']);
        $admin = $this->admin();

        $this->actingAs($admin, 'admin')
            ->patch(route('admin.materi.update', $materi), ['video_youtube_id' => 'https://contoh.test/video'])
            ->assertSessionHasErrors('video_youtube_id');
        $this->assertSame('M7lc1UVf-VE', $materi->fresh()->video_youtube_id);

        $this->actingAs($admin, 'admin')
            ->patch(route('admin.materi.update', $materi), ['video_youtube_id' => ''])
            ->assertSessionHasNoErrors();
        $this->assertNull($materi->fresh()->video_youtube_id);
    }

    public function test_isi_materi_tetap_view_only(): void
    {
        // Aturan agent #11: hanya index, show, dan update video yang boleh ada.
        foreach (['create', 'store', 'edit', 'destroy'] as $aksi) {
            $this->assertFalse(\Route::has("admin.materi.$aksi"), "Rute admin.materi.$aksi tidak boleh ada — materi dihardcode developer.");
        }

        $materi = Materi::factory()->create(['judul' => 'Judul Asli', 'konten_view' => 'materi.placeholder']);

        $this->actingAs($this->admin(), 'admin')
            ->patch(route('admin.materi.update', $materi), [
                'judul' => 'Judul Diretas', 'konten_view' => 'materi.lain', 'video_youtube_id' => 'M7lc1UVf-VE',
            ])->assertSessionHasNoErrors();

        $materi->refresh();
        $this->assertSame('Judul Asli', $materi->judul);
        $this->assertSame('materi.placeholder', $materi->konten_view);
    }

    public function test_seeder_tidak_menimpa_video_yang_diisi_admin(): void
    {
        $this->seed(MateriSeeder::class);
        $materi = Materi::firstOrFail();
        $materi->update(['video_youtube_id' => 'M7lc1UVf-VE']);

        $this->seed(MateriSeeder::class);

        $this->assertSame('M7lc1UVf-VE', $materi->fresh()->video_youtube_id);
    }

    public function test_orang_tua_tidak_bisa_membuka_kelola_materi(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.materi.index'))
            ->assertRedirect(route('admin.login'));
    }
}
