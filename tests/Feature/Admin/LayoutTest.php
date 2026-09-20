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
        // Menu pembanding digeser tiap kali rutenya terdaftar (Responden → Sesi 31).
        $this->assertFalse(\Route::has('admin.soal.index'), 'Sesi 33 sudah jalan — ganti ke menu lain yang masih kosong.');

        $this->actingAs($admin, 'admin')
            ->get('/admin/dashboard')
            ->assertOk()
            // Rute terdaftar → tautan; belum terdaftar → span bertanda "segera".
            ->assertSee('href="'.route('admin.responden.index').'"', false)
            ->assertSee('segera');
    }
}
