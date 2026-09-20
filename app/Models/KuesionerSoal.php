<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['tipe', 'pertanyaan', 'jawaban_benar', 'reverse_scored', 'urutan'])]
class KuesionerSoal extends Model
{
    public const TIPE = ['pengetahuan' => 'Pengetahuan', 'sikap' => 'Sikap'];

    public const JAWABAN_PENGETAHUAN = ['B' => 'Benar', 'S' => 'Salah'];

    public const JAWABAN_SIKAP = ['SS' => 'Sangat Setuju', 'S' => 'Setuju', 'TS' => 'Tidak Setuju', 'STS' => 'Sangat Tidak Setuju'];

    /** Bobot sikap untuk soal biasa; soal `reverse_scored` memakai 5 - bobot. */
    // ponytail: bobot Likert 4..1 default, belum dikonfirmasi ke panduan skor resmi klien.
    public const BOBOT_SIKAP = ['SS' => 4, 'S' => 3, 'TS' => 2, 'STS' => 1];

    protected $table = 'kuesioner_soal';

    protected function casts(): array
    {
        return ['reverse_scored' => 'boolean'];
    }

    /**
     * Jawaban responden atas soal ini. FK `soal_id` cascadeOnDelete → menghapus
     * soal ikut menghapus jawaban; Kelola Soal memakai relasi ini untuk menolak
     * hapus soal yang sudah pernah dijawab.
     */
    public function detail(): HasMany
    {
        return $this->hasMany(HasilKuesionerDetail::class, 'soal_id');
    }
}
