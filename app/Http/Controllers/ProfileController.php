<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user()->load('anak'),
            'pendidikan' => RegisteredUserController::PENDIDIKAN,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $anak = Arr::pull($data, 'anak');
        $user = $request->user()->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        DB::transaction(function () use ($user, $anak) {
            $user->save();
            $user->anak()->updateOrCreate([], $anak);
        });

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    // Hapus akun sengaja tidak disediakan (keputusan Sesi 25): cascade FK akan
    // menghapus seluruh data penelitian responden. Penghapusan lewat tim peneliti.
}
