<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Penilaian perkembangan: berkelanjutan, satu baris per penilaian (bukan pola final kuesioner). */
#[Fillable([
    'user_id', 'anak_id', 'tanggal_penilaian', 'usia_bulan', 'kelompok_usia',
    'skor_motorik_kasar', 'skor_motorik_halus', 'skor_bicara_bahasa', 'skor_sosial_emosional', 'skor_total',
])]
class PenilaianPerkembangan extends Model
{
    protected $table = 'penilaian_perkembangan';

    protected function casts(): array
    {
        return [
            'tanggal_penilaian' => 'date',
            'skor_motorik_kasar' => 'decimal:2',
            'skor_motorik_halus' => 'decimal:2',
            'skor_bicara_bahasa' => 'decimal:2',
            'skor_sosial_emosional' => 'decimal:2',
            'skor_total' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function detail(): HasMany
    {
        return $this->hasMany(PenilaianDetail::class, 'penilaian_id');
    }
}
