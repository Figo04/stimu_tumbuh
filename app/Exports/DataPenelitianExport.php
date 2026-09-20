<?php

namespace App\Exports;

use App\Models\AktivitasStimulasi;
use App\Models\Anak;
use App\Models\Materi;
use App\Models\PenilaianPerkembangan;
use App\Models\User;
use App\Services\UsiaAnakService;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * Lima sheet sesuai PRD §3.4 + baris CSV ringkas (Tabel 1 + hasil test).
 *
 * Tanggal ditulis ISO (Y-m-d) dan skor sebagai angka, bukan teks berformat —
 * file ini dipakai untuk analisis (Excel/SPSS/R), bukan untuk dibaca di layar.
 *
 * ponytail: seluruh baris dimuat ke memori sekali (tanpa chunk/queue) —
 * responden penelitian diperkirakan < 100. Pakai WithChunkReading bila membengkak.
 */
class DataPenelitianExport implements Export, WithMultipleSheets
{
    private const KOLOM_IDENTITAS = [
        'Kode Responden', 'Nama Orang Tua', 'Email', 'No. HP', 'Hubungan dengan Anak',
        'Pendidikan Terakhir', 'Pekerjaan', 'Kecamatan', 'Alamat', 'Tanggal Daftar',
        'Inisial Anak', 'Jenis Kelamin', 'Tanggal Lahir Anak', 'Usia Anak (bulan)', 'Kelompok Usia',
    ];

    private const KOLOM_HASIL_TEST = [
        'Pre-test Dikirim', 'Pre-test Skor Pengetahuan (%)', 'Pre-test Kategori', 'Pre-test Skor Sikap',
        'Post-test Dikirim', 'Post-test Skor Pengetahuan (%)', 'Post-test Kategori', 'Post-test Skor Sikap',
    ];

    /** @var Collection<int, User>|null */
    private ?Collection $responden = null;

    public static function namaFile(string $ekstensi): string
    {
        return 'stimutumbuh-data-penelitian-'.now()->format('Y-m-d-Hi').'.'.$ekstensi;
    }

    public function sheets(): array
    {
        return [
            new SheetTabel('Tabel 1 - Identitas Responden', self::KOLOM_IDENTITAS, $this->identitas()),
            new SheetTabel('Tabel 2 - Kondisi Saat Lahir', [
                'Kode Responden', 'Inisial Anak', 'Usia Gestasi (minggu)', 'Jenis Persalinan',
                'BB Lahir (gram)', 'PB Lahir (cm)', 'Lingkar Kepala (cm)', 'Kondisi Lahir',
            ], $this->kondisiLahir()),
            new SheetTabel('Tabel 3 - Aktivitas Stimulasi', [
                'Kode Responden', 'Tanggal', 'Usia Anak (bulan)', 'Aspek', 'Jenis Stimulasi',
                'Durasi (menit)', 'Pelaku', 'Respons Anak',
            ], $this->aktivitas()),
            new SheetTabel('Tabel 4 - Perkembangan', [
                'Kode Responden', 'Tanggal Penilaian', 'Usia Anak (bulan)', 'Kelompok Usia',
                'Skor Motorik Kasar (%)', 'Skor Motorik Halus (%)', 'Skor Bicara & Bahasa (%)',
                'Skor Sosial & Emosional (%)', 'Skor Total (%)',
            ], $this->perkembangan()),
            new SheetTabel('Tabel 5 - Penggunaan Aplikasi', [
                'Kode Responden', 'Materi Dibuka', 'Materi Selesai', 'Video Ditonton',
                'Materi Kelompok Usia Saat Ini', 'Progres Materi',
                ...self::KOLOM_HASIL_TEST,
            ], $this->penggunaan()),
        ];
    }

    /** CSV ringkas (PRD §3.4): identitas + hasil test, satu baris per responden. */
    public function csvRingkas(): array
    {
        $baris = [];

        foreach ($this->responden() as $r) {
            $baris[] = [...$this->barisIdentitas($r), ...$this->barisHasilTest($r)];
        }

        return [[...self::KOLOM_IDENTITAS, ...self::KOLOM_HASIL_TEST], $baris];
    }

    /** @return Collection<int, User> */
    private function responden(): Collection
    {
        return $this->responden ??= User::query()
            ->with(['anak', 'hasilKuesioner'])
            ->withCount([
                'progressMateri as materi_dibuka',
                'progressMateri as materi_selesai_count' => fn ($q) => $q->where('materi_selesai', true),
                'progressMateri as video_ditonton_count' => fn ($q) => $q->where('video_ditonton', true),
            ])
            ->orderBy('kode_responden')
            ->get();
    }

