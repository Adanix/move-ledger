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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DestinationDeliveryWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $surveyor;
    protected Customer $customer;
    protected Move $move;
    protected MoveRoom $room;
    protected InventoryItem $item1;
    protected InventoryItem $item2;

    protected string $sampleSignatureUri = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAUCAYAAADPym6aAAAANElEQVR4nO3PsQ0AAAyDsPz/dHpGUcTAjtOkC+V7QIgQeEJoCaElhJYQWkJoCaElhNYM5ABtR8iali0t0gAAAABJRU5ErkJggg==';

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->surveyor = User::create([
            'name' => 'Driver Dave',
            'email' => 'driver@test.com',
            'role' => UserRole::Surveyor,
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->customer = Customer::create([
            'name' => 'Michael Scott',
            'email' => 'michael@example.com',
            'phone' => '555-555-0199',
        ]);

        $this->move = Move::create([
            'reference_code' => 'ML-2611-DELV',
            'customer_id' => $this->customer->id,
            'origin_address' => '1725 Slough Ave, Scranton, PA',
            'destination_address' => '1000 Broadway, New York, NY',
            'scheduled_move_date' => now()->subDay(),
            'property_type' => PropertyType::Office,
            'status' => MoveStatus::InTransit,
            'assigned_surveyor_id' => $this->surveyor->id,
        ]);

        $this->room = MoveRoom::create([
            'move_id' => $this->move->id,
            'name' => 'Main Office',
            'sort_order' => 1,
        ]);

        $this->item1 = InventoryItem::create([
            'move_id' => $this->move->id,
            'move_room_id' => $this->room->id,
            'tag_number' => '#001',
            'name' => 'Executive Desk',
            'item_category' => ItemCategory::Furniture,
            'packing_type' => PackingType::UnpackedFurniture,
            'origin_condition' => OriginCondition::Good,
            'unit_volume_cuft' => '40.00',
            'unit_weight_lbs' => '120.00',
            'quantity' => 1,
            'declared_value' => '600.00',
            'is_fragile' => false,
            'is_high_value' => false,
            'requires_disassembly' => false,
            'delivery_status' => DeliveryStatus::Pending,
        ]);

        $this->item2 = InventoryItem::create([
            'move_id' => $this->move->id,
            'move_room_id' => $this->room->id,
            'tag_number' => '#002',
            'name' => 'Laser Printer',
            'item_category' => ItemCategory::Electronics,
            'packing_type' => PackingType::CompanyPacked,
            'origin_condition' => OriginCondition::Good,
            'unit_volume_cuft' => '5.00',
            'unit_weight_lbs' => '25.00',
            'quantity' => 1,
            'declared_value' => '300.00',
            'is_fragile' => true,
            'is_high_value' => false,
            'requires_disassembly' => false,
            'delivery_status' => DeliveryStatus::Pending,
        ]);
    }

    public function test_surveyor_can_mark_individual_item_delivery_status(): void
    {
        $response = $this->actingAs($this->surveyor)->patch("/moves/{$this->move->id}/items/{$this->item1->id}/delivery", [
            'delivery_status' => DeliveryStatus::Delivered->value,
        ]);

        $response->assertRedirect();
        $this->item1->refresh();
        $this->assertSame(DeliveryStatus::Delivered, $this->item1->delivery_status);
    }

    public function test_surveyor_can_record_delivery_exception_with_notes(): void
    {
        $response = $this->actingAs($this->surveyor)->patch("/moves/{$this->move->id}/items/{$this->item2->id}/delivery", [
            'delivery_status' => DeliveryStatus::DamagedInTransit->value,
            'delivery_exception_notes' => 'Toner cartridge leaked during bumpy road transit',
        ]);

        $response->assertRedirect();
        $this->item2->refresh();
        $this->assertSame(DeliveryStatus::DamagedInTransit, $this->item2->delivery_status);
        $this->assertSame('Toner cartridge leaked during bumpy road transit', $this->item2->delivery_exception_notes);
    }

    public function test_surveyor_can_mark_all_items_in_a_room_as_delivered(): void
    {
        $response = $this->actingAs($this->surveyor)->post("/moves/{$this->move->id}/delivery/mark-room", [
            'room_id' => $this->room->id,
        ]);

        $response->assertRedirect();

        $this->item1->refresh();
        $this->item2->refresh();
        $this->assertSame(DeliveryStatus::Delivered, $this->item1->delivery_status);
        $this->assertSame(DeliveryStatus::Delivered, $this->item2->delivery_status);
    }

    public function test_destination_sign_off_completes_the_move(): void
    {
        $response = $this->actingAs($this->surveyor)->post("/moves/{$this->move->id}/sign/destination", [
            'signer_name' => 'Michael Scott',
            'signer_role' => 'customer',
            'legal_acknowledgment' => '1',
            'signature_data_uri' => $this->sampleSignatureUri,
        ]);

        $response->assertRedirect("/moves/{$this->move->id}");

        $this->move->refresh();
        $this->assertSame(MoveStatus::Delivered, $this->move->status);
        $this->assertNotNull($this->move->validDestinationSignature);
        $this->assertSame('Michael Scott', $this->move->validDestinationSignature->signer_name);
    }
}
