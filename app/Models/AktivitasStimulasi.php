<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Entri kalender stimulasi (berkelanjutan, multi-entri); `materi_id` terisi bila dicatat dari tab Praktik. */
#[Fillable(['user_id', 'materi_id', 'tanggal', 'aspek', 'jenis_stimulasi', 'durasi_menit', 'pelaku', 'respons_anak'])]
class AktivitasStimulasi extends Model
{
    public const PELAKU = [
        'ibu' => 'Ibu',
        'ayah' => 'Ayah',
        'pengasuh' => 'Pengasuh',
        'lain' => 'Lainnya',
    ];

    protected $table = 'aktivitas_stimulasi';

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function materi(): BelongsTo
    {
        return $this->belongsTo(Materi::class);
    }
}
