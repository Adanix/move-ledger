<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class PublicAndDemoPagesTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $coordinatorUser;
    protected User $surveyorUser;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('app.demo_mode', true);

        $this->adminUser = User::create([
            'name' => 'Demo Administrator',
            'email' => 'admin@moveledger.test',
            'role' => UserRole::Admin,
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->coordinatorUser = User::create([
            'name' => 'Demo Coordinator',
            'email' => 'dispatch@moveledger.test',
            'role' => UserRole::Coordinator,
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->surveyorUser = User::create([
            'name' => 'Demo Field Surveyor',
            'email' => 'surveyor@moveledger.test',
            'role' => UserRole::Surveyor,
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
    }

    public function test_guest_can_access_landing_page_without_auth(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('MoveLedger');
        $response->assertSee('Mobile-First Relocation Inventory');
        $response->assertSee('Explore Live Demo');
        $response->assertSee('Read Feature Guide');
        $response->assertSee('mailto:nixresponding@gmail.com', false);
        $response->assertSee('nixresponding@gmail.com');
    }

    public function test_guest_can_access_documentation_page_without_auth(): void
    {
        $response = $this->get('/docs');

        $response->assertStatus(200);
        $response->assertSee('Product Feature Guide');
        $response->assertSee('Need something more tailored?');
        $response->assertSee('mailto:nixresponding@gmail.com', false);
        $response->assertSee('nixresponding@gmail.com');
        $response->assertSee('On-Site Survey Workspace');
        $response->assertSee('Cryptographic Manifest Sign-Off');
    }

    public function test_demo_login_screen_renders_all_three_persona_cards(): void
    {
        $response = $this->get('/demo/login');

        $response->assertStatus(200);
        $response->assertSee('Select a Demonstration Persona');
        $response->assertSee('Continue as Administrator');
        $response->assertSee('Continue as Move Coordinator');
        $response->assertSee('Continue as Field Surveyor');

        // Standard persona selection screen must NOT contain username/password input fields
        $response->assertDontSee('type="password"', false);
        $response->assertDontSee('name="password"', false);
        $response->assertDontSee('name="email"', false);
    }

    public function test_quick_login_authenticates_admin_persona_and_regenerates_session(): void
    {
        $originalSessionId = session()->getId();

        $response = $this->post('/demo/quick-login', [
            'persona' => 'admin',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->adminUser);
        $this->assertNotEquals($originalSessionId, session()->getId());
    }

    public function test_quick_login_authenticates_coordinator_persona(): void
    {
        $response = $this->post('/demo/quick-login', [
            'persona' => 'coordinator',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->coordinatorUser);
    }

    public function test_quick_login_authenticates_surveyor_persona(): void
    {
        $response = $this->post('/demo/quick-login', [
            'persona' => 'surveyor',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->surveyorUser);
    }

    public function test_quick_login_rejects_invalid_persona_keys(): void
    {
        $response = $this->post('/demo/quick-login', [
            'persona' => 'hacker_role',
        ]);

        $response->assertSessionHasErrors('persona');
        $this->assertGuest();
    }

    public function test_authenticated_persona_cannot_exceed_assigned_permissions(): void
    {
        // Field surveyor cannot access Admin User Management
        $response = $this->actingAs($this->surveyorUser)->get('/admin/users');
        $response->assertStatus(403);

        // Admin can access Admin User Management
        $adminResponse = $this->actingAs($this->adminUser)->get('/admin/users');
        $adminResponse->assertStatus(200);
    }

    public function test_logout_redirects_to_demo_login_when_demo_mode_is_enabled(): void
    {
        Config::set('app.demo_mode', true);

        $response = $this->actingAs($this->surveyorUser)->post('/logout');

        $response->assertRedirect('/demo/login');
        $this->assertGuest();
    }

    public function test_logout_redirects_to_home_when_demo_mode_is_disabled(): void
    {
        Config::set('app.demo_mode', false);

        $response = $this->actingAs($this->surveyorUser)->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_demo_login_and_quick_login_are_inaccessible_when_demo_mode_disabled(): void
    {
        Config::set('app.demo_mode', false);

        $loginResponse = $this->get('/demo/login');
        $loginResponse->assertStatus(404);

        $quickLoginResponse = $this->post('/demo/quick-login', [
            'persona' => 'admin',
        ]);
        $quickLoginResponse->assertStatus(404);
        $this->assertGuest();
    }
}
