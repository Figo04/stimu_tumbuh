<?php

namespace App\Http\Controllers;

use App\Models\HasilKuesioner;
use App\Models\KuesionerSoal;
use App\Models\Materi;
use App\Models\User;
use App\Services\SkorService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KuesionerController extends Controller
{
    /** Label per `tipe_sesi`; tipe datang dari `->defaults('tipe', ...)` di routes/web.php. */
    public const LABEL = ['pre' => 'Pre-test', 'post' => 'Post-test'];

    public function show(Request $request, string $tipe): View|RedirectResponse
    {
        if ($tolak = $this->tolak($request->user(), $tipe)) {
            return $tolak;
        }

        $kelompokUsia = $this->kelompokUsia($request->user(), $tipe);

        return view('kuesioner.pretest', [
            'tipe' => $tipe,
            'anak' => $request->user()->anak,
            'kelompokUsia' => $kelompokUsia,
            'soal' => $this->soal($kelompokUsia)->groupBy('aspek'),
        ]);
    }

    /**
     * Tujuan tab "Tes" saat tidak ada tes yang bisa diisi: post-test terkunci atau keduanya selesai.
     * Hanya tampilan — gating tetap di show()/store(). Skor tidak ditampilkan ke orang tua.
     */
    public function status(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user->sudahPretest()) {
            return redirect()->route('pretest');
        }
        if (! $user->sudahKuesioner('post') && $user->bolehPosttest()) {
            return redirect()->route('posttest');
        }

        $hasil = $user->hasilKuesioner()->pluck('submitted_at', 'tipe_sesi');
        $kelompokUsia = $user->anak?->kelompok_usia;
        $materiIds = Materi::where('kelompok_usia', $kelompokUsia)->pluck('id');

        return view('kuesioner.status', [
            'hasil' => $hasil,
            'kelompokUsia' => $kelompokUsia,
            'totalMateri' => $materiIds->count(),
            'materiSelesai' => $user->progressMateri()->where('materi_selesai', true)->whereIn('materi_id', $materiIds)->count(),
        ]);
    }

    public function store(Request $request, string $tipe): RedirectResponse
    {
        // Final: kiriman kedua ditolak di backend, bukan sekadar disembunyikan di UI.
        if ($tolak = $this->tolak($request->user(), $tipe)) {
            return $tolak;
        }

        $kelompokUsia = $this->kelompokUsia($request->user(), $tipe);
        $soal = $this->soal($kelompokUsia);

        // Hanya soal kelompok usia ini; soal_id asing ditolak lewat array:ids.
        $rules = ['jawaban' => ['required', 'array:'.$soal->pluck('id')->implode(',')]];
        $attributes = [];
        foreach ($soal as $s) {
            $rules["jawaban.{$s->id}"] = ['required', Rule::in(array_keys(KuesionerSoal::JAWABAN))];
            $attributes["jawaban.{$s->id}"] = 'jawaban "'.$s->pertanyaan.'"';
        }
        $jawaban = array_map(fn ($j) => $j === '1', $request->validate($rules, [], $attributes)['jawaban']);

        try {
            DB::transaction(function () use ($request, $tipe, $kelompokUsia, $soal, $jawaban) {
                $hasil = HasilKuesioner::create([
                    'user_id' => $request->user()->id,
                    'tipe_sesi' => $tipe,
                    'kelompok_usia' => $kelompokUsia,
                    'submitted_at' => now(),
                ] + SkorService::skorPerkembangan($soal, $jawaban));
                $hasil->detail()->createMany($soal->map(fn ($s) => [
                    'soal_id' => $s->id,
                    'jawaban_responden' => $jawaban[$s->id],
                ]));
            });
        } catch (UniqueConstraintViolationException) {
            // Kiriman ganda bersamaan: unique (user_id, tipe_sesi) menolak yang kedua.
            return $this->sudahDikirim($tipe);
        }

        return redirect()->route('dashboard')
            ->with('status', self::LABEL[$tipe].' berhasil dikirim. Terima kasih!');
    }

    /** Null bila boleh mengisi; selain itu redirect dengan alasan. */
    private function tolak(User $user, string $tipe): ?RedirectResponse
    {
        if ($user->sudahKuesioner($tipe)) {
            return $this->sudahDikirim($tipe);
        }

        if ($tipe === 'post' && ! $user->bolehPosttest()) {
            return redirect()->route('materi.index')
                ->with('status', 'Post-test terbuka setelah seluruh materi kelompok usia anak ditandai selesai.');
        }

        return null;
    }

    /**
     * Pre-test: kelompok usia anak saat ini (lewat UsiaAnakService di model Anak).
     * Post-test: kelompok yang dijawab saat pre-test, supaya item pre & post sama
     * walau anak sudah naik kelompok usia.
     */
    private function kelompokUsia(User $user, string $tipe): string
    {
        return $tipe === 'post'
            ? $user->hasilKuesioner()->where('tipe_sesi', 'pre')->value('kelompok_usia')
            : $user->anak()->firstOrFail()->kelompok_usia;
    }

    private function soal(string $kelompokUsia)
    {
        $soal = KuesionerSoal::where('kelompok_usia', $kelompokUsia)->orderBy('urutan')->orderBy('id')->get();
        abort_if($soal->isEmpty(), 404);

        return $soal;
    }

    private function sudahDikirim(string $tipe): RedirectResponse
    {
        return redirect()->route('dashboard')
            ->with('status', self::LABEL[$tipe].' sudah dikirim dan tidak dapat diubah.');
    }
}
