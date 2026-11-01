<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $coordinator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->coordinator = User::create([
            'name' => 'Customer Coordinator',
            'email' => 'coord@test.com',
            'role' => UserRole::Coordinator,
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
    }

    public function test_coordinator_can_list_customers_and_search(): void
    {
        Customer::create([
            'name' => 'Alice Cooper',
            'email' => 'alice@example.com',
            'phone' => '555-1111',
        ]);

        Customer::create([
            'name' => 'Bob Marley',
            'email' => 'bob@example.com',
            'phone' => '555-2222',
        ]);

        $response = $this->actingAs($this->coordinator)->get('/customers?search=Alice');

        $response->assertStatus(200);
        $response->assertSee('Alice Cooper');
        $response->assertDontSee('Bob Marley');
    }

    public function test_coordinator_can_create_customer(): void
    {
        $response = $this->actingAs($this->coordinator)->post('/customers', [
            'name' => 'Charlie Brown',
            'email' => 'charlie@example.com',
            'phone' => '555-3333',
            'secondary_phone' => '555-4444',
            'notes' => 'VIP Customer',
        ]);

        $customer = Customer::where('email', 'charlie@example.com')->first();
        $this->assertNotNull($customer);
        $response->assertRedirect("/customers/{$customer->id}");
    }

    public function test_coordinator_can_view_and_update_customer(): void
    {
        $customer = Customer::create([
            'name' => 'David Bowie',
            'email' => 'david@example.com',
            'phone' => '555-5555',
        ]);

        $viewResponse = $this->actingAs($this->coordinator)->get("/customers/{$customer->id}");
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('David Bowie');

        $updateResponse = $this->actingAs($this->coordinator)->put("/customers/{$customer->id}", [
            'name' => 'David Bowie Updated',
            'email' => 'david.bowie@example.com',
            'phone' => '555-9999',
            'secondary_phone' => null,
            'notes' => 'Updated notes',
        ]);

        $updateResponse->assertRedirect("/customers/{$customer->id}");
        $customer->refresh();
        $this->assertSame('David Bowie Updated', $customer->name);
        $this->assertSame('david.bowie@example.com', $customer->email);
    }
}
