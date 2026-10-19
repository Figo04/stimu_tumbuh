<?php

namespace Tests\Unit;

use App\Models\ItemPerkembangan;
use App\Services\SkorService;
use PHPUnit\Framework\TestCase;

class SkorServiceTest extends TestCase
{
    public function test_skor_perkembangan_per_aspek_dan_total_dari_seluruh_item(): void
    {
        $item = collect([
            [1, 'motorik'], [2, 'motorik'], [3, 'motorik'],
            [4, 'berpikir_belajar'],
            [5, 'komunikasi'], [6, 'komunikasi'],
        ])->map(fn ($i) => (new ItemPerkembangan)->forceFill(['id' => $i[0], 'aspek' => $i[1]]));

        $skor = SkorService::skorPerkembangan($item, [1 => true, 2 => true, 3 => false, 4 => false, 5 => true, 6 => true]);

        $this->assertSame(66.67, $skor['skor_motorik']);
        $this->assertSame(0.0, $skor['skor_berpikir_belajar']);
        $this->assertSame(100.0, $skor['skor_komunikasi']);
        $this->assertSame(0, $skor['skor_sosial_emosional']); // aspek tanpa item
        $this->assertSame(66.67, $skor['skor_total']); // 4 dari 6 item, bukan rata-rata aspek
    }
}
