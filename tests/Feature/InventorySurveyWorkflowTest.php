<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\CatalogCategory;
use App\Enums\DeliveryStatus;
use App\Enums\ItemCategory;
use App\Enums\MoveStatus;
use App\Enums\OriginCondition;
use App\Enums\PackingType;
use App\Enums\PropertyType;
use App\Enums\UserRole;
use App\Models\CatalogItem;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Move;
use App\Models\MoveRoom;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InventorySurveyWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $surveyor;
    protected Customer $customer;
    protected Move $move;
    protected MoveRoom $room;
    protected CatalogItem $catalogItem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->surveyor = User::create([
            'name' => 'Field Surveyor Sam',
            'email' => 'sam@test.com',
            'role' => UserRole::Surveyor,
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->customer = Customer::create([
            'name' => 'Jane Miller',
            'email' => 'jane@example.com',
            'phone' => '555-333-4444',
        ]);

        $this->move = Move::create([
            'reference_code' => 'ML-2611-SURV',
            'customer_id' => $this->customer->id,
            'origin_address' => '500 Pine St, Seattle, WA',
            'destination_address' => '800 Oak St, Portland, OR',
            'scheduled_move_date' => now()->addDays(5),
            'property_type' => PropertyType::House3Br,
            'assigned_surveyor_id' => $this->surveyor->id,
            'status' => MoveStatus::InSurvey,
        ]);

        $this->room = MoveRoom::create([
            'move_id' => $this->move->id,
            'name' => 'Master Bedroom',
            'sort_order' => 1,
        ]);

        $this->catalogItem = CatalogItem::create([
            'name' => 'Queen Bed & Frame',
            'category' => CatalogCategory::Bedroom,
            'item_type' => ItemCategory::Furniture,
            'default_volume_cuft' => '65.00',
            'default_weight_lbs' => '150.00',
            'is_fragile_default' => false,
            'requires_disassembly_default' => true,
            'is_active' => true,
        ]);
    }

    public function test_surveyor_can_add_item_from_catalog(): void
    {
        $response = $this->actingAs($this->surveyor)->post("/moves/{$this->move->id}/items", [
            'move_room_id' => $this->room->id,
            'catalog_item_id' => $this->catalogItem->id,
            'name' => $this->catalogItem->name,
            'item_category' => $this->catalogItem->item_type->value,
            'unit_volume_cuft' => '65.00',
            'unit_weight_lbs' => '150.00',
            'quantity' => 1,
            'packing_type' => PackingType::UnpackedFurniture->value,
            'origin_condition' => OriginCondition::Good->value,
            'is_fragile' => '0',
            'is_high_value' => '0',
            'requires_disassembly' => '1',
            'declared_value' => '800.00',
        ]);

        $response->assertRedirect();

        $item = InventoryItem::first();
        $this->assertNotNull($item);
        $this->assertSame('#001', $item->tag_number);
        $this->assertSame('Queen Bed & Frame', $item->name);

        $this->room->refresh();
        $this->move->refresh();

        $this->assertSame(1, $this->room->room_items_count);
        $this->assertSame('65.00', (string) $this->room->room_volume_cuft);
        $this->assertSame(1, $this->move->total_items_count);
        $this->assertSame('65.00', (string) $this->move->total_volume_cuft);
    }

    public function test_condition_notes_required_when_item_has_pre_existing_damage(): void
    {
        // When origin_condition is pre_existing_damage, condition_notes must not be empty
        $response = $this->actingAs($this->surveyor)->post("/moves/{$this->move->id}/items", [
            'move_room_id' => $this->room->id,
            'name' => 'Antique Coffee Table',
            'item_category' => ItemCategory::Furniture->value,
            'unit_volume_cuft' => '15.00',
            'unit_weight_lbs' => '40.00',
            'quantity' => 1,
            'packing_type' => PackingType::UnpackedFurniture->value,
            'origin_condition' => OriginCondition::PreExistingDamage->value,
            'condition_notes' => '', // Intentionally left blank
            'is_fragile' => '0',
            'is_high_value' => '0',
            'requires_disassembly' => '0',
            'declared_value' => '250.00',
        ]);

        $response->assertSessionHasErrors('condition_notes');
        $this->assertDatabaseCount('inventory_items', 0);
    }

    public function test_quantity_stepper_updates_item_quantity_and_recalculates(): void
    {
        $item = InventoryItem::create([
            'move_id' => $this->move->id,
            'move_room_id' => $this->room->id,
            'tag_number' => '#001',
            'name' => 'Medium Box',
            'item_category' => ItemCategory::BoxCarton,
            'packing_type' => PackingType::CompanyPacked,
            'origin_condition' => OriginCondition::Good,
            'unit_volume_cuft' => '3.00',
            'unit_weight_lbs' => '30.00',
            'quantity' => 2,
            'declared_value' => '0.00',
            'is_fragile' => false,
            'is_high_value' => false,
            'requires_disassembly' => false,
            'delivery_status' => DeliveryStatus::Pending,
        ]);

        // Increment quantity
        $response = $this->actingAs($this->surveyor)->patch("/moves/{$this->move->id}/items/{$item->id}/quantity", [
            'action' => 'increment',
        ]);

        $response->assertRedirect();
        $item->refresh();
        $this->assertSame(3, $item->quantity);

        $this->move->refresh();
        $this->assertSame(3, $this->move->total_boxes_count);
        $this->assertSame('9.00', (string) $this->move->total_volume_cuft);
    }

    public function test_surveyor_can_upload_photo_for_inventory_item(): void
    {
        Storage::fake('public');

        $item = InventoryItem::create([
            'move_id' => $this->move->id,
            'move_room_id' => $this->room->id,
            'tag_number' => '#001',
            'name' => 'Dining Table',
            'item_category' => ItemCategory::Furniture,
            'packing_type' => PackingType::UnpackedFurniture,
            'origin_condition' => OriginCondition::PreExistingDamage,
            'condition_notes' => 'Scratch on top left corner',
            'unit_volume_cuft' => '30.00',
            'unit_weight_lbs' => '100.00',
            'quantity' => 1,
            'is_fragile' => false,
            'is_high_value' => false,
            'requires_disassembly' => false,
            'delivery_status' => DeliveryStatus::Pending,
        ]);

        $file = UploadedFile::fake()->create('scratch.jpg', 50, 'image/jpeg');

        $response = $this->actingAs($this->surveyor)->post("/moves/{$this->move->id}/items/{$item->id}/photos", [
            'photos' => [$file],
            'caption' => 'Pre-existing scratch on table leg',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('inventory_item_photos', [
            'inventory_item_id' => $item->id,
            'caption' => 'Pre-existing scratch on table leg',
        ]);
    }

    public function test_modifying_inventory_is_prevented_when_move_is_locked(): void
    {
        $this->move->update(['status' => MoveStatus::OriginSigned]);

        $response = $this->actingAs($this->surveyor)->post("/moves/{$this->move->id}/items", [
            'move_room_id' => $this->room->id,
            'name' => 'Illegal Item After Lock',
            'item_category' => ItemCategory::Furniture->value,
            'unit_volume_cuft' => '10.00',
            'unit_weight_lbs' => '20.00',
            'quantity' => 1,
            'packing_type' => PackingType::UnpackedFurniture->value,
            'origin_condition' => OriginCondition::Good->value,
            'is_fragile' => '0',
            'is_high_value' => '0',
            'requires_disassembly' => '0',
        ]);

        $response->assertStatus(403);
    }
}
