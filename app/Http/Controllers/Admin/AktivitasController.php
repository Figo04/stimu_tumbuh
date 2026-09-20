<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

/** View-only (PRD §3.2) — rekap & riwayat kalender stimulasi, tanpa rute ubah/hapus. */
class AktivitasController extends Controller
{
    public function index(): View
    {
        return view('admin.aktivitas.index', [
            'responden' => User::query()
                ->withCount('aktivitasStimulasi')
                ->withSum('aktivitasStimulasi', 'durasi_menit')
                ->withMax('aktivitasStimulasi', 'tanggal')
                ->withCasts(['aktivitas_stimulasi_max_tanggal' => 'date'])
                ->orderBy('kode_responden')
                ->paginate(25),
        ]);
    }

    public function show(User $responden): View
    {
        return view('admin.aktivitas.show', [
            'responden' => $responden->load('anak'),
            // ponytail: tanpa paginasi — satu responden diperkirakan < 200 entri selama penelitian.
            'riwayat' => $responden->aktivitasStimulasi()
                ->with('materi')
                ->orderByDesc('tanggal')
                ->orderByDesc('id')
                ->get(),
        ]);
    }
}
