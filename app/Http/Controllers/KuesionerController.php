<?php

namespace App\Http\Controllers;

use App\Models\HasilKuesioner;
use App\Models\KuesionerSoal;
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
        return $this->tolak($request->user(), $tipe)
            ?? view('kuesioner.pretest', ['tipe' => $tipe, 'soal' => $this->soal()->groupBy('tipe')]);
    }

    public function store(Request $request, string $tipe): RedirectResponse
    {
        // Final: kiriman kedua ditolak di backend, bukan sekadar disembunyikan di UI.
        if ($tolak = $this->tolak($request->user(), $tipe)) {
            return $tolak;
        }

        $soal = $this->soal();
        $rules = ['jawaban' => ['required', 'array:'.$soal->pluck('id')->implode(',')]];
        $attributes = [];
        foreach ($soal->groupBy('tipe') as $tipeSoal => $items) {
            $pilihan = $tipeSoal === 'pengetahuan' ? KuesionerSoal::JAWABAN_PENGETAHUAN : KuesionerSoal::JAWABAN_SIKAP;
            foreach ($items->values() as $i => $s) {
                $rules["jawaban.{$s->id}"] = ['required', Rule::in(array_keys($pilihan))];
                $attributes["jawaban.{$s->id}"] = 'soal '.$tipeSoal.' nomor '.($i + 1);
            }
        }
        $jawaban = $request->validate($rules, [], $attributes)['jawaban'];

        try {
            DB::transaction(function () use ($request, $tipe, $soal, $jawaban) {
                $hasil = HasilKuesioner::create([
                    'user_id' => $request->user()->id,
                    'tipe_sesi' => $tipe,
                    'submitted_at' => now(),
                ] + SkorService::skorKuesioner($soal, $jawaban));
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

    private function soal()
    {
        return KuesionerSoal::orderBy('tipe')->orderBy('urutan')->get();
    }

    private function sudahDikirim(string $tipe): RedirectResponse
    {
        return redirect()->route('dashboard')
            ->with('status', self::LABEL[$tipe].' sudah dikirim dan tidak dapat diubah.');
    }
}
