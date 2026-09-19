<?php

namespace Tests\Unit;

use App\Services\UsiaAnakService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class UsiaAnakServiceTest extends TestCase
{
    public static function batasKelompok(): array
    {
        // [tanggal_lahir, tanggal_acuan, usia_bulan, kelompok_usia]
        return [
            'baru lahir' => ['2026-01-15', '2026-01-15', 0, '0-3'],
            'sehari sebelum 3 bln' => ['2026-01-15', '2026-04-14', 2, '0-3'],
            'tepat 3 bln' => ['2026-01-15', '2026-04-15', 3, '3-6'],
            'tepat 6 bln' => ['2026-01-15', '2026-07-15', 6, '6-9'],
            'tepat 9 bln' => ['2026-01-15', '2026-10-15', 9, '9-12'],
            'sehari sebelum 12 bln' => ['2026-01-15', '2027-01-14', 11, '9-12'],
            'tepat 12 bln' => ['2026-01-15', '2027-01-15', 12, '12-18'],
            'tepat 18 bln' => ['2026-01-15', '2027-07-15', 18, '18-24'],
            'tepat 24 bln' => ['2026-01-15', '2028-01-15', 24, '24-36'],
            'tepat 36 bln dijepit' => ['2026-01-15', '2029-01-15', 36, '24-36'],
            '40 bln dijepit' => ['2026-01-15', '2029-05-15', 40, '24-36'],
            'lahir 31 Jan, cek 29 Feb' => ['2024-01-31', '2024-02-29', 0, '0-3'],
            'lahir 31 Jan, cek 1 Mar' => ['2024-01-31', '2024-03-01', 1, '0-3'],
            'lahir 30 Nov, cek 28 Feb (belum 3 bln)' => ['2023-11-30', '2024-02-28', 2, '0-3'],
            'lahir 30 Nov, cek 1 Mar (3 bln)' => ['2023-11-30', '2024-03-01', 3, '3-6'],
        ];
    }

    #[DataProvider('batasKelompok')]
    public function test_usia_dan_kelompok_usia(string $lahir, string $acuan, int $usia, string $kelompok): void
    {
        $this->assertSame($usia, UsiaAnakService::usiaBulan($lahir, $acuan));
        $this->assertSame($kelompok, UsiaAnakService::kelompokUsia($lahir, $acuan));
    }
}
