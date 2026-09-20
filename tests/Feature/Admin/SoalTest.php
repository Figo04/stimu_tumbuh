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
            'tipe' => 'pengetahuan', 'pertanyaan' => 'Soal uji', 'jawaban_benar' => 'B', 'urutan' => 1,
        ]);
    }

    /** Soal sudah dijawab satu responden → dipakai untuk menguji guard hapus. */
    private function jawab(KuesionerSoal $soal): void
    {
        $hasil = HasilKuesioner::create([
            'user_id' => User::factory()->create()->id, 'tipe_sesi' => 'pre', 'submitted_at' => now(),
        ]);
        HasilKuesionerDetail::create([
            'hasil_kuesioner_id' => $hasil->id, 'soal_id' => $soal->id, 'jawaban_responden' => 'B',
        ]);
    }

    public function test_admin_bisa_menambah_soal_pengetahuan(): void
    {
        $this->actingAs($this->admin(), 'admin')
            ->post(route('admin.soal.store'), [
                'tipe' => 'pengetahuan', 'pertanyaan' => 'ASI eksklusif diberikan 6 bulan.',
                'jawaban_benar' => 'B', 'reverse_scored' => '1', 'urutan' => 3,
            ])
            ->assertRedirect(route('admin.soal.index'));

        $soal = KuesionerSoal::sole();
        $this->assertSame('B', $soal->jawaban_benar);
        // reverse_scored hanya berlaku untuk sikap — dikirim pun diabaikan (PRD §5).
        $this->assertFalse($soal->reverse_scored);
    }

    public function test_soal_sikap_disimpan_tanpa_jawaban_benar_dan_bisa_reverse_scored(): void
    {
        $this->actingAs($this->admin(), 'admin')
            ->post(route('admin.soal.store'), [
                'tipe' => 'sikap', 'pertanyaan' => 'Stimulasi itu merepotkan.',
                'jawaban_benar' => 'B', 'reverse_scored' => '1', 'urutan' => 1,
            ])
            ->assertRedirect(route('admin.soal.index'));

        $soal = KuesionerSoal::sole();
        $this->assertNull($soal->jawaban_benar);
        $this->assertTrue($soal->reverse_scored);
    }

    public function test_soal_pengetahuan_wajib_punya_jawaban_benar(): void
    {
        $this->actingAs($this->admin(), 'admin')
            ->post(route('admin.soal.store'), ['tipe' => 'pengetahuan', 'pertanyaan' => 'Tanpa kunci.', 'urutan' => 1])
            ->assertSessionHasErrors('jawaban_benar');

        $this->assertSame(0, KuesionerSoal::count());
    }

    public function test_admin_bisa_mengubah_soal_tapi_tipenya_tetap(): void
    {
        $soal = $this->soal();

        $this->actingAs($this->admin(), 'admin')
            ->put(route('admin.soal.update', $soal), [
                'tipe' => 'sikap', 'pertanyaan' => 'Pertanyaan diperbaiki.', 'jawaban_benar' => 'S', 'urutan' => 2,
            ])
            ->assertRedirect(route('admin.soal.index'));

        $soal->refresh();
        $this->assertSame('Pertanyaan diperbaiki.', $soal->pertanyaan);
        $this->assertSame('S', $soal->jawaban_benar);
        // Tipe tidak ikut berubah: jawaban B/S yang sudah masuk tak bisa diskor sebagai skala sikap.
        $this->assertSame('pengetahuan', $soal->tipe);
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
