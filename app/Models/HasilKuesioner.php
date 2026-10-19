<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id', 'tipe_sesi', 'kelompok_usia',
    'skor_motorik', 'skor_berpikir_belajar', 'skor_komunikasi', 'skor_sosial_emosional', 'skor_total',
    'submitted_at',
])]
class HasilKuesioner extends Model
{
    protected $table = 'hasil_kuesioner';

    protected function casts(): array
    {
        return [
            'skor_motorik' => 'decimal:2',
            'skor_berpikir_belajar' => 'decimal:2',
            'skor_komunikasi' => 'decimal:2',
            'skor_sosial_emosional' => 'decimal:2',
            'skor_total' => 'decimal:2',
            'submitted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function detail(): HasMany
    {
        return $this->hasMany(HasilKuesionerDetail::class);
    }
}
