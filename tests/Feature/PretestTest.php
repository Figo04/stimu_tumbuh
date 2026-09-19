<?php

namespace Tests\Feature;

use App\Models\HasilKuesioner;
use App\Models\KuesionerSoal;
use App\Models\User;
use Database\Seeders\KuesionerSoalSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PretestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(KuesionerSoalSeeder::class);
    }

    private function jawabanLengkap(): array
    {
        return KuesionerSoal::all()
            ->mapWithKeys(fn ($s) => [$s->id => $s->tipe === 'pengetahuan' ? 'B' : 'SS'])
            ->all();
    }

    public function test_dashboard_terkunci_sampai_pretest_dikirim(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/dashboard')->assertRedirect(route('pretest'));
        $this->actingAs($user)->get('/pretest')->assertOk()->assertSee('Kirim Jawaban');
    }

    public function test_pretest_tersimpan_dan_dashboard_terbuka(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/pretest', ['jawaban' => $this->jawabanLengkap()])
            ->assertRedirect(route('dashboard'));

        $hasil = HasilKuesioner::where('user_id', $user->id)->sole();
        $this->assertSame('pre', $hasil->tipe_sesi);
        $this->assertNotNull($hasil->submitted_at);
        $this->assertCount(KuesionerSoal::count(), $hasil->detail);
        $this->actingAs($user)->get('/dashboard')->assertOk();
    }

    public function test_jawaban_tidak_lengkap_atau_tidak_valid_ditolak(): void
    {
        $user = User::factory()->create();
        $jawaban = $this->jawabanLengkap();
        $pengetahuan = KuesionerSoal::where('tipe', 'pengetahuan')->first();
        $sikap = KuesionerSoal::where('tipe', 'sikap')->first();

        $kurang = $jawaban;
        unset($kurang[$pengetahuan->id]);
        $this->actingAs($user)->post('/pretest', ['jawaban' => $kurang])
            ->assertSessionHasErrors("jawaban.{$pengetahuan->id}");

        // Pilihan sikap tidak berlaku untuk soal pengetahuan, dan sebaliknya.
        $salahTipe = [$pengetahuan->id => 'SS', $sikap->id => 'B'] + $jawaban;
        $this->actingAs($user)->post('/pretest', ['jawaban' => $salahTipe])
            ->assertSessionHasErrors(["jawaban.{$pengetahuan->id}", "jawaban.{$sikap->id}"]);

        $this->actingAs($user)->post('/pretest', ['jawaban' => $jawaban + [999999 => 'B']])
            ->assertSessionHasErrors('jawaban');

        $this->assertDatabaseCount('hasil_kuesioner', 0);
    }

    public function test_pretest_final_tidak_bisa_dikirim_ulang(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post('/pretest', ['jawaban' => $this->jawabanLengkap()]);

        $jawabanBaru = array_map(fn ($j) => $j === 'B' ? 'S' : 'STS', $this->jawabanLengkap());
        $this->actingAs($user)->post('/pretest', ['jawaban' => $jawabanBaru])
            ->assertRedirect(route('dashboard'));
        $this->actingAs($user)->get('/pretest')->assertRedirect(route('dashboard'));

        $this->assertDatabaseCount('hasil_kuesioner', 1);
        $this->assertDatabaseMissing('hasil_kuesioner_detail', ['jawaban_responden' => 'S']);
        $this->assertDatabaseMissing('hasil_kuesioner_detail', ['jawaban_responden' => 'STS']);
    }
}
