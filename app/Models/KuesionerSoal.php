<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['tipe', 'pertanyaan', 'jawaban_benar', 'reverse_scored', 'urutan'])]
class KuesionerSoal extends Model
{
    public const TIPE = ['pengetahuan' => 'Pengetahuan', 'sikap' => 'Sikap'];

    public const JAWABAN_PENGETAHUAN = ['B' => 'Benar', 'S' => 'Salah'];

    protected $table = 'kuesioner_soal';

    protected function casts(): array
    {
        return ['reverse_scored' => 'boolean'];
    }
}
