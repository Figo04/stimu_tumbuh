<?php

namespace Tests\Feature;

use App\Models\ItemPerkembangan;
use App\Services\UsiaAnakService;
use Database\Seeders\ItemPerkembanganSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemPerkembanganSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_tiap_kelompok_usia_dan_aspek_punya_item_dan_seeding_ulang_tidak_menggandakan(): void
    {
        $this->seed(ItemPerkembanganSeeder::class);

        foreach (UsiaAnakService::KELOMPOK_USIA as $kelompok) {
            foreach (array_keys(ItemPerkembangan::ASPEK) as $aspek) {
                $this->assertTrue(
                    ItemPerkembangan::where(['kelompok_usia' => $kelompok, 'aspek' => $aspek])->exists(),
                    "Item {$kelompok} {$aspek} kosong"
                );
            }
        }

        // Jumlah soal per dokumen klien: 12–17 bln = 17, 18–23 bln = 17, 24 bln = 14.
        $this->assertSame(['12-18' => 17, '18-24' => 17, '24-36' => 14],
            ItemPerkembangan::selectRaw('kelompok_usia, count(*) as n')->groupBy('kelompok_usia')->orderBy('kelompok_usia')->pluck('n', 'kelompok_usia')->all());
        $this->assertSame(0, ItemPerkembangan::where('pertanyaan', 'like', '%PLACEHOLDER%')->count());

        $jumlah = ItemPerkembangan::count();
        $this->seed(ItemPerkembanganSeeder::class);

        $this->assertSame($jumlah, ItemPerkembangan::count());
    }
}
