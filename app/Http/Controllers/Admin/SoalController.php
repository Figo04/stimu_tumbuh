<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KuesionerSoal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Satu-satunya CRUD penuh milik admin (PRD §3.2); menu admin lain view-only. */
class SoalController extends Controller
{
    public function index(): View
    {
        return view('admin.soal.index', [
            // Dikelompokkan di PHP, bukan lewat ORDER BY tipe: urutan tampil milik KuesionerSoal::TIPE.
            'soal' => KuesionerSoal::withCount('detail')->orderBy('urutan')->orderBy('id')->get()->groupBy('tipe'),
        ]);
    }

    public function create(): View
    {
        return view('admin.soal.create', [
            'soal' => null,
            'urutanBerikutnya' => KuesionerSoal::max('urutan') + 1,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        KuesionerSoal::create($this->data($request));

        return redirect()->route('admin.soal.index')->with('status', 'Soal ditambahkan.');
    }

    public function edit(KuesionerSoal $soal): View
    {
        return view('admin.soal.edit', [
            'soal' => $soal->loadCount('detail'),
        ]);
    }

    public function update(Request $request, KuesionerSoal $soal): RedirectResponse
    {
        // Tipe tidak ikut diubah (lihat aturan()): jawaban lama B/S vs SS..STS akan salah terskor.
        $soal->update($this->data($request, $soal));

        return redirect()->route('admin.soal.index')->with('status', 'Soal diperbarui.');
    }

    public function destroy(KuesionerSoal $soal): RedirectResponse
    {
        // FK cascadeOnDelete: menghapus soal yang sudah dijawab akan menghapus jawaban
        // responden tanpa jejak, dan skor hasil_kuesioner tidak ikut dihitung ulang.
        if ($jumlah = $soal->detail()->count()) {
            return redirect()->route('admin.soal.index')
                ->with('galat', "Soal sudah dijawab $jumlah responden dan tidak bisa dihapus. Ubah isinya, atau hubungi developer bila soal ini memang harus ditarik.");
        }

        $soal->delete();

        return redirect()->route('admin.soal.index')->with('status', 'Soal dihapus.');
    }

    /** Jawaban benar & reverse_scored dinormalkan menurut tipe (PRD §5). */
    private function data(Request $request, ?KuesionerSoal $soal = null): array
    {
        $data = $request->validate($this->aturan($soal));
        $tipe = $soal?->tipe ?? $data['tipe'];

        return [...$data,
            'jawaban_benar' => $tipe === 'pengetahuan' ? $data['jawaban_benar'] ?? null : null,
            'reverse_scored' => $tipe === 'sikap' && $request->boolean('reverse_scored'),
        ];
    }

    private function aturan(?KuesionerSoal $soal): array
    {
        return array_filter([
            // Tipe hanya ditetapkan saat membuat soal: mengubahnya membuat jawaban yang
            // sudah masuk tidak cocok dengan skala skornya. Salah pilih → hapus & buat ulang.
            'tipe' => $soal ? null : ['required', Rule::in(array_keys(KuesionerSoal::TIPE))],
            'pertanyaan' => ['required', 'string', 'max:1000'],
            'jawaban_benar' => [
                Rule::requiredIf(($soal?->tipe ?? request('tipe')) === 'pengetahuan'),
                'nullable',
                Rule::in(array_keys(KuesionerSoal::JAWABAN_PENGETAHUAN)),
            ],
            'urutan' => ['required', 'integer', 'min:1', 'max:255'],
        ]);
    }
}
