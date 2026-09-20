<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\AktivitasStimulasi;
use App\Models\Anak;
use App\Models\Materi;
use App\Models\PenilaianPerkembangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AktivitasPerkembanganTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): Admin
    {
        return Admin::create(['nama' => 'Admin', 'email' => 'admin@example.com', 'password' => 'rahasia123']);
    }

    public function test_rekap_aktivitas_menampilkan_jumlah_durasi_dan_jarak_entri_terakhir(): void
    {
        $aktif = User::factory()->create(['nama' => 'Ibu Aktif']);
        $diam = User::factory()->create(['nama' => 'Ibu Diam']);

        AktivitasStimulasi::create([
            'user_id' => $aktif->id, 'tanggal' => now()->subDays(3), 'aspek' => 'motorik_kasar',
            'jenis_stimulasi' => 'Tengkurap', 'durasi_menit' => 30, 'pelaku' => 'ibu',
        ]);
        // Entri gaya tab Praktik: durasi null → ikut jumlah entri, tidak ikut total durasi.
        AktivitasStimulasi::create([
            'user_id' => $aktif->id, 'tanggal' => now()->subDays(10), 'aspek' => 'bicara_bahasa', 'pelaku' => 'ibu',
        ]);

        $this->actingAs($this->admin(), 'admin')->get(route('admin.aktivitas.index'))
            ->assertOk()
            ->assertSeeInOrder(['Ibu Aktif', '2 entri', '30 menit', '3 hari lalu'])
            ->assertSee('Belum ada entri');

        $this->assertSame(0, $diam->aktivitasStimulasi()->count());
    }

    public function test_riwayat_aktivitas_menampilkan_entri_dan_usia_anak_saat_entri(): void
    {
        $user = User::factory()->create();
        Anak::factory()->create(['user_id' => $user->id, 'tanggal_lahir' => now()->subMonths(8)]);
        $materi = Materi::factory()->create(['judul' => 'Materi Uji']);

        AktivitasStimulasi::create([
            'user_id' => $user->id, 'tanggal' => now()->subMonths(2), 'aspek' => 'motorik_halus',
            'jenis_stimulasi' => 'Meraih mainan', 'durasi_menit' => 15, 'pelaku' => 'ayah',
            'respons_anak' => 'Anak tertawa',
        ]);
        AktivitasStimulasi::create([
            'user_id' => $user->id, 'materi_id' => $materi->id, 'tanggal' => now(),
            'aspek' => 'sosial_emosional', 'pelaku' => 'ibu',
        ]);

        $this->actingAs($this->admin(), 'admin')->get(route('admin.aktivitas.show', $user))
            ->assertOk()
            // Entri terbaru dulu; entri tab Praktik tampil sebagai "Praktik: {judul}".
            ->assertSeeInOrder(['Praktik: Materi Uji', 'Meraih mainan', 'Ayah', 'Anak tertawa'])
            // Usia saat entri dicatat (8 − 2 = 6 bln), bukan usia hari ini (8 bln).
            ->assertSee('6 bln');
    }

    public function test_rekap_perkembangan_menampilkan_jumlah_dan_skor_terakhir(): void
    {
        $user = User::factory()->create(['nama' => 'Ibu Nilai']);
        $anak = Anak::factory()->create(['user_id' => $user->id, 'tanggal_lahir' => now()->subMonths(4)]);

        foreach ([['2026-01-10', 50], ['2026-02-10', 75]] as [$tanggal, $total]) {
            PenilaianPerkembangan::create([
                'user_id' => $user->id, 'anak_id' => $anak->id, 'tanggal_penilaian' => $tanggal,
                'usia_bulan' => 4, 'kelompok_usia' => '3-6',
                'skor_motorik_kasar' => $total, 'skor_motorik_halus' => $total,
                'skor_bicara_bahasa' => $total, 'skor_sosial_emosional' => $total, 'skor_total' => $total,
            ]);
        }

        // Tanpa penilaian sama sekali.
        User::factory()->create(['nama' => 'Ibu Kosong']);

        $this->actingAs($this->admin(), 'admin')->get(route('admin.perkembangan.index'))
            ->assertOk()
            // Skor yang tampil = penilaian terakhir (75), bukan yang pertama (50).
            ->assertSeeInOrder(['Ibu Nilai', '2&times;', '10/02/2026', '75,00%'], false)
            ->assertSee('Belum pernah menilai');
    }

    public function test_riwayat_perkembangan_menampilkan_skor_tiap_aspek_tanpa_jawaban_per_item(): void
    {
        $user = User::factory()->create();
        $anak = Anak::factory()->create(['user_id' => $user->id, 'tanggal_lahir' => now()->subMonths(4)]);

        PenilaianPerkembangan::create([
            'user_id' => $user->id, 'anak_id' => $anak->id, 'tanggal_penilaian' => '2026-03-05',
            'usia_bulan' => 4, 'kelompok_usia' => '3-6',
            'skor_motorik_kasar' => 100, 'skor_motorik_halus' => 66.67,
            'skor_bicara_bahasa' => 33.33, 'skor_sosial_emosional' => 0, 'skor_total' => 50,
        ]);

        $this->actingAs($this->admin(), 'admin')->get(route('admin.perkembangan.show', $user))
            ->assertOk()
            ->assertSeeInOrder(['05/03/2026', '4 bln', '3-6 bln', '100,00%', '66,67%', '33,33%', '0,00%', '50,00%']);
    }

    public function test_menu_aktivitas_dan_perkembangan_tertutup_untuk_tamu_dan_orang_tua(): void
    {
        foreach (['admin.aktivitas.index', 'admin.perkembangan.index'] as $rute) {
            $this->get(route($rute))->assertRedirect('/admin');
            $this->actingAs(User::factory()->create())->get(route($rute))->assertRedirect('/admin');
        }
    }

    /** View-only (aturan agent #11): admin tidak boleh mengubah data penelitian responden. */
    public function test_tidak_ada_rute_ubah_atau_hapus(): void
    {
        foreach (['admin.aktivitas', 'admin.perkembangan'] as $basis) {
            foreach (['create', 'edit', 'store', 'update', 'destroy'] as $aksi) {
                $this->assertFalse(\Route::has("$basis.$aksi"), "Rute $basis.$aksi seharusnya tidak ada.");
            }
        }
    }
}
