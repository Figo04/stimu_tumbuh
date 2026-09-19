<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Baris dibuat saat materi pertama kali dibuka (created_at = waktu dibuka). */
#[Fillable(['user_id', 'materi_id', 'materi_selesai', 'materi_selesai_at', 'video_ditonton', 'video_ditonton_at'])]
class ProgressMateri extends Model
{
    protected $table = 'progress_materi';

    protected function casts(): array
    {
        return [
            'materi_selesai' => 'boolean',
            'materi_selesai_at' => 'datetime',
            'video_ditonton' => 'boolean',
            'video_ditonton_at' => 'datetime',
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
