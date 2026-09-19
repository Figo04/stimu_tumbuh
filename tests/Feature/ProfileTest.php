<?php

namespace Tests\Feature;

use App\Models\Anak;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $override = []): array
    {
        return array_replace_recursive([
            'nama' => 'Test User',
            'email' => 'test@example.com',
            'hubungan_dengan_anak' => 'ayah',
            'no_hp' => '081234567890',
            'pendidikan_terakhir' => 'SMA',
            'kecamatan' => 'Sukajadi',
            'anak' => [
                'nama_inisial' => 'AB',
                'tanggal_lahir' => now()->subMonths(4)->toDateString(),
                'jenis_kelamin' => 'P',
                'bb_lahir_gram' => 3100,
                'kondisi_lahir' => 'sehat',
            ],
        ], $override);
    }

    public function test_profile_page_is_displayed(): void
    {
        $user = Anak::factory()->create()->user;

        $this->actingAs($user)->get('/profile')
            ->assertOk()
            ->assertSee($user->kode_responden);
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = Anak::factory()->create()->user;

        $response = $this
            ->actingAs($user)
            ->patch('/profile', $this->payload());

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->nama);
        $this->assertSame('test@example.com', $user->email);
        $this->assertSame('ayah', $user->hubungan_dengan_anak);
        $this->assertSame('Sukajadi', $user->kecamatan);
        $this->assertNull($user->email_verified_at);

        $this->assertSame(1, Anak::where('user_id', $user->id)->count());
        $this->assertSame('AB', $user->anak->nama_inisial);
        $this->assertSame('P', $user->anak->jenis_kelamin);
        $this->assertSame(3100, $user->anak->bb_lahir_gram);
        $this->assertSame('3-6', $user->anak->kelompok_usia);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = Anak::factory()->create()->user;

        $this->actingAs($user)
            ->patch('/profile', $this->payload(['email' => $user->email]))
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_kode_responden_cannot_be_changed(): void
    {
        $user = Anak::factory()->create()->user;
        $kode = $user->kode_responden;

        $this->actingAs($user)
            ->patch('/profile', $this->payload(['kode_responden' => 'RSP-999']))
            ->assertSessionHasNoErrors();

        $this->assertSame($kode, $user->refresh()->kode_responden);
    }

    public function test_tanggal_lahir_rules(): void
    {
        // Anak yang kini > 36 bulan tetap bisa menyimpan profil dengan tanggal lahir tersimpan.
        $lahir = now()->subMonths(40)->toDateString();
        $user = Anak::factory()->create(['tanggal_lahir' => $lahir])->user;

        $this->actingAs($user)
            ->patch('/profile', $this->payload(['anak' => ['tanggal_lahir' => $lahir]]))
            ->assertSessionHasNoErrors();

        // Tapi tidak boleh dimundurkan lebih jauh, dan tidak boleh di masa depan.
        foreach ([now()->subMonths(41), now()->addDay()] as $tanggal) {
            $this->actingAs($user)
                ->patch('/profile', $this->payload(['anak' => ['tanggal_lahir' => $tanggal->toDateString()]]))
                ->assertSessionHasErrors('anak.tanggal_lahir');
        }
    }

    public function test_account_cannot_be_deleted_by_responden(): void
    {
        $user = Anak::factory()->create()->user;

        $this->actingAs($user)->delete('/profile')->assertMethodNotAllowed();

        $this->assertNotNull($user->fresh());
    }
}