    private function barisIdentitas(User $r): array
    {
        $anak = $r->anak;

        return [
            $r->kode_responden,
            $r->nama,
            $r->email,
            $r->no_hp,
            $r->hubungan_dengan_anak,
            $r->pendidikan_terakhir,
            $r->pekerjaan,
            $r->kecamatan,
            $r->alamat,
            $r->created_at?->format('Y-m-d'),
            $anak?->nama_inisial,
            $anak?->jenis_kelamin,
            $anak?->tanggal_lahir->format('Y-m-d'),
            $anak ? UsiaAnakService::usiaBulan($anak->tanggal_lahir) : null,
            $anak?->kelompok_usia,
        ];
    }

    private function barisHasilTest(User $r): array
    {
        $baris = [];

        foreach (['pre', 'post'] as $tipe) {
            $hasil = $r->hasilKuesioner->firstWhere('tipe_sesi', $tipe);
            $baris = [...$baris, $hasil?->submitted_at?->format('Y-m-d'), self::angka($hasil?->skor_pengetahuan),
                $hasil?->kategori_pengetahuan, self::angka($hasil?->skor_sikap)];
        }

        return $baris;
    }

    private function identitas(): array
    {
        return $this->responden()->map(fn (User $r) => $this->barisIdentitas($r))->all();
    }

    private function kondisiLahir(): array
    {
        return $this->responden()
            ->filter(fn (User $r) => $r->anak !== null)
            ->map(fn (User $r) => [
                $r->kode_responden,
                $r->anak->nama_inisial,
                $r->anak->usia_gestasi_minggu,
                Anak::JENIS_PERSALINAN[$r->anak->jenis_persalinan] ?? null,
                $r->anak->bb_lahir_gram,
                self::angka($r->anak->pb_lahir_cm),
                self::angka($r->anak->lingkar_kepala_cm),
                Anak::KONDISI_LAHIR[$r->anak->kondisi_lahir] ?? null,
            ])
            ->values()
            ->all();
    }

    private function aktivitas(): array
    {
        return AktivitasStimulasi::query()
            ->with(['user.anak', 'materi'])
            ->orderBy('user_id')
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get()
            ->map(fn (AktivitasStimulasi $a) => [
                $a->user->kode_responden,
                $a->tanggal->format('Y-m-d'),
                // Usia pada tanggal entri (PRD §3.4 Tabel 3), bukan usia hari ini.
                $a->user->anak ? UsiaAnakService::usiaBulan($a->user->anak->tanggal_lahir, $a->tanggal) : null,
                Materi::ASPEK[$a->aspek] ?? $a->aspek,
                // Entri dari tab Praktik tidak punya jenis_stimulasi (Sesi 19).
                $a->jenis_stimulasi ?? ($a->materi ? 'Praktik: '.$a->materi->judul : null),
                $a->durasi_menit,
                AktivitasStimulasi::PELAKU[$a->pelaku] ?? $a->pelaku,
                $a->respons_anak,
            ])
            ->all();
    }

    private function perkembangan(): array
    {
        return PenilaianPerkembangan::query()
            ->with('user')
            ->orderBy('user_id')
            ->orderBy('tanggal_penilaian')
            ->orderBy('id')
            ->get()
            ->map(fn (PenilaianPerkembangan $p) => [
                $p->user->kode_responden,
                $p->tanggal_penilaian->format('Y-m-d'),
                // Snapshot saat penilaian (kolom DB), bukan hitung ulang hari ini.
                $p->usia_bulan,
                $p->kelompok_usia,
                self::angka($p->skor_motorik_kasar),
                self::angka($p->skor_motorik_halus),
                self::angka($p->skor_bicara_bahasa),
                self::angka($p->skor_sosial_emosional),
                self::angka($p->skor_total),
            ])
            ->all();
    }

    private function penggunaan(): array
    {
        // Progres diukur terhadap kelompok usia anak SAAT INI — sama dengan
        // User::semuaMateriSelesai() dan donut Progres Materi (Sesi 29).
        $totalPerKelompok = Materi::query()
            ->selectRaw('kelompok_usia, count(*) as jumlah')
            ->groupBy('kelompok_usia')
            ->pluck('jumlah', 'kelompok_usia');

        return $this->responden()->map(function (User $r) use ($totalPerKelompok) {
            $total = $r->anak ? (int) ($totalPerKelompok[$r->anak->kelompok_usia] ?? 0) : null;

            return [
                $r->kode_responden,
                $r->materi_dibuka,
                $r->materi_selesai_count,
                $r->video_ditonton_count,
                $total,
                $total === null ? null : $r->materi_selesai_count.' dari '.$total,
                ...$this->barisHasilTest($r),
            ];
        })->all();
    }

    /** Skor bercast decimal:2 dikembalikan sebagai string — ditulis sebagai angka agar bisa dihitung. */
    private static function angka(string|float|null $nilai): ?float
    {
        return $nilai === null ? null : (float) $nilai;
    }
}
