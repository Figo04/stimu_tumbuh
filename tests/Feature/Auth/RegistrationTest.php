<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    private function payload(array $override = []): array
    {
        return array_replace_recursive([
            'nama' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'hubungan_dengan_anak' => 'ibu',
            'umur' => '28',
            'no_hp' => '081234567890',
            'pendidikan_terakhir' => 'SMA',
            'pekerjaan' => 'Ibu rumah tangga',
            'asuransi' => 'BPJS Kesehatan',
            'kecamatan' => 'Sukajadi',
            'alamat' => 'Jl. Melati 1',
            'anak' => [
                'nama_inisial' => 'AB',
                'tanggal_lahir' => now()->subMonthsNoOverflow(14)->toDateString(),
                'jenis_kelamin' => 'P',
                'bb_lahir_gram' => '3000',
                'pb_lahir_cm' => '49',
                'usia_gestasi_minggu' => '39',
                'jenis_persalinan' => 'normal',
                'kondisi_lahir' => ['sehat'],
                'anemia_hamil' => '0',
                'penyakit_hamil' => '0',
                'penyakit_keturunan' => '0',
                'kebiasaan_ibu' => ['tidak_ada'],
            ],
        ], $override);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', $this->payload());

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'umur' => 28,
            'no_hp' => '081234567890',
            'pendidikan_terakhir' => 'SMA',
            'asuransi' => 'BPJS Kesehatan',
            'kecamatan' => 'Sukajadi',
        ]);
    }

    public function test_data_primer_wajib_kecuali_lingkar_kepala(): void
    {
        $kosong = $this->payload();
        foreach (['umur', 'no_hp', 'pendidikan_terakhir', 'pekerjaan', 'asuransi', 'kecamatan', 'alamat'] as $k) {
            unset($kosong[$k]);
        }
        foreach (['bb_lahir_gram', 'pb_lahir_cm', 'usia_gestasi_minggu', 'jenis_persalinan', 'kondisi_lahir', 'anemia_hamil', 'penyakit_hamil', 'penyakit_keturunan', 'kebiasaan_ibu'] as $k) {
            unset($kosong['anak'][$k]);
        }

        $this->post('/register', $kosong)->assertSessionHasErrors([
            'umur', 'no_hp', 'pendidikan_terakhir', 'pekerjaan', 'asuransi', 'kecamatan', 'alamat',
            'anak.bb_lahir_gram', 'anak.pb_lahir_cm', 'anak.usia_gestasi_minggu', 'anak.jenis_persalinan', 'anak.kondisi_lahir',
            'anak.anemia_hamil', 'anak.penyakit_hamil', 'anak.penyakit_keturunan', 'anak.kebiasaan_ibu',
        ])->assertSessionDoesntHaveErrors('anak.lingkar_kepala_cm');
        $this->assertGuest();
    }

    public function test_riwayat_ibu_ada_wajib_ditulis_dan_tidak_ada_berdiri_sendiri(): void
    {
        $this->post('/register', $this->payload(['anak' => [
            'penyakit_hamil' => '1',
            'penyakit_keturunan' => '1',
            'kebiasaan_ibu' => ['tidak_ada', 'merokok', 'lainnya'],
        ]]))->assertSessionHasErrors([
            'anak.penyakit_hamil_ket', 'anak.penyakit_keturunan_ket', 'anak.kebiasaan_ibu', 'anak.kebiasaan_ibu_lain',
        ]);
        $this->assertGuest();
    }

    public function test_kode_responden_is_sequential(): void
    {
        $this->post('/register', $this->payload(['email' => 'a@example.com']));
        auth()->logout();
        $this->post('/register', $this->payload(['email' => 'b@example.com']));

        $this->assertSame(['RSP-001', 'RSP-002'], User::orderBy('id')->pluck('kode_responden')->all());
    }

    public function test_registration_creates_anak(): void
    {
        $lahir = now()->subMonthsNoOverflow(14)->toDateString();
        $this->post('/register', $this->payload(['anak' => [
            'nama_inisial' => 'AB',
            'tanggal_lahir' => $lahir,
            'jenis_kelamin' => 'L',
            'bb_lahir_gram' => '2400',
            'pb_lahir_cm' => '47.5',
            'kondisi_lahir' => ['prematur', 'bblr'],
            'jenis_persalinan' => 'sc',
            'anemia_hamil' => '1',
            'penyakit_hamil' => '1',
            'penyakit_hamil_ket' => 'Hipertensi',
            // "Tidak" dengan sisa isian: keterangan dibuang.
            'penyakit_keturunan_ket' => 'sisa ketikan',
            'kebiasaan_ibu' => ['merokok', 'lainnya'],
            'kebiasaan_ibu_lain' => 'Begadang',
        ]]))->assertSessionHasNoErrors();

        $anak = User::firstWhere('email', 'test@example.com')->anak;
        $this->assertSame($lahir, $anak->tanggal_lahir->toDateString());
        $this->assertSame(['L', 2400, '47.5', ['prematur', 'bblr'], 'sc'], [
            $anak->jenis_kelamin, $anak->bb_lahir_gram, $anak->pb_lahir_cm, $anak->kondisi_lahir, $anak->jenis_persalinan,
        ]);
        $this->assertNull($anak->penyakit_keturunan_ket);
        $this->assertSame([
            'Anemia saat hamil' => 'Anemia',
            'Penyakit saat hamil' => 'Ada: Hipertensi',
            'Penyakit keturunan' => 'Tidak ada',
            'Kebiasaan ibu' => 'Merokok, Lainnya: Begadang',
        ], $anak->riwayatIbu());
    }

    public function test_hanya_anak_12_sampai_24_bulan_yang_bisa_daftar(): void
    {
        $anak = fn ($tanggal) => ['anak' => ['tanggal_lahir' => $tanggal->toDateString()]];

        // 11 bulan (sehari sebelum 12), 25 bulan, dan masa depan ditolak.
        foreach ([now()->subMonthsNoOverflow(12)->addDay(), now()->subMonthsNoOverflow(25), now()->addDay()] as $tanggal) {
            $this->post('/register', $this->payload($anak($tanggal)))->assertSessionHasErrors('anak.tanggal_lahir');
        }
        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);

        // Tepat 24 bulan masih boleh.
        $this->post('/register', $this->payload($anak(now()->subMonthsNoOverflow(24))))->assertSessionHasNoErrors();
        $this->assertAuthenticated();
    }

    public function test_invalid_hubungan_and_no_hp_are_rejected(): void
    {
        $this->post('/register', $this->payload(['hubungan_dengan_anak' => 'kakek', 'no_hp' => '08-12ab']))
            ->assertSessionHasErrors(['hubungan_dengan_anak', 'no_hp']);

        $this->assertGuest();
    }
}
