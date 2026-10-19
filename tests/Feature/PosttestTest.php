<?php

namespace Tests\Feature;

use App\Models\Anak;
use App\Models\HasilKuesioner;
use App\Models\KuesionerSoal;
use App\Models\Materi;
use App\Models\ProgressMateri;
use App\Models\User;
use Database\Seeders\KuesionerSoalSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosttestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(KuesionerSoalSeeder::class);
    }

    private function jawabanLengkap(string $kelompokUsia = '12-18', string $jawaban = '1'): array
    {
        return KuesionerSoal::where('kelompok_usia', $kelompokUsia)->pluck('id')
            ->mapWithKeys(fn ($id) => [$id => $jawaban])->all();
    }

    /** Responden yang sudah pre-test, anak 13 bulan (kelompok 12-18), dengan satu materi belum selesai. */
    private function responden(): User
    {
        $user = User::factory()->create();
        Anak::factory()->create(['user_id' => $user->id, 'tanggal_lahir' => now()->subMonthsNoOverflow(13)->toDateString()]);
        HasilKuesioner::create(['user_id' => $user->id, 'tipe_sesi' => 'pre', 'kelompok_usia' => '12-18', 'submitted_at' => now()]);
        Materi::factory()->create(['kelompok_usia' => '12-18']);

        return $user;
    }

    private function selesaikanSemuaMateri(User $user): void
    {
        foreach (Materi::where('kelompok_usia', $user->anak->kelompok_usia)->get() as $materi) {
            ProgressMateri::create([
                'user_id' => $user->id,
                'materi_id' => $materi->id,
                'materi_selesai' => true,
                'materi_selesai_at' => now(),
            ]);
        }
    }

    public function test_posttest_terkunci_sampai_semua_materi_selesai(): void
    {
        $user = $this->responden();

        $this->actingAs($user)->get('/posttest')->assertRedirect(route('materi.index'));
        $this->actingAs($user)->post('/posttest', ['jawaban' => $this->jawabanLengkap()])
            ->assertRedirect(route('materi.index'));

        $this->assertDatabaseMissing('hasil_kuesioner', ['tipe_sesi' => 'post']);
        // Tab terkunci membuka halaman status, bukan form post-test.
        $this->actingAs($user)->get(route('materi.index'))->assertSee('Post-test terkunci')
            ->assertSee(route('tes'), false)->assertDontSee(route('posttest'), false);
    }

    public function test_halaman_status_tes_terkunci_menampilkan_progres_materi_tanpa_skor(): void
    {
        $user = $this->responden();
        $user->hasilKuesioner()->update(['skor_total' => 77.77]);

        $this->actingAs($user)->get(route('tes'))->assertOk()
            ->assertSee('Post-test belum terbuka')
            ->assertSeeInOrder(['Materi selesai', '0 dari 1'])
            ->assertSee('Terkunci')
            ->assertDontSee('77.77');
    }

    public function test_halaman_status_tes_mengarahkan_bila_ada_tes_yang_bisa_diisi(): void
    {
        $belumPre = User::factory()->has(Anak::factory(), 'anak')->create();
        $this->actingAs($belumPre)->get(route('tes'))->assertRedirect(route('pretest'));

        $siapPost = $this->responden();
        $this->selesaikanSemuaMateri($siapPost);
        $this->actingAs($siapPost)->get(route('tes'))->assertRedirect(route('posttest'));
    }

    public function test_halaman_status_tes_setelah_semua_tes_dikirim(): void
    {
        $user = $this->responden();
        $this->selesaikanSemuaMateri($user);
        HasilKuesioner::create(['user_id' => $user->id, 'tipe_sesi' => 'post', 'kelompok_usia' => '12-18', 'submitted_at' => now()]);

        $this->actingAs($user)->get(route('tes'))->assertOk()
            ->assertSee('Terima kasih, semua tes sudah dikirim')
            ->assertSee('Dikirim '.now()->translatedFormat('j M Y'))
            ->assertDontSee('Terkunci');
    }

    public function test_belum_pretest_tidak_bisa_membuka_posttest(): void
    {
        $user = User::factory()->has(Anak::factory(), 'anak')->create();

        $this->actingAs($user)->get('/posttest')->assertRedirect(route('pretest'));
        $this->actingAs($user)->post('/posttest', ['jawaban' => $this->jawabanLengkap()])
            ->assertRedirect(route('pretest'));
        $this->assertDatabaseCount('hasil_kuesioner', 0);
    }

    public function test_menu_berganti_ke_posttest_setelah_semua_materi_selesai(): void
    {
        $user = $this->responden();
        $this->selesaikanSemuaMateri($user);

        $this->actingAs($user)->get(route('materi.index'))
            ->assertSee(route('posttest'), false)
            ->assertDontSee('Post-test terkunci');
    }

    public function test_posttest_tersimpan_dengan_skor_dan_tipe_sesi_post(): void
    {
        $user = $this->responden();
        $this->selesaikanSemuaMateri($user);

        $this->actingAs($user)->get('/posttest')->assertOk()->assertSee('Kirim Jawaban');
        $this->actingAs($user)->post('/posttest', ['jawaban' => $this->jawabanLengkap()])
            ->assertRedirect(route('dashboard'));

        $hasil = HasilKuesioner::where('user_id', $user->id)->where('tipe_sesi', 'post')->sole();
        $this->assertNotNull($hasil->submitted_at);
        $this->assertSame('12-18', $hasil->kelompok_usia);
        $this->assertEquals(100, $hasil->skor_total);
        $this->assertCount(17, $hasil->detail);

        // Pre-test lama tidak tersentuh.
        $this->assertDatabaseCount('hasil_kuesioner', 2);
    }

    /** Item pre & post harus sama agar bisa dibandingkan, walau anak sudah naik kelompok usia. */
    public function test_posttest_memakai_kelompok_usia_pretest_walau_anak_naik_kelompok(): void
    {
        $user = $this->responden();
        $user->anak->update(['tanggal_lahir' => now()->subMonthsNoOverflow(19)->toDateString()]);
        Materi::factory()->create(['kelompok_usia' => '18-24']);
        $this->selesaikanSemuaMateri($user->refresh());

        $soal12 = KuesionerSoal::where('kelompok_usia', '12-18')->first();
        $soal18 = KuesionerSoal::where('kelompok_usia', '18-24')->first();
        $this->actingAs($user)->get('/posttest')->assertOk()
            ->assertSee($soal12->pertanyaan)->assertDontSee($soal18->pertanyaan);

        $this->actingAs($user)->post('/posttest', ['jawaban' => $this->jawabanLengkap('18-24')])
            ->assertSessionHasErrors('jawaban');
        $this->actingAs($user)->post('/posttest', ['jawaban' => $this->jawabanLengkap('12-18')])
            ->assertRedirect(route('dashboard'));

        $this->assertSame('12-18', $user->hasilKuesioner()->where('tipe_sesi', 'post')->value('kelompok_usia'));
    }

    public function test_posttest_final_tidak_bisa_dikirim_ulang(): void
    {
        $user = $this->responden();
        $this->selesaikanSemuaMateri($user);
        $this->actingAs($user)->post('/posttest', ['jawaban' => $this->jawabanLengkap()]);

        $this->actingAs($user)->post('/posttest', ['jawaban' => $this->jawabanLengkap(jawaban: '0')])
            ->assertRedirect(route('dashboard'));
        $this->actingAs($user)->get('/posttest')->assertRedirect(route('dashboard'));

        $this->assertDatabaseCount('hasil_kuesioner', 2);
        $this->assertDatabaseMissing('hasil_kuesioner_detail', ['jawaban_responden' => false]);
    }

    public function test_jawaban_posttest_tidak_lengkap_ditolak(): void
    {
        $user = $this->responden();
        $this->selesaikanSemuaMateri($user);

        $jawaban = $this->jawabanLengkap();
        $soal = KuesionerSoal::where('kelompok_usia', '12-18')->first();
        unset($jawaban[$soal->id]);

        $this->actingAs($user)->post('/posttest', ['jawaban' => $jawaban])
            ->assertSessionHasErrors("jawaban.{$soal->id}");
        $this->assertDatabaseMissing('hasil_kuesioner', ['tipe_sesi' => 'post']);
    }
}
