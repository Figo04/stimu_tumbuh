<?php

namespace App\Models;

use App\Services\UsiaAnakService;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id', 'nama_inisial', 'tanggal_lahir', 'jenis_kelamin', 'bb_lahir_gram', 'pb_lahir_cm',
    'lingkar_kepala_cm', 'usia_gestasi_minggu', 'jenis_persalinan', 'kondisi_lahir',
])]
class Anak extends Model
{
    use HasFactory;

    public const JENIS_PERSALINAN = ['normal' => 'Normal', 'sc' => 'Caesar (SC)', 'vakum' => 'Vakum', 'forceps' => 'Forceps'];

    public const KONDISI_LAHIR = [
        'sehat' => 'Sehat',
        'bblr' => 'BBLR (berat lahir rendah)',
        'prematur' => 'Prematur',
        'asfiksia' => 'Asfiksia',
        'lain' => 'Lainnya',
    ];

    protected $table = 'anak';

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'pb_lahir_cm' => 'decimal:1',
            'lingkar_kepala_cm' => 'decimal:1',
        ];
    }

    /** Kelompok usia saat ini — selalu lewat UsiaAnakService agar ikut berubah saat anak bertambah usia. */
    protected function kelompokUsia(): Attribute
    {
        return Attribute::get(fn () => UsiaAnakService::kelompokUsia($this->tanggal_lahir));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
