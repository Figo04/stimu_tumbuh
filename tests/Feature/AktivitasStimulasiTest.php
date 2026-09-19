<?php

namespace Tests\Feature;

use App\Models\AktivitasStimulasi;
use App\Models\Anak;
use App\Models\HasilKuesioner;
use App\Models\Materi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AktivitasStimulasiTest extends TestCase
{
    use RefreshDatabase;

    private function responden(): User
    {
        $user = User::factory()->create();
        Anak::factory()->create(['user_id' => $user->id, 'tanggal_lahir' => now()->subMonths(7)->toDateString()]);
        HasilKuesioner::create(['user_id' => $user->id, 'tipe_sesi' => 'pre', 'submitted_at' => now()]);

        return $user;
    }

    private function entri(array $ubah = []): array
    {
        return $ubah + [
            'tanggal' => now()->toDateString(),
            'aspek' => 'motorik_halus',
            'jenis_stimulasi' => 'Meremas kertas',
            'durasi_menit' => 15,
            'pelaku' => 'ayah',
            'respons_anak' => 'Anak tertawa',
        ];
    }

    public function test_tambah_berulang_ubah_dan_hapus_entri(): void
    {
        $user = $this->responden();

        $this->actingAs($user)->post(route('aktivitas.store'), $this->entri())->assertRedirect(route('aktivitas.index'));
        $this->actingAs($user)->post(route('aktivitas.store'), $this->entri(['respons_anak' => 'Anak mengantuk']));
        $this->assertSame(2, $user->aktivitasStimulasi()->count());

        $this->actingAs($user)->get(route('aktivitas.index'))->assertOk()->assertSee('Anak tertawa')->assertSee('Anak mengantuk');

        $entri = $user->aktivitasStimulasi()->first();
        $this->actingAs($user)->put(route('aktivitas.update', $entri), $this->entri(['aspek' => 'bicara_bahasa', 'durasi_menit' => 30]))
            ->assertRedirect(route('aktivitas.index'));
        $this->assertSame(30, $entri->fresh()->durasi_menit);
        $this->assertSame('bicara_bahasa', $entri->fresh()->aspek);

        $this->actingAs($user)->delete(route('aktivitas.destroy', $entri))->assertRedirect(route('aktivitas.index'));
        $this->assertSame(1, $user->aktivitasStimulasi()->count());
    }

    public function test_entri_responden_lain_tidak_bisa_diakses(): void
    {
        $user = $this->responden();
        $milikLain = $this->responden()->aktivitasStimulasi()->create($this->entri());

        $this->actingAs($user)->get(route('aktivitas.edit', $milikLain))->assertNotFound();
        $this->actingAs($user)->put(route('aktivitas.update', $milikLain), $this->entri(['durasi_menit' => 99]))->assertNotFound();
        $this->actingAs($user)->delete(route('aktivitas.destroy', $milikLain))->assertNotFound();
        $this->actingAs($user)->get(route('aktivitas.index'))->assertDontSee('Anak tertawa');

        $this->assertSame(15, $milikLain->fresh()->durasi_menit);
    }

    public function test_validasi_tanggal_masa_depan_dan_enum(): void
    {
        $user = $this->responden();

        $this->actingAs($user)->post(route('aktivitas.store'), $this->entri([
            'tanggal' => now()->addDay()->toDateString(),
            'aspek' => 'kognitif',
            'pelaku' => 'nenek',
            'durasi_menit' => 0,
        ]))->assertSessionHasErrors(['tanggal', 'aspek', 'pelaku', 'durasi_menit']);

        $this->assertSame(0, AktivitasStimulasi::count());
    }

    public function test_entri_dari_praktik_aspek_dan_materi_terkunci_saat_diubah(): void
    {
        $user = $this->responden();
        $materi = Materi::factory()->create(['kelompok_usia' => '6-9', 'aspek' => 'sosial_emosional']);
        $entri = $user->aktivitasStimulasi()->create($this->entri(['materi_id' => $materi->id, 'aspek' => 'sosial_emosional']));

        $this->actingAs($user)->put(route('aktivitas.update', $entri), $this->entri(['aspek' => 'motorik_kasar', 'materi_id' => null]))
            ->assertRedirect(route('aktivitas.index'));

        $entri->refresh();
        $this->assertSame('sosial_emosional', $entri->aspek);
        $this->assertSame($materi->id, $entri->materi_id);
        $this->assertSame('Meremas kertas', $entri->jenis_stimulasi);
    }

    public function test_kalender_terkunci_sebelum_pretest(): void
    {
        $user = User::factory()->create();
        Anak::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->get(route('aktivitas.index'))->assertRedirect(route('pretest'));
    }
}
