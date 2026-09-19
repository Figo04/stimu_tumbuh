<?php

namespace Tests\Unit;

use App\Models\ItemPerkembangan;
use App\Models\KuesionerSoal;
use App\Services\SkorService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SkorServiceTest extends TestCase
{
    public static function batasKategori(): array
    {
        return [
            '100' => [100, 'Baik'],
            'tepat 76' => [76, 'Baik'],
            '75.99' => [75.99, 'Cukup'],
            'tepat 56' => [56, 'Cukup'],
            '55.99' => [55.99, 'Kurang'],
            '0' => [0, 'Kurang'],
        ];
    }

    #[DataProvider('batasKategori')]
    public function test_kategori_pengetahuan(float $persen, string $kategori): void
    {
        $this->assertSame($kategori, SkorService::kategoriPengetahuan($persen));
    }

    private static function soal(int $id, string $tipe, ?string $benar = null, bool $reverse = false): KuesionerSoal
    {
        return (new KuesionerSoal)->forceFill([
            'id' => $id, 'tipe' => $tipe, 'jawaban_benar' => $benar, 'reverse_scored' => $reverse,
        ]);
    }

    public function test_skor_pengetahuan_dan_sikap_termasuk_reverse(): void
    {
        $soal = collect([
            self::soal(1, 'pengetahuan', 'B'),
            self::soal(2, 'pengetahuan', 'S'),
            self::soal(3, 'pengetahuan', 'B'),
            self::soal(4, 'sikap'),
            self::soal(5, 'sikap', reverse: true),
            self::soal(6, 'sikap', reverse: true),
        ]);

        // 2 dari 3 benar; sikap: SS=4, reverse SS=1, reverse STS=4.
        $skor = SkorService::skorKuesioner($soal, [1 => 'B', 2 => 'S', 3 => 'S', 4 => 'SS', 5 => 'SS', 6 => 'STS']);

        $this->assertSame(66.67, $skor['skor_pengetahuan']);
        $this->assertSame('Cukup', $skor['kategori_pengetahuan']);
        $this->assertSame(9, $skor['skor_sikap']);
    }

    public function test_skor_perkembangan_per_aspek_dan_total_dari_seluruh_item(): void
    {
        $item = collect([
            [1, 'motorik_kasar'], [2, 'motorik_kasar'], [3, 'motorik_kasar'],
            [4, 'motorik_halus'],
            [5, 'bicara_bahasa'], [6, 'bicara_bahasa'],
        ])->map(fn ($i) => (new ItemPerkembangan)->forceFill(['id' => $i[0], 'aspek' => $i[1]]));

        $skor = SkorService::skorPerkembangan($item, [1 => true, 2 => true, 3 => false, 4 => false, 5 => true, 6 => true]);

        $this->assertSame(66.67, $skor['skor_motorik_kasar']);
        $this->assertSame(0.0, $skor['skor_motorik_halus']);
        $this->assertSame(100.0, $skor['skor_bicara_bahasa']);
        $this->assertSame(0, $skor['skor_sosial_emosional']); // aspek tanpa item
        $this->assertSame(66.67, $skor['skor_total']); // 4 dari 6 item, bukan rata-rata aspek
    }
}
