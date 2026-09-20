<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AktivitasStimulasi;
use App\Models\HasilKuesioner;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $total = User::count();
        // "Sudah stimulasi" = pernah punya ≥ 1 entri kalender/praktik, tanpa batas waktu.
        $sudahStimulasi = AktivitasStimulasi::distinct()->count('user_id');

        return view('admin.dashboard', [
            'kartu' => [
                'Total Responden' => $total,
                // Unique (user_id, tipe_sesi) di DB → jumlah baris = jumlah responden.
                'Sudah Pre-test' => HasilKuesioner::where('tipe_sesi', 'pre')->count(),
                'Sudah Post-test' => HasilKuesioner::where('tipe_sesi', 'post')->count(),
                'Pre + Post Lengkap' => User::whereHas('hasilKuesioner', fn ($q) => $q->where('tipe_sesi', 'pre'))
                    ->whereHas('hasilKuesioner', fn ($q) => $q->where('tipe_sesi', 'post'))
                    ->count(),
                'Sudah Melakukan Stimulasi' => $sudahStimulasi,
                'Belum Melakukan Stimulasi' => $total - $sudahStimulasi,
            ],
            // ponytail: 10 entri terakhir, tanpa paginasi — daftar lengkap ada di Menu Aktivitas (Sesi 32).
            'terbaru' => AktivitasStimulasi::with(['user:id,kode_responden,nama', 'materi:id,judul'])
                ->latest('tanggal')->latest('id')->take(10)->get(),
        ]);
    }
}
