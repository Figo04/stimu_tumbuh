<?php

namespace Tests\Feature;

use App\Models\Anak;
use App\Models\HasilKuesioner;
use App\Models\ItemPerkembangan;
use App\Models\PenilaianPerkembangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerkembanganTest extends TestCase
{
    use RefreshDatabase;

    private function responden(): User
    {
        $user = User::factory()->create();
        Anak::factory()->create(['user_id' => $user->id, 'tanggal_lahir' => now()->subMonths(7)->toDateString()]);
        HasilKuesioner::create(['user_id' => $user->id, 'tipe_sesi' => 'pre', 'submitted_at' => now()]);

        return $user;
    }

    /** 2 item per aspek untuk kelompok 6-9. */
    private function item(string $kelompok = '6-9')
    {
        return collect(['motorik_kasar', 'motorik_halus', 'bicara_bahasa', 'sosial_emosional'])
            ->flatMap(fn ($aspek) => collect([1, 2])->map(fn ($n) => ItemPerkembangan::create([
                'aspek' => $aspek, 'kelompok_usia' => $kelompok, 'pertanyaan' => "Item $aspek $kelompok $n", 'urutan' => $n,
            ])));
    }

    public function test_simpan_penilaian_skor_otomatis_dan_boleh_diulang(): void
    {
        $user = $this->responden();
        $item = $this->item();
        $this->item('0-3');

        $this->actingAs($user)->get(route('perkembangan.index'))->assertOk()
            ->assertSee('Item motorik_kasar 6-9 1')
            ->assertDontSee('Item motorik_kasar 0-3 1');

        // Motorik kasar 2/2, motorik halus 1/2, sisanya 0 → total 3/8.
        $jawaban = $item->mapWithKeys(fn ($i) => [$i->id => '0'])->all();
        $jawaban[$item[0]->id] = $jawaban[$item[1]->id] = $jawaban[$item[2]->id] = '1';

        $this->actingAs($user)->post(route('perkembangan.store'), ['jawaban' => $jawaban])
            ->assertRedirect(route('perkembangan.index'));

        $p = $user->penilaianPerkembangan()->first();
        $this->assertSame('100.00', $p->skor_motorik_kasar);
        $this->assertSame('50.00', $p->skor_motorik_halus);
        $this->assertSame('0.00', $p->skor_bicara_bahasa);
        $this->assertSame('37.50', $p->skor_total);
        $this->assertSame('6-9', $p->kelompok_usia);
        $this->assertSame(7, $p->usia_bulan);
        $this->assertTrue($p->tanggal_penilaian->isToday());
        $this->assertSame(8, $p->detail()->count());
        $this->assertTrue($p->detail()->where('item_id', $item[0]->id)->value('jawaban'));

        // Berkelanjutan: penilaian ulang menambah entri baru, entri lama tetap.
        $this->actingAs($user)->post(route('perkembangan.store'), ['jawaban' => array_fill_keys(array_keys($jawaban), '1')]);
        $this->assertSame(2, $user->penilaianPerkembangan()->count());
        $this->assertSame('37.50', $p->fresh()->skor_total);

        $this->actingAs($user)->get(route('perkembangan.index'))->assertSee('Total: <strong>100%</strong>', false);
    }

    public function test_riwayat_menampilkan_perubahan_skor_hanya_dalam_kelompok_usia_sama(): void
    {
        $user = $this->responden();
        $this->item();
        $penilaian = fn (User $u, $tanggal, $kelompok, $skor) => $u->penilaianPerkembangan()->create([
            'anak_id' => $u->anak()->first()->id, 'tanggal_penilaian' => $tanggal, 'usia_bulan' => 5, 'kelompok_usia' => $kelompok,
            'skor_motorik_kasar' => $skor[0], 'skor_motorik_halus' => $skor[1], 'skor_bicara_bahasa' => $skor[2],
            'skor_sosial_emosional' => $skor[3], 'skor_total' => $skor[4],
        ]);
        $penilaian($user, today()->subMonths(2), '3-6', [50, 50, 50, 50, 50]);
        $penilaian($user, today()->subMonth(), '6-9', [100, 50, 0, 0, 37.5]);
        $penilaian($user, today(), '6-9', [100, 25, 100, 100, 81.25]);
        $penilaian($this->responden(), '2020-01-01', '6-9', [0, 0, 0, 0, 0]);

        $this->actingAs($user)->get(route('perkembangan.index'))->assertOk()
            ->assertSeeInOrder(['Riwayat penilaian', today()->translatedFormat('j F Y'), today()->subMonth()->translatedFormat('j F Y')])
            ->assertSee('▲ +43.75')   // total 37.5 → 81.25
            ->assertSee('▼ −25')      // motorik halus 50 → 25
            ->assertSee('tetap')      // motorik kasar 100 → 100
            ->assertSee('kelompok usia baru, tidak dibandingkan')
            ->assertSee('penilaian pertama')
            ->assertDontSee('▼ −50')  // 50 (3-6) → 0 (6-9) tidak dibandingkan
            ->assertDontSee(\Illuminate\Support\Carbon::parse('2020-01-01')->translatedFormat('j F Y'));
    }

    public function test_jawaban_kurang_atau_item_kelompok_lain_ditolak(): void
    {
        $user = $this->responden();
        $item = $this->item();
        $asing = $this->item('0-3')->first();

        $jawaban = $item->mapWithKeys(fn ($i) => [$i->id => '1'])->all();

        $this->actingAs($user)->post(route('perkembangan.store'), ['jawaban' => array_slice($jawaban, 1, null, true)])
            ->assertSessionHasErrors('jawaban.'.$item[0]->id);
        $this->actingAs($user)->post(route('perkembangan.store'), ['jawaban' => $jawaban + [$asing->id => '1']])
            ->assertSessionHasErrors('jawaban');
        $this->actingAs($user)->post(route('perkembangan.store'), ['jawaban' => [$item[0]->id => 'ya'] + $jawaban])
            ->assertSessionHasErrors('jawaban.'.$item[0]->id);

        $this->assertSame(0, PenilaianPerkembangan::count());
    }

    public function test_kelompok_tanpa_item_tidak_bisa_dinilai(): void
    {
        $user = $this->responden();

        $this->actingAs($user)->get(route('perkembangan.index'))->assertOk()->assertSee('belum tersedia');
        $this->actingAs($user)->post(route('perkembangan.store'), ['jawaban' => []])->assertNotFound();
    }

    public function test_perkembangan_terkunci_sebelum_pretest(): void
    {
        $user = User::factory()->create();
        Anak::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->get(route('perkembangan.index'))->assertRedirect(route('pretest'));
    }
}
