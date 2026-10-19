<?php

namespace Tests\Feature;

use App\Models\Anak;
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

    /** Responden dengan anak 13 bulan (kelompok 12-18). */
    private function responden(): User
    {
        $user = User::factory()->create();
        Anak::factory()->create(['user_id' => $user->id, 'tanggal_lahir' => now()->subMonthsNoOverflow(13)->toDateString()]);

        return $user;
    }

    private function jawabanLengkap(string $kelompokUsia = '12-18', string $jawaban = '1'): array
    {
        return KuesionerSoal::where('kelompok_usia', $kelompokUsia)->pluck('id')
            ->mapWithKeys(fn ($id) => [$id => $jawaban])->all();
    }

    public function test_dashboard_terkunci_sampai_pretest_dikirim(): void
    {
        $user = $this->responden();

        $this->actingAs($user)->get('/dashboard')->assertRedirect(route('pretest'));
        $this->actingAs($user)->get('/pretest')->assertOk()->assertSee('Kirim Jawaban');
    }

    public function test_pretest_hanya_menampilkan_soal_kelompok_usia_anak(): void
    {
        $soal12 = KuesionerSoal::where('kelompok_usia', '12-18')->first();
        $soal18 = KuesionerSoal::where('kelompok_usia', '18-24')->first();

        $this->actingAs($this->responden())->get('/pretest')->assertOk()
            ->assertSee($soal12->pertanyaan)
            ->assertDontSee($soal18->pertanyaan)
            ->assertSee('17 pertanyaan');
    }

    public function test_dashboard_meneruskan_ke_materi_membawa_pesan_sukses(): void
    {
        $this->actingAs($this->responden())->followingRedirects()
            ->post('/pretest', ['jawaban' => $this->jawabanLengkap()])
            ->assertOk()
            ->assertSee('Materi Stimulasi')
            ->assertSee('Pre-test berhasil dikirim');
    }

    public function test_pretest_tersimpan_dengan_kelompok_usia_dan_skor_per_kategori(): void
    {
        $user = $this->responden();
        // Motorik dijawab Ya semua, kategori lain Tidak.
        $jawaban = KuesionerSoal::where('kelompok_usia', '12-18')->get()
            ->mapWithKeys(fn ($s) => [$s->id => $s->aspek === 'motorik' ? '1' : '0'])->all();

        $this->actingAs($user)->post('/pretest', ['jawaban' => $jawaban])
            ->assertRedirect(route('dashboard'));

        $hasil = HasilKuesioner::where('user_id', $user->id)->sole();
        $this->assertSame('pre', $hasil->tipe_sesi);
        $this->assertSame('12-18', $hasil->kelompok_usia);
        $this->assertNotNull($hasil->submitted_at);
        $this->assertCount(17, $hasil->detail);
        $this->assertSame(7, $hasil->detail->where('jawaban_responden', true)->count());

        // 12–17 bln: 7 item motorik dari 17.
        $this->assertEquals(100, $hasil->skor_motorik);
        $this->assertEquals(0, $hasil->skor_komunikasi);
        $this->assertEquals(41.18, $hasil->skor_total);
        // Tidak lagi dilempar ke pre-test; dashboard meneruskan ke Materi.
        $this->actingAs($user)->get('/dashboard')->assertRedirect(route('materi.index'));
    }

    public function test_jawaban_tidak_lengkap_atau_tidak_valid_ditolak(): void
    {
        $user = $this->responden();
        $jawaban = $this->jawabanLengkap();
        $soal = KuesionerSoal::where('kelompok_usia', '12-18')->first();

        $kurang = $jawaban;
        unset($kurang[$soal->id]);
        $this->actingAs($user)->post('/pretest', ['jawaban' => $kurang])
            ->assertSessionHasErrors("jawaban.{$soal->id}");

        $this->actingAs($user)->post('/pretest', ['jawaban' => [$soal->id => 'Ya'] + $jawaban])
            ->assertSessionHasErrors("jawaban.{$soal->id}");

        // Soal kelompok usia lain ikut dikirim → ditolak.
        $this->actingAs($user)->post('/pretest', ['jawaban' => $jawaban + $this->jawabanLengkap('18-24')])
            ->assertSessionHasErrors('jawaban');

        $this->assertDatabaseCount('hasil_kuesioner', 0);
    }

    public function test_pretest_final_tidak_bisa_dikirim_ulang(): void
    {
        $user = $this->responden();
        $this->actingAs($user)->post('/pretest', ['jawaban' => $this->jawabanLengkap()]);

        $this->actingAs($user)->post('/pretest', ['jawaban' => $this->jawabanLengkap(jawaban: '0')])
            ->assertRedirect(route('dashboard'));
        $this->actingAs($user)->get('/pretest')->assertRedirect(route('dashboard'));

        $this->assertDatabaseCount('hasil_kuesioner', 1);
        $this->assertDatabaseMissing('hasil_kuesioner_detail', ['jawaban_responden' => false]);
    }
}
