<?php

namespace Tests\Feature;

use App\Models\ItemPerkembangan;
use App\Models\Materi;
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
            foreach (array_keys(Materi::ASPEK) as $aspek) {
                $this->assertTrue(
                    ItemPerkembangan::where(['kelompok_usia' => $kelompok, 'aspek' => $aspek])->exists(),
                    "Item {$kelompok} {$aspek} kosong"
                );
            }
        }

        $jumlah = ItemPerkembangan::count();
        $this->seed(ItemPerkembanganSeeder::class);

        $this->assertSame($jumlah, ItemPerkembangan::count());
    }
}
