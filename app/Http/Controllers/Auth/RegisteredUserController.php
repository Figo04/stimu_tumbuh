<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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

            'anak.nama_inisial' => ['required', 'string', 'max:50'],
            // Sasaran penelitian: anak 0–36 bulan saat registrasi.
            'anak.tanggal_lahir' => ['required', 'date', 'before_or_equal:today', 'after_or_equal:'.now()->subMonths(36)->toDateString()],
            'anak.jenis_kelamin' => ['required', 'in:L,P'],
            'anak.bb_lahir_gram' => ['nullable', 'integer', 'min:300', 'max:6000'],
            'anak.pb_lahir_cm' => ['nullable', 'numeric', 'min:20', 'max:70'],
            'anak.lingkar_kepala_cm' => ['nullable', 'numeric', 'min:20', 'max:60'],
            'anak.usia_gestasi_minggu' => ['nullable', 'integer', 'min:20', 'max:45'],
            'anak.jenis_persalinan' => ['nullable', Rule::in(array_keys(Anak::JENIS_PERSALINAN))],
            'anak.kondisi_lahir' => ['nullable', Rule::in(array_keys(Anak::KONDISI_LAHIR))],
        ]);

        $anak = Arr::pull($data, 'anak');

        // Kode dari id: berurutan (RSP-001, RSP-002, …) dan aman dari registrasi bersamaan.
        // User + anak satu transaksi: tidak ada akun tanpa data anak.
        $user = DB::transaction(function () use ($data, $anak) {
            $user = User::create([
                ...$data,
                'kode_responden' => (string) Str::uuid(),
                'password' => Hash::make($data['password']),
            ]);
            $user->update(['kode_responden' => sprintf('RSP-%03d', $user->id)]);
            $user->anak()->create($anak);

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
