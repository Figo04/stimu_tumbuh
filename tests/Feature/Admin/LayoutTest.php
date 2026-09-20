<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_sidebar_menampilkan_seluruh_menu_data_dan_konten(): void
    {
        $admin = Admin::create(['nama' => 'Admin', 'email' => 'admin@example.com', 'password' => 'rahasia123']);

        $this->actingAs($admin, 'admin')
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertSeeInOrder([
                'Data',
                'Dashboard', 'Responden', 'Hasil Test', 'Aktivitas Stimulasi', 'Perkembangan',
                'Konten',
                'Kelola Soal', 'Kelola Materi',
            ]);
    }

    public function test_menu_tanpa_rute_tidak_dirender_sebagai_tautan(): void
    {
        $admin = Admin::create(['nama' => 'Admin', 'email' => 'admin@example.com', 'password' => 'rahasia123']);
        $this->assertFalse(\Route::has('admin.responden.index'), 'Sesi 31 sudah jalan — perbarui test ini.');

        $this->actingAs($admin, 'admin')
            ->get('/admin/dashboard')
            ->assertOk()
            // Dashboard punya rute → tautan aktif; Responden belum → span "segera".
            ->assertSee('href="'.route('admin.dashboard').'"', false)
            ->assertDontSee('>Responden</a>', false);
    }
}
