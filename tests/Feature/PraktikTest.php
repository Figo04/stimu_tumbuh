<?php

namespace Tests\Feature;

use App\Models\AktivitasStimulasi;
use App\Models\Anak;
use App\Models\HasilKuesioner;
use App\Models\Materi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PraktikTest extends TestCase
{
    use RefreshDatabase;

    /** Responden (ayah) yang sudah pre-test, anaknya berusia 7 bulan (kelompok 6-9). */
    private function responden(): User
    {
        $user = User::factory()->create(['hubungan_dengan_anak' => 'ayah']);
        Anak::factory()->create(['user_id' => $user->id, 'tanggal_lahir' => now()->subMonths(7)->toDateString()]);
        HasilKuesioner::create(['user_id' => $user->id, 'tipe_sesi' => 'pre', 'submitted_at' => now()]);

        return $user;
    }

    public function test_praktik_terkunci_sebelum_materi_selesai(): void
    {
        $user = $this->responden();
        $materi = Materi::factory()->create(['kelompok_usia' => '6-9']);

        $this->actingAs($user)->get(route('materi.show', $materi))->assertDontSee('Sudah saya praktikkan');
        $this->actingAs($user)->post(route('materi.praktik', $materi), ['tanggal' => now()->toDateString()])->assertForbidden();

        $this->assertSame(0, AktivitasStimulasi::count());
    }

    public function test_praktik_boleh_diisi_berulang_termasuk_tanggal_sama(): void
    {
        $user = $this->responden();
        $materi = Materi::factory()->create(['kelompok_usia' => '6-9', 'aspek' => 'bicara_bahasa']);
        $this->actingAs($user)->post(route('materi.selesai', $materi));

        $hariIni = now()->toDateString();
        $this->actingAs($user)->post(route('materi.praktik', $materi), ['tanggal' => $hariIni, 'respons_anak' => 'Anak tersenyum'])
            ->assertRedirect(route('materi.show', $materi));
        $this->actingAs($user)->post(route('materi.praktik', $materi), ['tanggal' => $hariIni]);

        $this->assertSame(2, AktivitasStimulasi::count());
        $entri = AktivitasStimulasi::first();
        $this->assertSame($materi->id, $entri->materi_id);
        $this->assertSame('bicara_bahasa', $entri->aspek);
        $this->assertSame('ayah', $entri->pelaku);

        $this->actingAs($user)->get(route('materi.show', $materi))->assertSee('Sudah saya praktikkan')->assertSee('Anak tersenyum');
    }

    public function test_praktik_menolak_tanggal_masa_depan_dan_materi_usia_lain(): void
    {
        $user = $this->responden();
        $materi = Materi::factory()->create(['kelompok_usia' => '6-9']);
        $lain = Materi::factory()->create(['kelompok_usia' => '12-18']);
        $this->actingAs($user)->post(route('materi.selesai', $materi));

        $this->actingAs($user)->post(route('materi.praktik', $materi), ['tanggal' => now()->addDay()->toDateString()])
            ->assertSessionHasErrors('tanggal');
        $this->actingAs($user)->post(route('materi.praktik', $lain), ['tanggal' => now()->toDateString()])->assertNotFound();

        $this->assertSame(0, AktivitasStimulasi::count());
    }
}
