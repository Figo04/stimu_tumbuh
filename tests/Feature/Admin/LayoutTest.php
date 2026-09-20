<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
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
        // Seluruh menu sidebar sudah punya rute sejak Sesi 34, jadi komponennya diuji
        // langsung — mekanisme Route::has() tetap dijaga untuk menu yang ditambah nanti.
        $this->assertStringContainsString(
            'href="'.route('admin.responden.index').'"',
            Blade::render('<x-admin-nav-link route="admin.responden.index">Responden</x-admin-nav-link>')
        );

        $this->assertStringContainsString(
            'segera',
            Blade::render('<x-admin-nav-link route="admin.belum-ada.index">Menu Nanti</x-admin-nav-link>')
        );
    }
}
