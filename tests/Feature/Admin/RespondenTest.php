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
            'tanggal_lahir' => now()->subMonths(7),
        ]);

        $this->actingAs($this->admin(), 'admin')->get(route('admin.responden.index'))
            ->assertOk()
            // Usia & kelompok usia lewat UsiaAnakService, bukan kolom DB.
            ->assertSeeInOrder([$user->kode_responden, 'Ibu A', 'Ngaglik', 'AZ', 'P', '7 bln', '6-9 bln']);
    }

    public function test_detail_responden_menampilkan_kondisi_lahir(): void
    {
        $user = User::factory()->create(['pekerjaan' => 'Guru']);
        Anak::factory()->create([
            'user_id' => $user->id, 'tanggal_lahir' => now()->subMonths(2),
            'bb_lahir_gram' => 2300, 'jenis_persalinan' => 'sc', 'kondisi_lahir' => 'bblr',
        ]);

        $this->actingAs($this->admin(), 'admin')->get(route('admin.responden.show', $user))
            ->assertOk()
            ->assertSee('Guru')
            ->assertSee('2.300 gram')
            ->assertSee('Caesar (SC)')
            ->assertSee('BBLR (berat lahir rendah)');
    }

    public function test_hasil_test_menampilkan_skor_dan_kategori_atau_status_belum(): void
    {
        $sudah = User::factory()->create(['nama' => 'Ibu Sudah']);
        $belum = User::factory()->create(['nama' => 'Ibu Belum']);

        HasilKuesioner::create([
            'user_id' => $sudah->id, 'tipe_sesi' => 'pre', 'skor_pengetahuan' => 80,
            'kategori_pengetahuan' => 'Baik', 'skor_sikap' => 32, 'submitted_at' => now(),
        ]);

        $this->actingAs($this->admin(), 'admin')->get(route('admin.hasil-test.index'))
            ->assertOk()
            ->assertSeeInOrder(['Ibu Sudah', '80,00%', 'Baik', '32,00'])
            // Post-test milik $sudah dan kedua test milik $belum masih kosong.
            ->assertSee('Belum dikerjakan');
    }

    public function test_detail_hasil_test_menampilkan_jawaban_per_soal(): void
    {
        $user = User::factory()->create();
        $hasil = HasilKuesioner::create([
            'user_id' => $user->id, 'tipe_sesi' => 'pre', 'skor_pengetahuan' => 50,
            'kategori_pengetahuan' => 'Kurang', 'skor_sikap' => 6, 'submitted_at' => now(),
        ]);

        $pengetahuan = KuesionerSoal::create([
            'tipe' => 'pengetahuan', 'pertanyaan' => 'Soal pengetahuan uji', 'jawaban_benar' => 'B', 'urutan' => 1,
        ]);
        $sikap = KuesionerSoal::create([
            'tipe' => 'sikap', 'pertanyaan' => 'Soal sikap uji', 'reverse_scored' => true, 'urutan' => 1,
        ]);

        // Pengetahuan dijawab salah; sikap dijawab "S" = Setuju (bukan "Salah").
        HasilKuesionerDetail::create(['hasil_kuesioner_id' => $hasil->id, 'soal_id' => $pengetahuan->id, 'jawaban_responden' => 'S']);
        HasilKuesionerDetail::create(['hasil_kuesioner_id' => $hasil->id, 'soal_id' => $sikap->id, 'jawaban_responden' => 'S']);

        $this->actingAs($this->admin(), 'admin')->get(route('admin.hasil-test.show', $user))
            ->assertOk()
            ->assertSeeInOrder(['Soal pengetahuan uji', 'Salah', 'Soal sikap uji', 'Setuju'])
            ->assertSee('(reverse)')
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
