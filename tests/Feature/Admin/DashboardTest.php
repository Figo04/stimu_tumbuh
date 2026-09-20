<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\AktivitasStimulasi;
use App\Models\Anak;
use App\Models\HasilKuesioner;
use App\Models\Materi;
use App\Models\PenilaianPerkembangan;
use App\Models\ProgressMateri;
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

    public function test_data_chart_dihitung_terhadap_kelompok_usia_anak_saat_ini(): void
    {
        // Dua materi di kelompok 0-3, satu di 6-9.
        $m1 = Materi::factory()->create(['kelompok_usia' => '0-3', 'aspek' => 'motorik_kasar']);
        $m2 = Materi::factory()->create(['kelompok_usia' => '0-3', 'aspek' => 'motorik_halus']);
        $lama = Materi::factory()->create(['kelompok_usia' => '6-9', 'aspek' => 'motorik_kasar']);

        $responden = function (int $usiaBulan): User {
            $user = User::factory()->create();
            Anak::factory()->create(['user_id' => $user->id, 'tanggal_lahir' => now()->subMonths($usiaBulan)]);

            return $user->load('anak');
        };

        // A: bayi 1 bln, kedua materi 0-3 selesai → "Selesai semua".
        $a = $responden(1);
        foreach ([$m1, $m2] as $m) {
            ProgressMateri::create(['user_id' => $a->id, 'materi_id' => $m->id, 'materi_selesai' => true]);
        }

        // B: bayi 2 bln, tapi yang selesai materi kelompok 6-9 → tetap "Belum mulai".
        $b = $responden(2);
        ProgressMateri::create(['user_id' => $b->id, 'materi_id' => $lama->id, 'materi_selesai' => true]);

        // C: 7 bln, materi 6-9 dibuka tapi belum selesai → "Belum mulai".
        $c = $responden(7);
        ProgressMateri::create(['user_id' => $c->id, 'materi_id' => $lama->id, 'materi_selesai' => false]);

        // Hanya penilaian TERAKHIR tiap responden yang dirata-rata.
        foreach ([[$a, 20], [$a, 80], [$c, 60]] as [$u, $skor]) {
            PenilaianPerkembangan::create([
                'user_id' => $u->id, 'anak_id' => $u->anak->id, 'tanggal_penilaian' => now()->toDateString(),
                'usia_bulan' => 1, 'kelompok_usia' => '0-3',
                'skor_motorik_kasar' => $skor, 'skor_motorik_halus' => $skor,
                'skor_bicara_bahasa' => $skor, 'skor_sosial_emosional' => $skor, 'skor_total' => $skor,
            ]);
        }

        HasilKuesioner::create(['user_id' => $a->id, 'tipe_sesi' => 'pre', 'submitted_at' => now()]);
        HasilKuesioner::create(['user_id' => $a->id, 'tipe_sesi' => 'post', 'submitted_at' => now()]);
        HasilKuesioner::create(['user_id' => $b->id, 'tipe_sesi' => 'pre', 'submitted_at' => now()]);

        $admin = Admin::create(['nama' => 'Admin', 'email' => 'admin@example.com', 'password' => 'rahasia123']);

        $this->actingAs($admin, 'admin')->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewHas('statusTest', ['Belum test' => 1, 'Pre-test saja' => 1, 'Pre + Post' => 1])
            ->assertViewHas('progresMateri', ['Belum mulai' => 2, 'Sebagian' => 0, 'Selesai semua' => 1])
            ->assertViewHas('sebaranUsia', ['0-3 bln' => 2, '3-6 bln' => 0, '6-9 bln' => 1,
                '9-12 bln' => 0, '12-18 bln' => 0, '18-24 bln' => 0, '24-36 bln' => 0])
            // (80 + 60) / 2 = 70 — penilaian pertama A (20) tidak ikut.
            ->assertViewHas('rataSkorAspek', array_fill_keys(array_values(Materi::ASPEK), 70.0));
    }

    public function test_rata_rata_frekuensi_dan_durasi_dihitung_dari_entri_aktivitas(): void
    {
        [$a, $b] = [User::factory()->create(), User::factory()->create()];
        User::factory()->create(); // Tanpa entri → tidak ikut penyebut.

        // A: 2 entri dalam rentang 2 hari (1 minggu aktif) → 2 kali/minggu.
        foreach ([['2026-09-14', 15], ['2026-09-15', 10]] as [$tanggal, $durasi]) {
            AktivitasStimulasi::create([
                'user_id' => $a->id, 'tanggal' => $tanggal, 'aspek' => 'motorik_kasar',
                'jenis_stimulasi' => 'Tengkurap', 'durasi_menit' => $durasi, 'pelaku' => 'ibu',
            ]);
        }

        // B: 2 entri berjarak 8 hari (2 minggu aktif) → 1 kali/minggu; durasi null (pola tab Praktik).
        foreach (['2026-09-01', '2026-09-09'] as $tanggal) {
            AktivitasStimulasi::create([
                'user_id' => $b->id, 'tanggal' => $tanggal, 'aspek' => 'bicara_bahasa', 'pelaku' => 'ibu',
            ]);
        }

        $admin = Admin::create(['nama' => 'Admin', 'email' => 'admin@example.com', 'password' => 'rahasia123']);

        $this->actingAs($admin, 'admin')->get(route('admin.dashboard'))
            ->assertOk()
            // Frekuensi (2 + 1) / 2 = 1,5 · durasi (15 + 10) / 2 = 12,5 (entri B tanpa durasi diabaikan).
            ->assertViewHas('rataStimulasi', ['frekuensi' => 1.5, 'durasi' => 12.5])
            ->assertSeeInOrder(['Rata-rata Frekuensi Stimulasi', '1,5', 'Rata-rata Durasi per Sesi', '12,5']);
    }

    /** Akses tamu/orang tua sudah ditutup 8 test Admin\AuthTest — di sini cukup keadaan kosong. */
    public function test_dashboard_tanpa_data_tidak_error(): void
    {
        $admin = Admin::create(['nama' => 'Admin', 'email' => 'admin@example.com', 'password' => 'rahasia123']);

        $this->actingAs($admin, 'admin')->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Belum ada entri stimulasi')
            // Chart tanpa data tampil sebagai pesan, bukan canvas kosong.
            ->assertSee('Belum ada data.')
            ->assertDontSee('data-chart', false)
            // Kartu rata-rata tanpa entri → "—", bukan 0.
            ->assertViewHas('rataStimulasi', ['frekuensi' => null, 'durasi' => null]);
    }
}
