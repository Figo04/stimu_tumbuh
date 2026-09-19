<?php

namespace Tests\Unit;

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
}
