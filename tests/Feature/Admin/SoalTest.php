<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\HasilKuesioner;
use App\Models\HasilKuesionerDetail;
use App\Models\KuesionerSoal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SoalTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): Admin
    {
        return Admin::create(['nama' => 'Admin', 'email' => 'admin@example.com', 'password' => 'rahasia123']);
    }

    private function soal(array $atribut = []): KuesionerSoal
    {
        return KuesionerSoal::create($atribut + [
            'kelompok_usia' => '12-18', 'aspek' => 'motorik', 'pertanyaan' => 'Soal uji', 'urutan' => 1,
        ]);
    }

    /** Soal sudah dijawab satu responden → dipakai untuk menguji guard hapus. */
    private function jawab(KuesionerSoal $soal): void
    {
        $hasil = HasilKuesioner::create([
            'user_id' => User::factory()->create()->id, 'tipe_sesi' => 'pre', 'kelompok_usia' => '12-18', 'submitted_at' => now(),
        ]);
        HasilKuesionerDetail::create([
            'hasil_kuesioner_id' => $hasil->id, 'soal_id' => $soal->id, 'jawaban_responden' => true,
        ]);
    }

    public function test_admin_bisa_menambah_soal_per_kelompok_usia_dan_kategori(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'admin')
            ->post(route('admin.soal.store'), [
                'kelompok_usia' => '18-24', 'aspek' => 'komunikasi',
                'pertanyaan' => 'Apakah anak menyebut nama benda?', 'urutan' => 3,
            ])
            ->assertRedirect(route('admin.soal.index'));

        $soal = KuesionerSoal::sole();
        $this->assertSame('18-24', $soal->kelompok_usia);
        $this->assertSame('komunikasi', $soal->aspek);

        $this->actingAs($admin, 'admin')->get(route('admin.soal.index'))
            ->assertOk()->assertSeeInOrder(['Usia 18-24 bulan', 'Apakah anak menyebut nama benda?', 'Komunikasi']);
    }

    public function test_kelompok_usia_dan_kategori_wajib_valid(): void
    {
        $this->actingAs($this->admin(), 'admin')
            ->post(route('admin.soal.store'), ['kelompok_usia' => '0-3', 'aspek' => 'motorik_kasar', 'pertanyaan' => 'Soal.', 'urutan' => 1])
            ->assertSessionHasErrors(['kelompok_usia', 'aspek']);

        $this->assertSame(0, KuesionerSoal::count());
    }

    public function test_admin_bisa_mengubah_soal_tapi_kelompok_dan_kategorinya_tetap(): void
    {
        $soal = $this->soal();

        $this->actingAs($this->admin(), 'admin')
            ->put(route('admin.soal.update', $soal), [
                'kelompok_usia' => '24-36', 'aspek' => 'komunikasi', 'pertanyaan' => 'Pertanyaan diperbaiki.', 'urutan' => 2,
            ])
            ->assertRedirect(route('admin.soal.index'));

        $soal->refresh();
        $this->assertSame('Pertanyaan diperbaiki.', $soal->pertanyaan);
        $this->assertEquals(2, $soal->urutan);
        // Tidak ikut berubah: jawaban yang sudah masuk terskor di kelompok & kategori lamanya.
        $this->assertSame('12-18', $soal->kelompok_usia);
        $this->assertSame('motorik', $soal->aspek);
    }

    public function test_soal_yang_belum_dijawab_bisa_dihapus(): void
    {
        $soal = $this->soal();

        $this->actingAs($this->admin(), 'admin')
            ->delete(route('admin.soal.destroy', $soal))
            ->assertRedirect(route('admin.soal.index'));

        $this->assertSame(0, KuesionerSoal::count());
    }

    /** FK cascadeOnDelete → tanpa guard ini, hapus soal ikut menghapus jawaban responden. */
    public function test_soal_yang_sudah_dijawab_tidak_bisa_dihapus(): void
    {
        $soal = $this->soal();
        $this->jawab($soal);

        $this->actingAs($this->admin(), 'admin')
            ->delete(route('admin.soal.destroy', $soal))
            ->assertRedirect(route('admin.soal.index'))
            ->assertSessionHas('galat');

        $this->assertSame(1, KuesionerSoal::count());
        $this->assertSame(1, HasilKuesionerDetail::count());
    }

    public function test_kelola_soal_tertutup_untuk_tamu_dan_orang_tua(): void
    {
        $soal = $this->soal();

        $this->get(route('admin.soal.index'))->assertRedirect('/admin');
        $this->actingAs(User::factory()->create())->get(route('admin.soal.index'))->assertRedirect('/admin');
        $this->actingAs(User::factory()->create())->delete(route('admin.soal.destroy', $soal))->assertRedirect('/admin');

        $this->assertSame(1, KuesionerSoal::count());
    }
}
