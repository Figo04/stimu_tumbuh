<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ItemPerkembangan;
use App\Models\KuesionerSoal;
use App\Services\UsiaAnakService;
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
            // Dikelompokkan di PHP: urutan tampil milik UsiaAnakService::KELOMPOK_USIA.
            'soal' => KuesionerSoal::withCount('detail')->orderBy('urutan')->orderBy('id')->get()->groupBy('kelompok_usia'),
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
        // Kelompok usia & kategori tidak ikut diubah (lihat aturan()).
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

    private function data(Request $request, ?KuesionerSoal $soal = null): array
    {
        return $request->validate(array_filter([
            // Kelompok usia & kategori hanya ditetapkan saat membuat soal: mengubahnya memindahkan
            // soal yang sudah dijawab ke kelompok/skor aspek lain, sehingga pre & post tak lagi
            // sebanding. Salah pilih → hapus & buat ulang.
            'kelompok_usia' => $soal ? null : ['required', Rule::in(UsiaAnakService::KELOMPOK_USIA)],
            'aspek' => $soal ? null : ['required', Rule::in(array_keys(ItemPerkembangan::ASPEK))],
            'pertanyaan' => ['required', 'string', 'max:1000'],
            'urutan' => ['required', 'integer', 'min:1', 'max:255'],
        ]));
    }
}
