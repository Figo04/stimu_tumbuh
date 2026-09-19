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
        return [
            'nama' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'hubungan_dengan_anak' => 'ibu',
            'anak' => [
                'nama_inisial' => 'AB',
                'tanggal_lahir' => now()->subMonths(8)->toDateString(),
                'jenis_kelamin' => 'P',
            ],
            ...$override,
        ];
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', $this->payload([
            'no_hp' => '081234567890',
            'pendidikan_terakhir' => 'SMA',
            'kecamatan' => 'Sukajadi',
        ]));

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'no_hp' => '081234567890',
            'pendidikan_terakhir' => 'SMA',
            'kecamatan' => 'Sukajadi',
        ]);
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
        $lahir = now()->subMonths(8)->toDateString();
        $this->post('/register', $this->payload(['anak' => [
            'nama_inisial' => 'AB',
            'tanggal_lahir' => $lahir,
            'jenis_kelamin' => 'L',
            'bb_lahir_gram' => '2400',
            'pb_lahir_cm' => '47.5',
            'kondisi_lahir' => 'bblr',
            'jenis_persalinan' => 'sc',
        ]]));

        $anak = User::firstWhere('email', 'test@example.com')->anak;
        $this->assertSame($lahir, $anak->tanggal_lahir->toDateString());
        $this->assertSame(['L', 2400, '47.5', 'bblr', 'sc'], [
            $anak->jenis_kelamin, $anak->bb_lahir_gram, $anak->pb_lahir_cm, $anak->kondisi_lahir, $anak->jenis_persalinan,
        ]);
    }

    public function test_tanggal_lahir_outside_0_36_months_is_rejected(): void
    {
        foreach ([now()->addDay(), now()->subMonths(36)->subDay()] as $tanggal) {
            $this->post('/register', $this->payload(['anak' => [
                'nama_inisial' => 'AB', 'tanggal_lahir' => $tanggal->toDateString(), 'jenis_kelamin' => 'P',
            ]]))->assertSessionHasErrors('anak.tanggal_lahir');
        }

        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_invalid_hubungan_and_no_hp_are_rejected(): void
    {
        $this->post('/register', $this->payload(['hubungan_dengan_anak' => 'kakek', 'no_hp' => '08-12ab']))
            ->assertSessionHasErrors(['hubungan_dengan_anak', 'no_hp']);

        $this->assertGuest();
    }
}
