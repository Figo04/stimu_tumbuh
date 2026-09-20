<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

/** View-only (PRD §3.2) — rekap & riwayat penilaian perkembangan, tanpa rute ubah/hapus. */
class PerkembanganController extends Controller
{
    public function index(): View
    {
        return view('admin.perkembangan.index', [
            'responden' => User::query()
                ->withCount('penilaianPerkembangan')
                ->with('penilaianTerakhir')
                ->orderBy('kode_responden')
                ->paginate(25),
        ]);
    }

    public function show(User $responden): View
    {
        return view('admin.perkembangan.show', [
            'responden' => $responden->load('anak'),
            // Skor per aspek saja (PRD §3.2); jawaban per item checklist tidak ditampilkan.
            'riwayat' => $responden->penilaianPerkembangan()
                ->orderByDesc('tanggal_penilaian')
                ->orderByDesc('id')
                ->get(),
        ]);
    }
}
