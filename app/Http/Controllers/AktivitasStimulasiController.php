<?php

namespace App\Http\Controllers;

use App\Models\AktivitasStimulasi;
use App\Models\Materi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Kalender stimulasi: berkelanjutan, multi-entri, bebas diedit/dihapus (bukan pola final kuesioner). */
class AktivitasStimulasiController extends Controller
{
    public function index(Request $request): View
    {
        // ponytail: tanpa paginasi — riwayat per responden kecil; tambah paginate() bila entri ratusan.
        $riwayat = $request->user()->aktivitasStimulasi()
            ->with('materi')
            ->latest('tanggal')->latest('id')
            ->get();

        return view('aktivitas.index', compact('riwayat'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->user()->aktivitasStimulasi()->create($request->validate($this->aturan(true)));

        return redirect()->route('aktivitas.index')->with('status', 'Entri stimulasi tersimpan.');
    }

    public function edit(Request $request, string $aktivitas): View
    {
        return view('aktivitas.edit', ['entri' => $this->entri($request, $aktivitas)]);
    }

    public function update(Request $request, string $aktivitas): RedirectResponse
    {
        $entri = $this->entri($request, $aktivitas);

        // Entri dari tab Praktik: aspek & materi mengikuti materinya, tidak bisa diubah.
        $entri->update($request->validate($this->aturan($entri->materi_id === null)));

        return redirect()->route('aktivitas.index')->with('status', 'Entri stimulasi diperbarui.');
    }

    public function destroy(Request $request, string $aktivitas): RedirectResponse
    {
        $this->entri($request, $aktivitas)->delete();

        return redirect()->route('aktivitas.index')->with('status', 'Entri stimulasi dihapus.');
    }

    /** Dicari lewat relasi user: entri milik responden lain → 404. */
    private function entri(Request $request, string $id): AktivitasStimulasi
    {
        return $request->user()->aktivitasStimulasi()->findOrFail($id);
    }

    private function aturan(bool $aspekBolehDiubah): array
    {
        return array_filter([
            'tanggal' => ['required', 'date', 'before_or_equal:today'],
            'aspek' => $aspekBolehDiubah ? ['required', Rule::in(array_keys(Materi::ASPEK))] : null,
            'jenis_stimulasi' => ['nullable', 'string', 'max:255'],
            'durasi_menit' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'pelaku' => ['required', Rule::in(array_keys(AktivitasStimulasi::PELAKU))],
            'respons_anak' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
