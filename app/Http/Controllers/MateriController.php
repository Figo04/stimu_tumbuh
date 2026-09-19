<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

        $riwayatPraktik = $request->user()->aktivitasStimulasi()
            ->where('materi_id', $materi->id)
            ->latest('tanggal')->latest('id')
            ->get();

        return view('materi.show', compact('materi', 'progress', 'riwayatPraktik'));
    }

    /** Tab Praktik: berkelanjutan, setiap kiriman = entri baru `aktivitas_stimulasi` (tanpa kunci/edit). */
    public function praktik(Request $request, Materi $materi): RedirectResponse
    {
        $this->pastikanKelompokUsiaAnak($request, $materi);
        abort_unless($request->user()->materiSelesai($materi), 403, 'Tab Praktik terbuka setelah materi selesai dibaca.');

        $data = $request->validate([
            'tanggal' => ['required', 'date', 'before_or_equal:today'],
            'respons_anak' => ['nullable', 'string', 'max:1000'],
        ]);

        $request->user()->aktivitasStimulasi()->create($data + [
            'materi_id' => $materi->id,
            'aspek' => $materi->aspek,
            // Enum hubungan (ibu/ayah/pengasuh) adalah himpunan bagian enum pelaku.
            'pelaku' => $request->user()->hubungan_dengan_anak,
        ]);

        return redirect()->route('materi.show', $materi)->with('status', 'praktik-tersimpan');
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

    /** Dipanggil (fetch) saat popup video dibuka; satu arah, waktu tonton pertama dipertahankan. */
    public function video(Request $request, Materi $materi): Response
    {
        $this->pastikanKelompokUsiaAnak($request, $materi);
        abort_unless($materi->video_youtube_id, 404);

        $progress = $request->user()->progressMateri()->firstOrCreate(['materi_id' => $materi->id]);

        if (! $progress->video_ditonton) {
            $progress->update(['video_ditonton' => true, 'video_ditonton_at' => now()]);
        }

        return response()->noContent();
    }

    /** Materi kelompok usia lain disembunyikan (404), bukan sekadar ditolak. */
    private function pastikanKelompokUsiaAnak(Request $request, Materi $materi): void
    {
        abort_unless($materi->kelompok_usia === $request->user()->anak()->firstOrFail()->kelompok_usia, 404);
    }
}
