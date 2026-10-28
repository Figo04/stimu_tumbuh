<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\User;
use App\Services\UsiaAnakService;
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

    public const ASURANSI = ['BPJS Kesehatan', 'Asuransi swasta', 'BPJS & asuransi swasta', 'Tidak punya'];

    /**
     * Aturan identitas orang tua + anak, dipakai bersama registrasi dan profil.
     * $user diisi saat update profil: email miliknya sendiri tidak dianggap duplikat.
     */
    public static function aturanIdentitas(?User $user = null): array
    {
        // Sasaran penelitian: anak 12–24 bulan (dihitung lewat UsiaAnakService, tepat juga di akhir bulan).
        // Di profil, tanggal lahir yang sudah tersimpan tetap lolos meski anak kini sudah lebih tua.
        $usiaDaftar = function (string $atribut, mixed $nilai, \Closure $gagal) use ($user) {
            if ($nilai === $user?->anak?->tanggal_lahir?->toDateString()) {
                return;
            }
            $usia = UsiaAnakService::usiaBulan($nilai);
            if ($usia < UsiaAnakService::USIA_DAFTAR_MIN || $usia > UsiaAnakService::USIA_DAFTAR_MAKS) {
                $gagal('Penelitian ini untuk anak usia '.UsiaAnakService::USIA_DAFTAR_MIN.'–'.UsiaAnakService::USIA_DAFTAR_MAKS.' bulan.');
            }
        };

        $tidakAdaSendiri = function (string $atribut, array $nilai, \Closure $gagal) {
            if (in_array('tidak_ada', $nilai, true) && count($nilai) > 1) {
                $gagal('Pilih "Tidak ada" saja, atau pilih kebiasaannya.');
            }
        };

        // Data primer penelitian (revisi klien 29 Okt 2026): semua wajib kecuali lingkar kepala.
        return [
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user?->id)],
            'hubungan_dengan_anak' => ['required', 'in:ibu,ayah,pengasuh'],
            'umur' => ['required', 'integer', 'min:15', 'max:70'],
            'no_hp' => ['required', 'regex:/^[0-9]+$/', 'max:20'],
            'pendidikan_terakhir' => ['required', Rule::in(self::PENDIDIKAN)],
            'pekerjaan' => ['required', 'string', 'max:255'],
            'asuransi' => ['required', Rule::in(self::ASURANSI)],
            'kecamatan' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string', 'max:1000'],

            'anak.nama_inisial' => ['required', 'string', 'max:50'],
            'anak.tanggal_lahir' => ['bail', 'required', 'date', 'before_or_equal:today', $usiaDaftar],
            'anak.jenis_kelamin' => ['required', 'in:L,P'],
            'anak.bb_lahir_gram' => ['required', 'integer', 'min:300', 'max:6000'],
            'anak.pb_lahir_cm' => ['required', 'numeric', 'min:20', 'max:70'],
            'anak.lingkar_kepala_cm' => ['nullable', 'numeric', 'min:20', 'max:60'],
            'anak.usia_gestasi_minggu' => ['required', 'integer', 'min:20', 'max:45'],
            'anak.jenis_persalinan' => ['required', Rule::in(array_keys(Anak::JENIS_PERSALINAN))],
            'anak.kondisi_lahir' => ['required', 'array'],
            'anak.kondisi_lahir.*' => [Rule::in(array_keys(Anak::KONDISI_LAHIR))],

            'anak.anemia_hamil' => ['required', 'boolean'],
            'anak.penyakit_hamil' => ['required', 'boolean'],
            'anak.penyakit_hamil_ket' => ['nullable', 'required_if_accepted:anak.penyakit_hamil', 'string', 'max:255'],
            'anak.penyakit_keturunan' => ['required', 'boolean'],
            'anak.penyakit_keturunan_ket' => ['nullable', 'required_if_accepted:anak.penyakit_keturunan', 'string', 'max:255'],
            'anak.kebiasaan_ibu' => ['bail', 'required', 'array', $tidakAdaSendiri],
            'anak.kebiasaan_ibu.*' => [Rule::in(array_keys(Anak::KEBIASAAN_IBU))],
            'anak.kebiasaan_ibu_lain' => ['nullable', Rule::requiredIf(fn () => in_array('lainnya', (array) request()->input('anak.kebiasaan_ibu'), true)), 'string', 'max:255'],
        ];
    }

    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register', ['pendidikan' => self::PENDIDIKAN, 'asuransi' => self::ASURANSI]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            ...self::aturanIdentitas(),
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
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
