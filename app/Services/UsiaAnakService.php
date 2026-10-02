<?php

namespace App\Services;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

class UsiaAnakService
{
    /** Usia (bulan) yang boleh mendaftar — sasaran penelitian 12–24 bulan (keputusan klien, 2 Okt 2026). */
    public const USIA_DAFTAR_MIN = 12;

    public const USIA_DAFTAR_MAKS = 24;

    /**
     * Batas bawah (bulan, inklusif) => nilai enum `kelompok_usia`.
     * Enum DB masih memuat 0-3…9-12 (migration lama), tapi kelompok itu tidak dipakai lagi.
     */
    public const KELOMPOK_USIA = [
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

    /** Bawah inklusif, atas eksklusif; usia >= 36 bulan dijepit ke '24-36', < 12 bulan (tak bisa daftar) ke '12-18'. */
    public static function kelompokUsia(CarbonInterface|string $tanggalLahir, CarbonInterface|string|null $tanggalAcuan = null): string
    {
        $usia = self::usiaBulan($tanggalLahir, $tanggalAcuan);
        $kelompok = '12-18';

        foreach (self::KELOMPOK_USIA as $batasBawah => $nilai) {
            if ($usia >= $batasBawah) {
                $kelompok = $nilai;
            }
        }

        return $kelompok;
    }
}
