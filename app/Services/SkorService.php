<?php

namespace App\Services;

use App\Models\KuesionerSoal;
use Illuminate\Support\Collection;

class SkorService
{
    /** Kategori Arikunto: Baik 76–100%, Cukup 56–75%, Kurang < 56%. */
    public static function kategoriPengetahuan(float $persen): string
    {
        return match (true) {
            $persen >= 76 => 'Baik',
            $persen >= 56 => 'Cukup',
            default => 'Kurang',
        };
    }

    /**
     * Skor kuesioner siap disimpan ke `hasil_kuesioner`.
     *
     * @param  Collection<int, KuesionerSoal>  $soal
     * @param  array<int, string>  $jawaban  soal_id => jawaban responden
     */
    public static function skorKuesioner(Collection $soal, array $jawaban): array
    {
        $pengetahuan = $soal->where('tipe', 'pengetahuan');
        $benar = $pengetahuan->filter(fn ($s) => ($jawaban[$s->id] ?? null) === $s->jawaban_benar)->count();
        $persen = $pengetahuan->isEmpty() ? 0 : round($benar / $pengetahuan->count() * 100, 2);

        $sikap = $soal->where('tipe', 'sikap')->sum(function ($s) use ($jawaban) {
            $bobot = KuesionerSoal::BOBOT_SIKAP[$jawaban[$s->id]];

            return $s->reverse_scored ? 5 - $bobot : $bobot;
        });

        return [
            'skor_pengetahuan' => $persen,
            'kategori_pengetahuan' => self::kategoriPengetahuan($persen),
            'skor_sikap' => $sikap,
        ];
    }
}
