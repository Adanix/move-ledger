<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\DeliveryStatus;
use App\Enums\ItemCategory;
use App\Enums\MoveStatus;
use App\Enums\OriginCondition;
use App\Enums\PackingType;
use App\Enums\PropertyType;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Move;
use App\Models\MoveRoom;
use App\Services\InventoryCalculationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryCalculationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected InventoryCalculationService $calcService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calcService = new InventoryCalculationService();
    }

    public function test_it_generates_sequential_tag_numbers_for_a_move(): void
    {
        $customer = Customer::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '555-1234',
        ]);

        $move = Move::create([
            'reference_code' => 'ML-2611-TEST',
            'customer_id' => $customer->id,
            'origin_address' => '123 Main St',
            'destination_address' => '456 Elm St',
            'scheduled_move_date' => now()->addDays(5),
            'property_type' => PropertyType::House3Br,
            'status' => MoveStatus::InSurvey,
        ]);

        $room = MoveRoom::create([
            'move_id' => $move->id,
            'name' => 'Living Room',
            'sort_order' => 1,
        ]);

        $this->assertSame('#001', $this->calcService->generateNextTagNumber($move));

        InventoryItem::create([
            'move_id' => $move->id,
            'move_room_id' => $room->id,
            'tag_number' => '#001',
            'name' => 'Sofa',
            'item_category' => ItemCategory::Furniture,
            'packing_type' => PackingType::UnpackedFurniture,
            'origin_condition' => OriginCondition::Good,
            'unit_volume_cuft' => '50.00',
            'unit_weight_lbs' => '180.00',
            'quantity' => 1,
            'declared_value' => '1200.00',
            'is_fragile' => false,
            'is_high_value' => false,
            'requires_disassembly' => false,
            'delivery_status' => DeliveryStatus::Pending,
        ]);

        $this->assertSame('#002', $this->calcService->generateNextTagNumber($move));
    }

    public function test_it_recalculates_room_and_move_totals_with_exact_decimals(): void
    {
        $customer = Customer::create([
            'name' => 'Alice Smith',
            'email' => 'alice@example.com',
            'phone' => '555-4321',
        ]);

        $move = Move::create([
            'reference_code' => 'ML-2611-CALC',
            'customer_id' => $customer->id,
            'origin_address' => '100 Alpha St',
            'destination_address' => '200 Beta St',
            'scheduled_move_date' => now()->addDays(3),
            'property_type' => PropertyType::Apartment2Br,
            'status' => MoveStatus::Draft,
        ]);

        $room1 = MoveRoom::create([
            'move_id' => $move->id,
            'name' => 'Living Room',
            'sort_order' => 1,
        ]);

        $room2 = MoveRoom::create([
            'move_id' => $move->id,
            'name' => 'Kitchen',
            'sort_order' => 2,
        ]);

        // Item 1 in Room 1: Sofa (qty 2, unit_vol 45.50 each = 91.00, unit_weight 120.25 each = 240.50, val 500)
        InventoryItem::create([
            'move_id' => $move->id,
            'move_room_id' => $room1->id,
            'tag_number' => '#001',
            'name' => 'Armchair',
            'item_category' => ItemCategory::Furniture,
            'packing_type' => PackingType::UnpackedFurniture,
            'origin_condition' => OriginCondition::Good,
            'unit_volume_cuft' => '45.50',
            'unit_weight_lbs' => '120.25',
            'quantity' => 2,
            'declared_value' => '500.00',
            'is_fragile' => false,
            'is_high_value' => false,
            'requires_disassembly' => false,
            'delivery_status' => DeliveryStatus::Pending,
        ]);

        // Item 2 in Room 2: Box Cartons (qty 10, unit_vol 4.50 each = 45.00, unit_weight 25.00 each = 250.00, val 250)
        InventoryItem::create([
            'move_id' => $move->id,
            'move_room_id' => $room2->id,
            'tag_number' => '#002',
            'name' => 'Dish Pack Box',
            'item_category' => ItemCategory::BoxCarton,
            'packing_type' => PackingType::CompanyPacked,
            'origin_condition' => OriginCondition::Good,
            'unit_volume_cuft' => '4.50',
            'unit_weight_lbs' => '25.00',
            'quantity' => 10,
            'declared_value' => '250.00',
            'is_fragile' => true,
            'is_high_value' => false,
            'requires_disassembly' => false,
            'delivery_status' => DeliveryStatus::Pending,
        ]);

        $this->calcService->recalculateMoveTotals($move);

        $room1->refresh();
        $room2->refresh();
        $move->refresh();

        // Room 1 verification
        $this->assertSame(2, $room1->room_items_count);
        $this->assertSame('91.00', (string) $room1->room_volume_cuft);

        // Room 2 verification
        $this->assertSame(10, $room2->room_items_count);
        $this->assertSame('45.00', (string) $room2->room_volume_cuft);

        // Move Totals verification
        $this->assertSame(12, $move->total_items_count);
        $this->assertSame(10, $move->total_boxes_count);
        $this->assertSame('136.00', (string) $move->total_volume_cuft);
        $this->assertSame('490.50', (string) $move->total_weight_lbs);
        $this->assertSame('750.00', (string) $move->total_declared_value);
    }
}
