<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MateriController extends Controller
{
    public function index(Request $request): View
    {
        $kelompokUsia = $request->user()->anak()->firstOrFail()->kelompok_usia;

        $materi = Materi::where('kelompok_usia', $kelompokUsia)
            ->orderBy('urutan')
            ->get();

        $selesai = $request->user()->progressMateri()
            ->where('materi_selesai', true)
            ->whereIn('materi_id', $materi->pluck('id'))
            ->pluck('materi_id');

        $total = $materi->count();
        $materi = $materi->groupBy('aspek');

        return view('materi.index', compact('kelompokUsia', 'materi', 'selesai', 'total'));
    }

    public function show(Request $request, Materi $materi): View
    {
        $this->pastikanKelompokUsiaAnak($request, $materi);

        // Baris progress pertama = catatan "materi dibuka" (export Tabel 5).
        $progress = $request->user()->progressMateri()->firstOrCreate(['materi_id' => $materi->id]);

        return view('materi.show', compact('materi', 'progress'));
    }

    public function selesai(Request $request, Materi $materi): RedirectResponse
    {
        $this->pastikanKelompokUsiaAnak($request, $materi);

        $progress = $request->user()->progressMateri()->firstOrCreate(['materi_id' => $materi->id]);

        // Satu arah: waktu selesai pertama dipertahankan, tidak ada rute untuk membatalkan.
        if (! $progress->materi_selesai) {
            $progress->update(['materi_selesai' => true, 'materi_selesai_at' => now()]);
        }

        return redirect()->route('materi.show', $materi);
    }

    /** Materi kelompok usia lain disembunyikan (404), bukan sekadar ditolak. */
    private function pastikanKelompokUsiaAnak(Request $request, Materi $materi): void
    {
        abort_unless($materi->kelompok_usia === $request->user()->anak()->firstOrFail()->kelompok_usia, 404);
    }
}
