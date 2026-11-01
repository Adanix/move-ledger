<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\MoveStatus;
use App\Enums\PropertyType;
use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\Move;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MoveManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $coordinator;
    protected Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->coordinator = User::create([
            'name' => 'Move Dispatcher',
            'email' => 'dispatch@test.com',
            'role' => UserRole::Coordinator,
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->customer = Customer::create([
            'name' => 'Robert Johnson',
            'email' => 'robert@example.com',
            'phone' => '555-987-6543',
        ]);
    }

    public function test_coordinator_can_create_a_move_and_it_seeds_default_rooms(): void
    {
        $response = $this->actingAs($this->coordinator)->post('/moves', [
            'customer_id' => $this->customer->id,
            'origin_address' => '742 Evergreen Terrace, Springfield, OR',
            'destination_address' => '1000 Ocean Drive, Miami, FL',
            'scheduled_move_date' => now()->addDays(7)->toDateString(),
            'property_type' => PropertyType::House3Br->value,
            'assigned_surveyor_id' => $this->coordinator->id,
            'notes' => 'Watch out for pets.',
        ]);

        $move = Move::latest()->first();

        $this->assertNotNull($move);
        $response->assertRedirect("/moves/{$move->id}/survey");
        $this->assertMatchesRegularExpression('/^MV-\d{4}-\d{5}$/', $move->reference_code);
        $this->assertSame(MoveStatus::Draft, $move->status);

        // Verify default rooms were seeded
        $this->assertGreaterThanOrEqual(4, $move->rooms()->count());
        $this->assertTrue($move->rooms()->where('name', 'Living Room')->exists());
        $this->assertTrue($move->rooms()->where('name', 'Kitchen & Dining')->exists());
    }

    public function test_move_status_transition_works_through_status_controller(): void
    {
        $move = Move::create([
            'reference_code' => 'ML-2611-ABCD',
            'customer_id' => $this->customer->id,
            'origin_address' => '123 Test St',
            'destination_address' => '456 Test St',
            'scheduled_move_date' => now()->addDays(2),
            'property_type' => PropertyType::Apartment2Br,
            'status' => MoveStatus::Draft,
        ]);

        $response = $this->actingAs($this->coordinator)->patch("/moves/{$move->id}/status", [
            'status' => MoveStatus::InSurvey->value,
        ]);

        $response->assertRedirect("/moves/{$move->id}/survey");
        $move->refresh();
        $this->assertSame(MoveStatus::InSurvey, $move->status);
    }

    public function test_user_can_add_custom_room_to_move(): void
    {
        $move = Move::create([
            'reference_code' => 'ML-2611-ROOM',
            'customer_id' => $this->customer->id,
            'origin_address' => '123 Test St',
            'destination_address' => '456 Test St',
            'scheduled_move_date' => now()->addDays(2),
            'property_type' => PropertyType::Apartment2Br,
            'status' => MoveStatus::Draft,
        ]);

        $response = $this->actingAs($this->coordinator)->post("/moves/{$move->id}/rooms", [
            'name' => 'Wine Cellar',
            'floor_level' => 'Basement',
        ]);

        $response->assertRedirect();
        $this->assertTrue($move->rooms()->where('name', 'Wine Cellar')->exists());
    }
}
