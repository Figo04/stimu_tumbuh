<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AktivitasStimulasi;
use App\Models\Anak;
use App\Models\HasilKuesioner;
use App\Models\Materi;
use App\Models\PenilaianPerkembangan;
use App\Models\ProgressMateri;
use App\Models\User;
use App\Services\UsiaAnakService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $total = User::count();
        // "Sudah stimulasi" = pernah punya ≥ 1 entri kalender/praktik, tanpa batas waktu.
        $sudahStimulasi = AktivitasStimulasi::distinct()->count('user_id');
        $pre = HasilKuesioner::where('tipe_sesi', 'pre')->count();
        $lengkap = User::whereHas('hasilKuesioner', fn ($q) => $q->where('tipe_sesi', 'pre'))
            ->whereHas('hasilKuesioner', fn ($q) => $q->where('tipe_sesi', 'post'))
            ->count();

        return view('admin.dashboard', [
            'kartu' => [
                'Total Responden' => $total,
                // Unique (user_id, tipe_sesi) di DB → jumlah baris = jumlah responden.
                'Sudah Pre-test' => $pre,
                'Sudah Post-test' => HasilKuesioner::where('tipe_sesi', 'post')->count(),
                'Pre + Post Lengkap' => $lengkap,
                'Sudah Melakukan Stimulasi' => $sudahStimulasi,
                'Belum Melakukan Stimulasi' => $total - $sudahStimulasi,
            ],
            // Post-test hanya terbuka setelah pre-test → tidak ada responden "post saja".
            'statusTest' => [
                'Belum test' => $total - $pre,
                'Pre-test saja' => $pre - $lengkap,
                'Pre + Post' => $lengkap,
            ],
            'progresMateri' => $this->progresMateri($total),
            'sebaranUsia' => $this->sebaranUsia(),
            'rataSkorAspek' => $this->rataSkorAspek(),
            // ponytail: 10 entri terakhir, tanpa paginasi — daftar lengkap ada di Menu Aktivitas (Sesi 32).
            'terbaru' => AktivitasStimulasi::with(['user:id,kode_responden,nama', 'materi:id,judul'])
                ->latest('tanggal')->latest('id')->take(10)->get(),
        ]);
    }

    /**
     * Progres materi per responden, diukur terhadap kelompok usia anaknya *saat ini*
     * (sama seperti User::semuaMateriSelesai()). 3 query teragregasi, bukan per responden.
     *
     * @return array<string, int>
     */
    private function progresMateri(int $total): array
    {
        $anak = Anak::select('user_id', 'tanggal_lahir')->get();
        $targetPerKelompok = Materi::selectRaw('kelompok_usia, count(*) as n')
            ->groupBy('kelompok_usia')->pluck('n', 'kelompok_usia');
        // Materi selesai dihitung per (responden, kelompok usia materi) supaya materi
        // kelompok lama tidak ikut terhitung saat anak naik kelompok usia.
        $selesai = ProgressMateri::where('materi_selesai', true)
            ->join('materi', 'materi.id', '=', 'progress_materi.materi_id')
            ->selectRaw('progress_materi.user_id, materi.kelompok_usia, count(*) as n')
            ->groupBy('progress_materi.user_id', 'materi.kelompok_usia')
            ->get()->keyBy(fn ($r) => $r->user_id.'|'.$r->kelompok_usia);

        // Responden tanpa data anak (hanya mungkin dari seeder/test) ikut "Belum mulai".
        $hitung = ['Belum mulai' => $total - $anak->count(), 'Sebagian' => 0, 'Selesai semua' => 0];

        foreach ($anak as $a) {
            $kelompok = UsiaAnakService::kelompokUsia($a->tanggal_lahir);
            $target = (int) ($targetPerKelompok[$kelompok] ?? 0);
            $n = (int) ($selesai->get($a->user_id.'|'.$kelompok)->n ?? 0);

            $hitung[match (true) {
                $n === 0 => 'Belum mulai',
                $target > 0 && $n >= $target => 'Selesai semua',
                default => 'Sebagian',
            }]++;
        }

        return $hitung;
    }

    /** @return array<string, int> jumlah anak per kelompok usia, semua kelompok selalu tampil. */
    private function sebaranUsia(): array
    {
        $hitung = Anak::pluck('tanggal_lahir')->countBy(fn ($tgl) => UsiaAnakService::kelompokUsia($tgl));

        return collect(UsiaAnakService::KELOMPOK_USIA)
            ->mapWithKeys(fn ($k) => [$k.' bln' => (int) ($hitung[$k] ?? 0)])->all();
    }

    /** @return array<string, float> rata-rata skor tiap aspek dari penilaian TERAKHIR tiap responden. */
    private function rataSkorAspek(): array
    {
        $kolom = ['motorik_kasar', 'motorik_halus', 'bicara_bahasa', 'sosial_emosional'];
        $rata = PenilaianPerkembangan::whereIn('id', fn ($q) => $q->from('penilaian_perkembangan')
            ->selectRaw('MAX(id)')->groupBy('user_id'))
            ->selectRaw(implode(', ', array_map(fn ($k) => "AVG(skor_$k) as $k", $kolom)))
            ->first();

        return array_combine(
            array_map(fn ($k) => Materi::ASPEK[$k], $kolom),
            array_map(fn ($k) => round((float) $rata?->$k, 2), $kolom),
        );
    }
}
