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

    private function jawabanLengkap(): array
    {
        return KuesionerSoal::all()
            ->mapWithKeys(fn ($s) => [$s->id => $s->tipe === 'pengetahuan' ? 'B' : 'SS'])
            ->all();
    }

    /** Responden yang sudah pre-test, anak 7 bulan (kelompok 6-9), dengan satu materi belum selesai. */
    private function responden(): User
    {
        $user = User::factory()->create();
        Anak::factory()->create(['user_id' => $user->id, 'tanggal_lahir' => now()->subMonths(7)->toDateString()]);
        HasilKuesioner::create(['user_id' => $user->id, 'tipe_sesi' => 'pre', 'submitted_at' => now()]);
        Materi::factory()->create(['kelompok_usia' => '6-9']);

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
        // Menu masih terkunci, bukan tautan.
        $this->actingAs($user)->get(route('materi.index'))->assertSee('Post-test 🔒');
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

        $this->actingAs($user)->get('/dashboard')
            ->assertSee(route('posttest'), false)
            ->assertDontSee('Post-test 🔒');
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
        $this->assertNotNull($hasil->skor_pengetahuan);
        $this->assertNotNull($hasil->kategori_pengetahuan);
        $this->assertNotNull($hasil->skor_sikap);
        $this->assertCount(KuesionerSoal::count(), $hasil->detail);

        // Pre-test lama tidak tersentuh.
        $this->assertDatabaseCount('hasil_kuesioner', 2);
    }

    public function test_posttest_final_tidak_bisa_dikirim_ulang(): void
    {
        $user = $this->responden();
        $this->selesaikanSemuaMateri($user);
        $this->actingAs($user)->post('/posttest', ['jawaban' => $this->jawabanLengkap()]);

        $jawabanBaru = array_map(fn ($j) => $j === 'B' ? 'S' : 'STS', $this->jawabanLengkap());
        $this->actingAs($user)->post('/posttest', ['jawaban' => $jawabanBaru])
            ->assertRedirect(route('dashboard'));
        $this->actingAs($user)->get('/posttest')->assertRedirect(route('dashboard'));

        $this->assertDatabaseCount('hasil_kuesioner', 2);
        $this->assertDatabaseMissing('hasil_kuesioner_detail', ['jawaban_responden' => 'S']);
        $this->assertDatabaseMissing('hasil_kuesioner_detail', ['jawaban_responden' => 'STS']);
    }

    public function test_jawaban_posttest_tidak_lengkap_ditolak(): void
    {
        $user = $this->responden();
        $this->selesaikanSemuaMateri($user);

        $jawaban = $this->jawabanLengkap();
        $soal = KuesionerSoal::first();
        unset($jawaban[$soal->id]);

        $this->actingAs($user)->post('/posttest', ['jawaban' => $jawaban])
            ->assertSessionHasErrors("jawaban.{$soal->id}");
        $this->assertDatabaseMissing('hasil_kuesioner', ['tipe_sesi' => 'post']);
    }
}
