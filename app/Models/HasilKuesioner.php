<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'tipe_sesi', 'skor_pengetahuan', 'kategori_pengetahuan', 'skor_sikap', 'submitted_at'])]
class HasilKuesioner extends Model
{
    protected $table = 'hasil_kuesioner';

    protected function casts(): array
    {
        return [
            'skor_pengetahuan' => 'decimal:2',
            'skor_sikap' => 'decimal:2',
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
