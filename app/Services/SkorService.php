<?php

namespace App\Services;

use App\Models\ItemPerkembangan;
use App\Models\KuesionerSoal;
use Illuminate\Support\Collection;

class SkorService
{
    /**
     * Skor checklist Ya/Tidak siap disimpan ke `penilaian_perkembangan` maupun `hasil_kuesioner`
     * (pre/post-test memakai item dokumen yang sama): persen item "Ya" per aspek,
     * total = persen "Ya" dari seluruh item.
     *
     * @param  Collection<int, ItemPerkembangan|KuesionerSoal>  $item
     * @param  array<int, bool>  $jawaban  item_id => true (Ya) / false (Tidak)
     */
    public static function skorPerkembangan(Collection $item, array $jawaban): array
    {
        $persen = fn (Collection $items) => $items->isEmpty()
            ? 0
            : round($items->filter(fn ($i) => $jawaban[$i->id] ?? false)->count() / $items->count() * 100, 2);

        $skor = [];
        foreach (array_keys(ItemPerkembangan::ASPEK) as $aspek) {
            $skor["skor_{$aspek}"] = $persen($item->where('aspek', $aspek));
        }

        return $skor + ['skor_total' => $persen($item)];
    }
}
