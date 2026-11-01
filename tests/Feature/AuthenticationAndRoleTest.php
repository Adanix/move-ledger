<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationAndRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_staff_can_login_with_valid_credentials(): void
    {
        $user = User::create([
            'name' => 'Active Admin',
            'email' => 'admin@test.com',
            'role' => UserRole::Admin,
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_inactive_staff_cannot_login(): void
    {
        User::create([
            'name' => 'Inactive Surveyor',
            'email' => 'inactive@test.com',
            'role' => UserRole::Surveyor,
            'password' => Hash::make('password123'),
            'is_active' => false,
        ]);

        $response = $this->post('/login', [
            'email' => 'inactive@test.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_authenticated_inactive_user_is_blocked_by_middleware(): void
    {
        $user = User::create([
            'name' => 'Deactivated User',
            'email' => 'deactivated@test.com',
            'role' => UserRole::Surveyor,
            'password' => Hash::make('password123'),
            'is_active' => false,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_surveyor_cannot_access_admin_user_management(): void
    {
        $surveyor = User::create([
            'name' => 'Surveyor Dave',
            'email' => 'dave@test.com',
            'role' => UserRole::Surveyor,
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $response = $this->actingAs($surveyor)->get('/admin/users');

        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_user_management(): void
    {
        $admin = User::create([
            'name' => 'Admin Boss',
            'email' => 'boss@test.com',
            'role' => UserRole::Admin,
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin/users');

        $response->assertStatus(200);
        $response->assertSee('Staff Accounts');
    }

    public function test_user_can_logout_successfully(): void
    {
        $user = User::create([
            'name' => 'Staff Dave',
            'email' => 'staff@test.com',
            'role' => UserRole::Coordinator,
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $expectedRedirect = config('app.demo_mode', true) ? '/demo/login' : '/';
        $response->assertRedirect($expectedRedirect);
        $this->assertGuest();
    }
}
