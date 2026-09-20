<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\AktivitasStimulasi;
use App\Models\HasilKuesioner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_stat_cards_dan_aktivitas_terbaru_menampilkan_angka_yang_benar(): void
    {
        // A: pre + post + stimulasi · B: pre saja · C: belum apa-apa.
        [$a, $b] = [User::factory()->create(['nama' => 'Ibu A']), User::factory()->create()];
        User::factory()->create();

        HasilKuesioner::create(['user_id' => $a->id, 'tipe_sesi' => 'pre', 'submitted_at' => now()]);
        HasilKuesioner::create(['user_id' => $a->id, 'tipe_sesi' => 'post', 'submitted_at' => now()]);
        HasilKuesioner::create(['user_id' => $b->id, 'tipe_sesi' => 'pre', 'submitted_at' => now()]);

        AktivitasStimulasi::create([
            'user_id' => $a->id, 'tanggal' => now()->subDay()->toDateString(),
            'aspek' => 'motorik_halus', 'jenis_stimulasi' => 'Meremas kertas', 'durasi_menit' => 15, 'pelaku' => 'ibu',
        ]);
        AktivitasStimulasi::create([
            'user_id' => $a->id, 'tanggal' => now()->toDateString(),
            'aspek' => 'bicara_bahasa', 'jenis_stimulasi' => 'Bernyanyi', 'durasi_menit' => 10, 'pelaku' => 'ayah',
        ]);

        $admin = Admin::create(['nama' => 'Admin', 'email' => 'admin@example.com', 'password' => 'rahasia123']);

        $this->actingAs($admin, 'admin')->get(route('admin.dashboard'))
            ->assertOk()
            // Total 3 · pre 2 · post 1 · lengkap 1 · sudah stimulasi 1 · belum 2.
            ->assertSeeInOrder(['Total Responden', '3', 'Sudah Pre-test', '2', 'Sudah Post-test', '1',
                'Pre + Post Lengkap', '1', 'Sudah Melakukan Stimulasi', '1', 'Belum Melakukan Stimulasi', '2'])
            // Entri terbaru di atas, lengkap dengan kode responden.
            ->assertSeeInOrder(['Aktivitas Terbaru', $a->kode_responden, 'Bernyanyi', 'Meremas kertas'])
            ->assertSee('Ibu A');
    }

    /** Akses tamu/orang tua sudah ditutup 8 test Admin\AuthTest — di sini cukup keadaan kosong. */
    public function test_dashboard_tanpa_data_tidak_error(): void
    {
        $admin = Admin::create(['nama' => 'Admin', 'email' => 'admin@example.com', 'password' => 'rahasia123']);

        $this->actingAs($admin, 'admin')->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Belum ada entri stimulasi');
    }
}
