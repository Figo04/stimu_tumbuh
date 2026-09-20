<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

/** View-only (PRD §3.2) — reset/pengulangan test di luar lingkup (PRD §3.3). */
class HasilTestController extends Controller
{
    public function index(): View
    {
        return view('admin.hasil-test.index', [
            // Unique (user_id, tipe_sesi) → maksimal satu baris per tipe, aman di-keyBy di view.
            'responden' => User::with('hasilKuesioner')->orderBy('kode_responden')->paginate(25),
        ]);
    }

    public function show(User $responden): View
    {
        return view('admin.hasil-test.show', [
            'responden' => $responden,
            // Urutan soal ikut bank soal (pengetahuan dulu, lalu urutan) — disortir di koleksi
            // karena urutannya milik tabel soal, bukan tabel detail.
            'hasil' => $responden->hasilKuesioner()->with('detail.soal')->get()
                // 'pengetahuan' < 'sikap' secara alfabet → pengetahuan tampil lebih dulu.
                ->each(fn ($h) => $h->setRelation('detail', $h->detail
                    ->sortBy(fn ($d) => [$d->soal->tipe, $d->soal->urutan])->values()))
                ->keyBy('tipe_sesi'),
        ]);
    }
}
