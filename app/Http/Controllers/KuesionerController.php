<?php

namespace App\Http\Controllers;

use App\Models\HasilKuesioner;
use App\Models\KuesionerSoal;
use App\Services\SkorService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KuesionerController extends Controller
{
    public function pretest(Request $request): View|RedirectResponse
    {
        if ($request->user()->sudahPretest()) {
            return $this->sudahDikirim();
        }

        return view('kuesioner.pretest', ['soal' => $this->soal()->groupBy('tipe')]);
    }

    public function storePretest(Request $request): RedirectResponse
    {
        // Final: kiriman kedua ditolak di backend, bukan sekadar disembunyikan di UI.
        if ($request->user()->sudahPretest()) {
            return $this->sudahDikirim();
        }

        $soal = $this->soal();
        $rules = ['jawaban' => ['required', 'array:'.$soal->pluck('id')->implode(',')]];
        $attributes = [];
        foreach ($soal->groupBy('tipe') as $tipe => $items) {
            $pilihan = $tipe === 'pengetahuan' ? KuesionerSoal::JAWABAN_PENGETAHUAN : KuesionerSoal::JAWABAN_SIKAP;
            foreach ($items->values() as $i => $s) {
                $rules["jawaban.{$s->id}"] = ['required', Rule::in(array_keys($pilihan))];
                $attributes["jawaban.{$s->id}"] = 'soal '.$tipe.' nomor '.($i + 1);
            }
        }
        $jawaban = $request->validate($rules, [], $attributes)['jawaban'];

        try {
            DB::transaction(function () use ($request, $soal, $jawaban) {
                $hasil = HasilKuesioner::create([
                    'user_id' => $request->user()->id,
                    'tipe_sesi' => 'pre',
                    'submitted_at' => now(),
                ] + SkorService::skorKuesioner($soal, $jawaban));
                $hasil->detail()->createMany($soal->map(fn ($s) => [
                    'soal_id' => $s->id,
                    'jawaban_responden' => $jawaban[$s->id],
                ]));
            });
        } catch (UniqueConstraintViolationException) {
            // Kiriman ganda bersamaan: unique (user_id, tipe_sesi) menolak yang kedua.
            return $this->sudahDikirim();
        }

        return redirect()->route('dashboard')->with('status', 'Pre-test berhasil dikirim. Terima kasih!');
    }

    private function soal()
    {
        return KuesionerSoal::orderBy('tipe')->orderBy('urutan')->get();
    }

    private function sudahDikirim(): RedirectResponse
    {
        return redirect()->route('dashboard')->with('status', 'Pre-test sudah dikirim dan tidak dapat diubah.');
    }
}
