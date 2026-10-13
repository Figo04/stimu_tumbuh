<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['aspek', 'kelompok_usia', 'pertanyaan', 'urutan'])]
class ItemPerkembangan extends Model
{
    /**
     * Kategori checklist dari dokumen klien (beda dengan Materi::ASPEK yang dipakai materi & kalender).
     * Urutan di sini = urutan tampil; kunci = akhiran kolom `skor_*` di penilaian_perkembangan.
     */
    public const ASPEK = [
        'motorik' => 'Motorik',
        'berpikir_belajar' => 'Berpikir & Belajar',
        'komunikasi' => 'Komunikasi',
        'sosial_emosional' => 'Sosial & Emosional',
    ];

    protected $table = 'item_perkembangan';
}
