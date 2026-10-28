<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Anak;
use App\Models\HasilKuesioner;
use App\Models\HasilKuesionerDetail;
use App\Models\KuesionerSoal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RespondenTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): Admin
    {
        return Admin::create(['nama' => 'Admin', 'email' => 'admin@example.com', 'password' => 'rahasia123']);
    }

    public function test_daftar_responden_menampilkan_data_orang_tua_dan_anak(): void
    {
        $user = User::factory()->create(['nama' => 'Ibu A', 'kecamatan' => 'Ngaglik']);
        Anak::factory()->create([
            'user_id' => $user->id, 'nama_inisial' => 'AZ', 'jenis_kelamin' => 'P',
            'tanggal_lahir' => now()->subMonthsNoOverflow(13),
        ]);

        $this->actingAs($this->admin(), 'admin')->get(route('admin.responden.index'))
            ->assertOk()
            // Usia & kelompok usia lewat UsiaAnakService, bukan kolom DB.
            ->assertSeeInOrder([$user->kode_responden, 'Ibu A', 'Ngaglik', 'AZ', 'P', '13 bln', '12-18 bln']);
    }

    public function test_detail_responden_menampilkan_kondisi_lahir(): void
    {
        $user = User::factory()->create(['pekerjaan' => 'Guru', 'umur' => 29, 'asuransi' => 'BPJS Kesehatan']);
        Anak::factory()->create([
            'user_id' => $user->id, 'tanggal_lahir' => now()->subMonthsNoOverflow(2),
            'bb_lahir_gram' => 2300, 'jenis_persalinan' => 'sc', 'kondisi_lahir' => ['prematur', 'bblr'],
            'anemia_hamil' => true, 'kebiasaan_ibu' => ['merokok', 'alkohol'],
        ]);

        $this->actingAs($this->admin(), 'admin')->get(route('admin.responden.show', $user))
            ->assertOk()
            ->assertSee(['Guru', '29 tahun', 'BPJS Kesehatan'])
            ->assertSee('2.300 gram')
            ->assertSee('Caesar (SC)')
            ->assertSee('Prematur, BBLR (berat lahir rendah)')
            ->assertSee(['Riwayat Kehamilan', 'Anemia', 'Merokok, Konsumsi alkohol']);
    }

    public function test_hasil_test_menampilkan_kelompok_usia_dan_skor_atau_status_belum(): void
    {
        $sudah = User::factory()->create(['nama' => 'Ibu Sudah']);
        $belum = User::factory()->create(['nama' => 'Ibu Belum']);

        HasilKuesioner::create([
            'user_id' => $sudah->id, 'tipe_sesi' => 'pre', 'kelompok_usia' => '18-24',
            'skor_total' => 82.35, 'submitted_at' => now(),
        ]);

        $this->actingAs($this->admin(), 'admin')->get(route('admin.hasil-test.index'))
            ->assertOk()
            ->assertSeeInOrder(['Ibu Sudah', '18-24 bln', '82,35%'])
            // Post-test milik $sudah dan kedua test milik $belum masih kosong.
            ->assertSee('Belum dikerjakan');
    }

    public function test_detail_hasil_test_menampilkan_skor_kategori_dan_jawaban_per_soal(): void
    {
        $user = User::factory()->create();
        $hasil = HasilKuesioner::create([
            'user_id' => $user->id, 'tipe_sesi' => 'pre', 'kelompok_usia' => '12-18',
            'skor_motorik' => 100, 'skor_komunikasi' => 0, 'skor_total' => 50, 'submitted_at' => now(),
        ]);

        $soal = fn ($aspek, $urutan, $pertanyaan) => KuesionerSoal::create([
            'kelompok_usia' => '12-18', 'aspek' => $aspek, 'pertanyaan' => $pertanyaan, 'urutan' => $urutan,
        ]);
        // Dibuat terbalik: tampilan harus urut menurut `urutan`, bukan id.
        $komunikasi = $soal('komunikasi', 2, 'Soal komunikasi uji');
        $motorik = $soal('motorik', 1, 'Soal motorik uji');

        HasilKuesionerDetail::create(['hasil_kuesioner_id' => $hasil->id, 'soal_id' => $komunikasi->id, 'jawaban_responden' => false]);
        HasilKuesionerDetail::create(['hasil_kuesioner_id' => $hasil->id, 'soal_id' => $motorik->id, 'jawaban_responden' => true]);

        $this->actingAs($this->admin(), 'admin')->get(route('admin.hasil-test.show', $user))
            ->assertOk()
            ->assertSeeInOrder(['50,00%', '12-18 bulan', 'Motorik', '100,00%'])
            ->assertSeeInOrder(['Soal motorik uji', 'Ya', 'Soal komunikasi uji', 'Tidak'])
            // Post-test belum dikerjakan → bagiannya tetap tampil dengan status kosong.
            ->assertSee('Belum dikerjakan responden ini.');
    }

    public function test_menu_responden_dan_hasil_test_tertutup_untuk_tamu_dan_orang_tua(): void
    {
        foreach (['admin.responden.index', 'admin.hasil-test.index'] as $rute) {
            $this->get(route($rute))->assertRedirect('/admin');
            $this->actingAs(User::factory()->create())->get(route($rute))->assertRedirect('/admin');
        }
    }

    /** View-only (aturan agent #11): tidak ada rute ubah/hapus responden maupun hasil test. */
    public function test_tidak_ada_rute_ubah_atau_hapus(): void
    {
        foreach (['admin.responden', 'admin.hasil-test'] as $basis) {
            foreach (['create', 'edit', 'store', 'update', 'destroy'] as $aksi) {
                $this->assertFalse(\Route::has("$basis.$aksi"), "Rute $basis.$aksi seharusnya tidak ada.");
            }
        }
    }
}
