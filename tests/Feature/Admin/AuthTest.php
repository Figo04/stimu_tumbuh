<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): Admin
    {
        return Admin::create(['nama' => 'Admin', 'email' => 'admin@example.com', 'password' => 'rahasia123']);
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $this->get('/admin')->assertOk();
    }

    public function test_admin_can_login_and_logout(): void
    {
        $this->admin();

        $this->post('/admin', ['email' => 'admin@example.com', 'password' => 'rahasia123'])
            ->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated('admin');
        $this->assertGuest('web');

        $this->post('/admin/logout')->assertRedirect(route('admin.login'));
        $this->assertGuest('admin');
    }

    public function test_admin_cannot_login_with_wrong_password(): void
    {
        $this->admin();

        $this->post('/admin', ['email' => 'admin@example.com', 'password' => 'salah']);

        $this->assertGuest('admin');
    }

    public function test_parent_credentials_cannot_login_as_admin(): void
    {
        $user = User::factory()->create();

        $this->post('/admin', ['email' => $user->email, 'password' => 'password']);

        $this->assertGuest('admin');
    }

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $this->get('/admin/dashboard')->assertRedirect(route('admin.login'));
    }

    public function test_parent_cannot_access_admin_area(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin/dashboard')
            ->assertRedirect(route('admin.login'));
    }

    public function test_admin_cannot_access_parent_area(): void
    {
        $this->actingAs($this->admin(), 'admin');
        // actingAs() menjadikan 'admin' guard default; request asli selalu mulai dari 'web'.
        Auth::shouldUse('web');

        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_logged_in_admin_visiting_login_goes_to_dashboard(): void
    {
        $this->actingAs($this->admin(), 'admin')
            ->get('/admin')
            ->assertRedirect(route('admin.dashboard'));
    }
}
