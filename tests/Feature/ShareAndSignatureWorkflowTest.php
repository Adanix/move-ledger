<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\DeliveryStatus;
use App\Enums\ItemCategory;
use App\Enums\MoveStatus;
use App\Enums\OriginCondition;
use App\Enums\PackingType;
use App\Enums\PropertyType;
use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Move;
use App\Models\MoveRoom;
use App\Models\User;
use App\Services\MoveLifecycleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ShareAndSignatureWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $coordinator;
    protected Customer $customer;
    protected Move $move;
    protected MoveRoom $room;
    protected MoveLifecycleService $lifecycleService;

    protected string $sampleSignatureUri = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAUCAYAAADPym6aAAAANElEQVR4nO3PsQ0AAAyDsPz/dHpGUcTAjtOkC+V7QIgQeEJoCaElhJYQWkJoCaElhNYM5ABtR8iali0t0gAAAABJRU5ErkJggg==';

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->lifecycleService = app(MoveLifecycleService::class);

        $this->coordinator = User::create([
            'name' => 'Dispatch Dan',
            'email' => 'dan@test.com',
            'role' => UserRole::Coordinator,
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->customer = Customer::create([
            'name' => 'Sarah Connor',
            'email' => 'sarah@example.com',
            'phone' => '555-900-1000',
        ]);

        $this->move = Move::create([
            'reference_code' => 'ML-2611-SIGN',
            'customer_id' => $this->customer->id,
            'origin_address' => '100 Cyberdyne Way, Los Angeles, CA',
            'destination_address' => '500 Tech Blvd, Austin, TX',
            'scheduled_move_date' => now()->addDays(4),
            'property_type' => PropertyType::House3Br,
            'status' => MoveStatus::InSurvey,
            'assigned_surveyor_id' => $this->coordinator->id,
        ]);

        $this->room = MoveRoom::create([
            'move_id' => $this->move->id,
            'name' => 'Living Room',
            'sort_order' => 1,
        ]);

        InventoryItem::create([
            'move_id' => $this->move->id,
            'move_room_id' => $this->room->id,
            'tag_number' => '#001',
            'name' => 'Sofa 3-Seat',
            'item_category' => ItemCategory::Furniture,
            'packing_type' => PackingType::UnpackedFurniture,
            'origin_condition' => OriginCondition::Good,
            'unit_volume_cuft' => '50.00',
            'unit_weight_lbs' => '150.00',
            'quantity' => 1,
            'declared_value' => '1000.00',
            'is_fragile' => false,
            'is_high_value' => false,
            'requires_disassembly' => false,
            'delivery_status' => DeliveryStatus::Pending,
        ]);
    }

    public function test_pin_protected_share_link_requires_pin_before_viewing(): void
    {
        $share = $this->lifecycleService->createShareLink(
            move: $this->move,
            pin: '1234',
            expireDays: 7,
            allowSign: true,
            actor: $this->coordinator
        );

        // Accessing share link without session should redirect to PIN entry
        $response = $this->get("/share/{$share->token}");
        $response->assertRedirect("/share/{$share->token}/pin");

        // Incorrect PIN
        $postIncorrect = $this->post("/share/{$share->token}/pin", [
            'pin' => '9999',
        ]);
        $postIncorrect->assertSessionHasErrors('pin');

        // Correct PIN
        $postCorrect = $this->post("/share/{$share->token}/pin", [
            'pin' => '1234',
        ]);
        $postCorrect->assertRedirect("/share/{$share->token}");

        // Now authorized
        $followResponse = $this->get("/share/{$share->token}");
        $followResponse->assertStatus(200);
        $followResponse->assertSee('ML-2611-SIGN');
    }

    public function test_customer_can_sign_origin_manifest_via_share_portal(): void
    {
        $share = $this->lifecycleService->createShareLink(
            move: $this->move,
            pin: null, // No PIN for this direct test
            expireDays: 7,
            allowSign: true,
            actor: $this->coordinator
        );

        $response = $this->post("/share/{$share->token}/sign/origin", [
            'signer_name' => 'Sarah Connor',
            'signer_role' => 'customer',
            'legal_acknowledgment' => '1',
            'signature_data_uri' => $this->sampleSignatureUri,
        ]);

        $response->assertRedirect("/share/{$share->token}");

        $this->move->refresh();
        $this->assertSame(MoveStatus::OriginSigned, $this->move->status);
        $this->assertNotNull($this->move->validOriginSignature);
        $this->assertSame('Sarah Connor', $this->move->validOriginSignature->signer_name);
        $this->assertNotEmpty($this->move->validOriginSignature->manifest_snapshot_hash);
        $this->assertNotEmpty($this->move->validOriginSignature->signature_image_path);
    }

    public function test_admin_can_unlock_move_for_revision_which_invalidates_prior_signature(): void
    {
        // First sign the move
        $share = $this->lifecycleService->createShareLink(
            move: $this->move,
            pin: null,
            expireDays: 7,
            allowSign: true,
            actor: $this->coordinator
        );

        $this->post("/share/{$share->token}/sign/origin", [
            'signer_name' => 'Sarah Connor',
            'signer_role' => 'customer',
            'legal_acknowledgment' => '1',
            'signature_data_uri' => $this->sampleSignatureUri,
        ]);

        $this->move->refresh();
        $this->assertSame(MoveStatus::OriginSigned, $this->move->status);

        // Now admin unlocks for revision
        $admin = User::create([
            'name' => 'Admin Boss',
            'email' => 'boss2@test.com',
            'role' => UserRole::Admin,
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->patch("/moves/{$this->move->id}/status", [
            'action' => 'unlock_for_revision',
            'reason' => 'Customer added 3 more items to inventory.',
        ]);

        $response->assertRedirect("/moves/{$this->move->id}/survey");

        $this->move->refresh();
        $this->assertSame(MoveStatus::InSurvey, $this->move->status);
        $this->assertNull($this->move->validOriginSignature); // Prior signature now invalidated
    }
}
