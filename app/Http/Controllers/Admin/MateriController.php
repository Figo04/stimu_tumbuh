<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Materi;
use App\Services\UsiaAnakService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * View-only (PRD §3.2) — kecuali `video_youtube_id`, yang boleh diganti admin
 * (keputusan developer Sesi 34, perubahan lingkup atas PRD §3.3). Judul, aspek,
 * kelompok usia, urutan, dan isi materi tetap dihardcode di MateriSeeder + view.
 */
class MateriController extends Controller
{
    public function index(): View
    {
        $materi = Materi::withCount([
            'progress as dibuka_count',
            'progress as selesai_count' => fn ($q) => $q->where('materi_selesai', true),
        ])->get();

        return view('admin.materi.index', [
            // Urut aspek mengikuti Materi::ASPEK (bukan urutan enum DB: test SQLite, produksi MySQL).
            'materi' => $materi->groupBy('kelompok_usia')->map(
                fn ($grup) => $grup->sortBy([
                    fn ($m) => array_search($m->aspek, array_keys(Materi::ASPEK)),
                    fn ($m) => $m->urutan,
                ])->values()
            ),
            'kelompokUsia' => UsiaAnakService::KELOMPOK_USIA,
        ]);
    }

    public function show(Materi $materi): View
    {
        return view('admin.materi.show', ['materi' => $materi]);
    }

    public function update(Request $request, Materi $materi): RedirectResponse
    {
        $masukan = trim((string) $request->input('video_youtube_id'));
        $id = $masukan === '' ? null : self::idYoutube($masukan);

        if ($masukan !== '' && $id === null) {
            throw ValidationException::withMessages([
                'video_youtube_id' => 'Tautan YouTube tidak dikenali. Tempel tautan lengkap (youtube.com/watch?v=… atau youtu.be/…) atau ID videonya saja.',
            ]);
        }

        $materi->update(['video_youtube_id' => $id]);

        return redirect()->route('admin.materi.show', $materi)
            ->with('status', $id ? 'Video diperbarui.' : 'Video dihapus dari materi ini.');
    }

    /** ID mentah, atau ID di dalam tautan watch/youtu.be/embed/shorts. Null bila tak dikenali. */
    private static function idYoutube(string $masukan): ?string
    {
        if (preg_match('~^[\w-]{11}$~', $masukan)) {
            return $masukan;
        }

        // Awalan wajib: tanpa itu potongan lain yang kebetulan 11 karakter ikut tertangkap.
        return preg_match('~(?:youtu\.be/|/embed/|/shorts/|[?&]v=)([\w-]{11})~', $masukan, $cocok) ? $cocok[1] : null;
    }
}
