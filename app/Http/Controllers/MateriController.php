<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MateriController extends Controller
{
    public function index(Request $request): View
    {
        $kelompokUsia = $request->user()->anak()->firstOrFail()->kelompok_usia;

        $materi = Materi::where('kelompok_usia', $kelompokUsia)
            ->orderBy('urutan')
            ->get()
            ->groupBy('aspek');

        return view('materi.index', compact('kelompokUsia', 'materi'));
    }

    public function show(Request $request, Materi $materi): View
    {
        // Materi kelompok usia lain disembunyikan (404), bukan sekadar ditolak.
        abort_unless($materi->kelompok_usia === $request->user()->anak()->firstOrFail()->kelompok_usia, 404);

        return view('materi.show', compact('materi'));
    }
}
