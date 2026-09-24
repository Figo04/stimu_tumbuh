<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KonsultasiTest extends TestCase
{
    use RefreshDatabase;

    public function test_tombol_berisi_nomor_dan_kode_responden_sejak_sebelum_pretest(): void
    {
        config(['services.wa_konsultasi' => '6281234567890']);
        $user = User::factory()->create(['kode_responden' => 'RSP-007']);

        $this->actingAs($user)->get('/pretest')->assertOk()
            ->assertSee('https://wa.me/6281234567890?text=', false)
            ->assertSee(rawurlencode('saya responden RSP-007 ingin'), false);
    }

    public function test_tombol_disembunyikan_bila_nomor_kosong(): void
    {
        config(['services.wa_konsultasi' => null]);

        $this->actingAs(User::factory()->create())->get('/pretest')->assertOk()
            ->assertDontSee('wa.me', false);
    }
}
