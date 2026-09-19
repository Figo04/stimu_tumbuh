<?php

namespace App\Services;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

class UsiaAnakService
{
    /** Batas bawah (bulan, inklusif) => nilai enum `kelompok_usia`. */
    public const KELOMPOK_USIA = [
        0 => '0-3',
        3 => '3-6',
        6 => '6-9',
        9 => '9-12',
        12 => '12-18',
        18 => '18-24',
        24 => '24-36',
    ];

    /** Usia dalam bulan penuh (dibulatkan ke bawah) pada tanggal acuan (default: hari ini). */
    public static function usiaBulan(CarbonInterface|string $tanggalLahir, CarbonInterface|string|null $tanggalAcuan = null): int
    {
        $lahir = Carbon::parse($tanggalLahir)->startOfDay();
        $acuan = Carbon::parse($tanggalAcuan ?? 'today')->startOfDay();

        return max(0, (int) floor($lahir->diffInMonths($acuan)));
    }

    /** Bawah inklusif, atas eksklusif; usia >= 36 bulan tetap dijepit ke '24-36'. */
    public static function kelompokUsia(CarbonInterface|string $tanggalLahir, CarbonInterface|string|null $tanggalAcuan = null): string
    {
        $usia = self::usiaBulan($tanggalLahir, $tanggalAcuan);
        $kelompok = '0-3';

        foreach (self::KELOMPOK_USIA as $batasBawah => $nilai) {
            if ($usia >= $batasBawah) {
                $kelompok = $nilai;
            }
        }

        return $kelompok;
    }
}
