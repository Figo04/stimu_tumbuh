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
    'anemia_hamil', 'penyakit_hamil', 'penyakit_hamil_ket', 'penyakit_keturunan', 'penyakit_keturunan_ket',
    'kebiasaan_ibu', 'kebiasaan_ibu_lain',
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

    public const KEBIASAAN_IBU = [
        'tidak_ada' => 'Tidak ada',
        'merokok' => 'Merokok',
        'alkohol' => 'Konsumsi alkohol',
        'lainnya' => 'Lainnya',
    ];

    protected $table = 'anak';

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'pb_lahir_cm' => 'decimal:1',
            'lingkar_kepala_cm' => 'decimal:1',
            'kondisi_lahir' => 'array',
            'anemia_hamil' => 'boolean',
            'penyakit_hamil' => 'boolean',
            'penyakit_keturunan' => 'boolean',
            'kebiasaan_ibu' => 'array',
        ];
    }

    /** Keterangan hanya disimpan bila pilihannya "ada"/"lainnya" — isian yang ditinggalkan tidak ikut tersimpan. */
    protected static function booted(): void
    {
        static::saving(function (Anak $anak) {
            if (! $anak->penyakit_hamil) {
                $anak->penyakit_hamil_ket = null;
            }
            if (! $anak->penyakit_keturunan) {
                $anak->penyakit_keturunan_ket = null;
            }
            if (! in_array('lainnya', $anak->kebiasaan_ibu ?? [], true)) {
                $anak->kebiasaan_ibu_lain = null;
            }
        });
    }

    /** Label pilihan ganda dipisah koma, mis. "Prematur, BBLR (berat lahir rendah)". */
    public static function labelGanda(array $opsi, ?array $nilai): ?string
    {
        return $nilai ? implode(', ', array_map(fn ($k) => $opsi[$k] ?? $k, $nilai)) : null;
    }

    /** Riwayat kehamilan & kesehatan ibu siap tampil (profil, detail admin, export). */
    public function riwayatIbu(): array
    {
        $adaTidak = fn (?bool $ada, ?string $ket) => $ada === null ? null : ($ada ? 'Ada'.($ket ? ': '.$ket : '') : 'Tidak ada');
        $opsi = self::KEBIASAAN_IBU;
        if ($this->kebiasaan_ibu_lain) {
            $opsi['lainnya'] = 'Lainnya: '.$this->kebiasaan_ibu_lain;
        }

        return [
            'Anemia saat hamil' => $this->anemia_hamil === null ? null : ($this->anemia_hamil ? 'Anemia' : 'Tidak anemia'),
            'Penyakit saat hamil' => $adaTidak($this->penyakit_hamil, $this->penyakit_hamil_ket),
            'Penyakit keturunan' => $adaTidak($this->penyakit_keturunan, $this->penyakit_keturunan_ket),
            'Kebiasaan ibu' => self::labelGanda($opsi, $this->kebiasaan_ibu),
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
