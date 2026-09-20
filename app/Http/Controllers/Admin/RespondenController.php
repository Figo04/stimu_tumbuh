<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

/** View-only (PRD §3.2) — tidak ada rute create/edit/destroy. */
class RespondenController extends Controller
{
    public function index(): View
    {
        return view('admin.responden.index', [
            'responden' => User::with('anak')->orderBy('kode_responden')->paginate(25),
        ]);
    }

    public function show(User $responden): View
    {
        return view('admin.responden.show', [
            'responden' => $responden->load('anak'),
        ]);
    }
}
