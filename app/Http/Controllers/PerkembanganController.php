<?php

namespace App\Http\Controllers;

use App\Models\ItemPerkembangan;
use App\Services\SkorService;
use App\Services\UsiaAnakService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Penilaian perkembangan: berkelanjutan, boleh dinilai ulang kapan saja (tanpa is_locked). */
class PerkembanganController extends Controller
{
    public function index(Request $request): View
    {
        $anak = $request->user()->anak()->firstOrFail();
        $riwayat = $request->user()->penilaianPerkembangan()->latest('tanggal_penilaian')->latest('id')->get();

        return view('perkembangan.index', [
            'anak' => $anak,
            'item' => $this->item($anak->kelompok_usia)->groupBy('aspek'),
            'terakhir' => $riwayat->first(),
            'riwayat' => $riwayat,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $anak = $request->user()->anak()->firstOrFail();
        $item = $this->item($anak->kelompok_usia);
        abort_if($item->isEmpty(), 404);

        // Hanya item kelompok usia anak saat ini; item_id asing ditolak lewat array:ids.
        $rules = ['jawaban' => ['required', 'array:'.$item->pluck('id')->implode(',')]];
        $attributes = [];
        foreach ($item as $i) {
            $rules["jawaban.{$i->id}"] = ['required', Rule::in(['1', '0'])];
            $attributes["jawaban.{$i->id}"] = 'jawaban "'.$i->pertanyaan.'"';
        }
        $jawaban = array_map(fn ($j) => $j === '1', $request->validate($rules, [], $attributes)['jawaban']);

        // Tanggal = hari ini, agar snapshot usia cocok dengan item yang ditampilkan.
        DB::transaction(function () use ($request, $anak, $item, $jawaban) {
            $penilaian = $request->user()->penilaianPerkembangan()->create([
                'anak_id' => $anak->id,
                'tanggal_penilaian' => today(),
                'usia_bulan' => UsiaAnakService::usiaBulan($anak->tanggal_lahir),
                'kelompok_usia' => $anak->kelompok_usia,
            ] + SkorService::skorPerkembangan($item, $jawaban));
            $penilaian->detail()->createMany($item->map(fn ($i) => [
                'item_id' => $i->id,
                'jawaban' => $jawaban[$i->id],
            ]));
        });

        return redirect()->route('perkembangan.index')->with('status', 'Penilaian perkembangan tersimpan.');
    }

    private function item(string $kelompokUsia)
    {
        return ItemPerkembangan::where('kelompok_usia', $kelompokUsia)->orderBy('urutan')->get();
    }
}
