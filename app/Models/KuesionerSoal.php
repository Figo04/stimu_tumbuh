<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Soal pre/post-test: item Ya/Tidak dokumen "Kuesioner Tumbuh Kembang" per kelompok usia,
 * kategori = ItemPerkembangan::ASPEK. Bank soal terpisah dari checklist Perkembangan.
 */
#[Fillable(['aspek', 'kelompok_usia', 'pertanyaan', 'urutan'])]
class KuesionerSoal extends Model
{
    /** Kunci = nilai yang dikirim form; disimpan sebagai boolean `jawaban_responden`. */
    public const JAWABAN = ['1' => 'Ya', '0' => 'Tidak'];

    protected $table = 'kuesioner_soal';

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
