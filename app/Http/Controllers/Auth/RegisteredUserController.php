<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public const PENDIDIKAN = ['Tidak sekolah', 'SD', 'SMP', 'SMA', 'D3', 'S1', 'S2+'];

    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register', ['pendidikan' => self::PENDIDIKAN]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'hubungan_dengan_anak' => ['required', 'in:ibu,ayah,pengasuh'],
            'no_hp' => ['nullable', 'regex:/^[0-9]+$/', 'max:20'],
            'pendidikan_terakhir' => ['nullable', Rule::in(self::PENDIDIKAN)],
            'pekerjaan' => ['nullable', 'string', 'max:255'],
            'kecamatan' => ['nullable', 'string', 'max:255'],
            'alamat' => ['nullable', 'string', 'max:1000'],
        ]);

        // Kode dari id: berurutan (RSP-001, RSP-002, …) dan aman dari registrasi bersamaan.
        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                ...$data,
                'kode_responden' => (string) Str::uuid(),
                'password' => Hash::make($data['password']),
            ]);
            $user->update(['kode_responden' => sprintf('RSP-%03d', $user->id)]);

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
