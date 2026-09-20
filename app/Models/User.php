<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'kode_responden', 'nama', 'email', 'password', 'no_hp', 'hubungan_dengan_anak',
    'pendidikan_terakhir', 'pekerjaan', 'kecamatan', 'alamat',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function anak(): HasOne
    {
        return $this->hasOne(Anak::class);
    }

    public function hasilKuesioner(): HasMany
    {
        return $this->hasMany(HasilKuesioner::class);
    }

    /** @param  'pre'|'post'  $tipe */
    public function sudahKuesioner(string $tipe): bool
    {
        return $this->hasilKuesioner()->where('tipe_sesi', $tipe)->exists();
    }

    public function sudahPretest(): bool
    {
        return $this->sudahKuesioner('pre');
    }

    /** Post-test terbuka setelah pre-test dikirim dan seluruh materi kelompok usia selesai. */
    public function bolehPosttest(): bool
    {
        return $this->sudahPretest() && $this->semuaMateriSelesai();
    }

    public function progressMateri(): HasMany
    {
        return $this->hasMany(ProgressMateri::class);
    }

    public function aktivitasStimulasi(): HasMany
    {
        return $this->hasMany(AktivitasStimulasi::class);
    }

    public function penilaianPerkembangan(): HasMany
    {
        return $this->hasMany(PenilaianPerkembangan::class);
    }

    /** Penilaian terakhir saja — dipakai rekap admin agar tidak memuat seluruh riwayat tiap baris. */
    public function penilaianTerakhir(): HasOne
    {
        return $this->hasOne(PenilaianPerkembangan::class)->latestOfMany();
    }

    /** Basis gating tab Praktik. */
    public function materiSelesai(Materi $materi): bool
    {
        return $this->progressMateri()->where('materi_id', $materi->id)->where('materi_selesai', true)->exists();
    }

    /** Basis auto-switch ke post-test: seluruh materi kelompok usia anak saat ini sudah selesai. */
    public function semuaMateriSelesai(): bool
    {
        if (! $this->anak) {
            return false;
        }

        $materiIds = Materi::where('kelompok_usia', $this->anak->kelompok_usia)->pluck('id');

        return $materiIds->isNotEmpty()
            && $this->progressMateri()->where('materi_selesai', true)->whereIn('materi_id', $materiIds)->count() === $materiIds->count();
    }
}
