<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['judul', 'slug', 'aspek', 'kelompok_usia', 'urutan', 'konten_view', 'video_youtube_id'])]
class Materi extends Model
{
    use HasFactory;

    /** Urutan di sini = urutan tampil di daftar materi. */
    public const ASPEK = [
        'motorik_kasar' => 'Motorik Kasar',
        'motorik_halus' => 'Motorik Halus',
        'bicara_bahasa' => 'Bicara & Bahasa',
        'sosial_emosional' => 'Sosial & Emosional',
    ];

    protected $table = 'materi';

    public function progress(): HasMany
    {
        return $this->hasMany(ProgressMateri::class);
    }
}
