<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\DeliveryStatus;
use App\Enums\ItemCategory;
use App\Enums\MoveStatus;
use App\Enums\OriginCondition;
use App\Enums\PackingType;
use App\Enums\PropertyType;
use App\Models\CatalogItem;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Move;
use App\Models\MoveRoom;
use App\Models\MoveShare;
use App\Models\MoveSignature;
use App\Models\User;
use App\Services\InventoryCalculationService;
use App\Services\MoveLifecycleService;
use App\Services\SignatureVerificationService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MoveSeeder extends Seeder
{
    public function run(): void
    {
        $calcService = app(InventoryCalculationService::class);
        $lifecycleService = app(MoveLifecycleService::class);
        $sigService = app(SignatureVerificationService::class);

        $admin = User::where('email', 'admin@moveledger.test')->first();
        $coordinator = User::where('email', 'dispatch@moveledger.test')->first();
        $surveyor = User::where('email', 'surveyor@moveledger.test')->first();

        // 1. Customers
        $customer1 = Customer::firstOrCreate(
            ['email' => 'emily.miller@example.com'],
            [
                'name' => 'John & Emily Miller',
                'phone' => '+1 (555) 234-5678',
                'secondary_phone' => '+1 (555) 876-1234',
                'notes' => 'Customer prefers morning walk-through. Has 2 cats (kept in bathroom during survey).',
            ]
        );

        $customer2 = Customer::firstOrCreate(
            ['email' => 'david.vance@example.com'],
            [
                'name' => 'David Robert Vance',
                'phone' => '+1 (555) 876-5432',
                'notes' => 'Building requires freight elevator booking 48 hours in advance.',
            ]
        );

        $customer3 = Customer::firstOrCreate(
            ['email' => 'operations@apexgrowth.test'],
            [
                'name' => 'Apex Growth Innovations Inc.',
                'phone' => '+1 (555) 901-2345',
                'notes' => 'Corporate relocation: 15 workstations, server racks, and executive suites.',
            ]
        );

        // -------------------------------------------------------------
        // MOVE 1: In Survey (House 3BR)
        // -------------------------------------------------------------
        $move1 = Move::firstOrCreate(
            ['reference_code' => 'MV-2026-00001'],
            [
                'customer_id' => $customer1->id,
                'assigned_surveyor_id' => $surveyor->id,
                'created_by_id' => $coordinator->id,
                'status' => MoveStatus::InSurvey,
                'property_type' => PropertyType::House3Br,
                'origin_address' => '742 Evergreen Terrace, Springfield, IL 62704',
                'origin_floor_access' => 'Single story rambler with wide driveway access',
                'destination_address' => '1042 Ocean Drive, Apt 4B, Miami Beach, FL 33139',
                'destination_floor_access' => '4th Floor, Service Elevator available 9am-4pm',
                'scheduled_move_date' => now()->addDays(3)->toDateString(),
                'survey_started_at' => now()->subHours(2),
                'notes' => 'High-priority residential relocation. Fragile china and piano included.',
            ]
        );

        if ($move1->rooms()->count() === 0) {
            $lifecycleService->seedDefaultRooms($move1);
        }

        $livingRoom = $move1->rooms()->where('name', 'Living Room')->first();
        $masterBed = $move1->rooms()->where('name', 'Master Bedroom')->first();
        $kitchen = $move1->rooms()->where('name', 'Kitchen & Dining')->first();
        $garage = $move1->rooms()->where('name', 'Garage / Storage')->first();

        if ($move1->items()->count() === 0) {
            $itemsData = [
                // Living Room
                ['room' => $livingRoom, 'name' => '3-Seater Sofa', 'category' => ItemCategory::Furniture, 'qty' => 1, 'vol' => 45.00, 'wt' => 150.00, 'pack' => PackingType::UnpackedFurniture, 'fragile' => false, 'disasm' => false, 'cond' => OriginCondition::PreExistingDamage, 'notes' => '2-inch scuff on front left armrest leather.'],
                ['room' => $livingRoom, 'name' => 'Coffee Table', 'category' => ItemCategory::Furniture, 'qty' => 1, 'vol' => 10.00, 'wt' => 45.00, 'pack' => PackingType::UnpackedFurniture, 'fragile' => false, 'disasm' => false, 'cond' => OriginCondition::FairWorn, 'notes' => null],
                ['room' => $livingRoom, 'name' => 'TV Stand / Media Console', 'category' => ItemCategory::Furniture, 'qty' => 1, 'vol' => 25.00, 'wt' => 80.00, 'pack' => PackingType::CompanyPacked, 'fragile' => false, 'disasm' => false, 'cond' => OriginCondition::Good, 'notes' => null],
                ['room' => $livingRoom, 'name' => 'Bookshelf (5-Tier)', 'category' => ItemCategory::Furniture, 'qty' => 2, 'vol' => 20.00, 'wt' => 70.00, 'pack' => PackingType::CompanyPacked, 'fragile' => false, 'disasm' => false, 'cond' => OriginCondition::Good, 'notes' => null],
                ['room' => $livingRoom, 'name' => 'Medium Box (3.0 cu ft)', 'category' => ItemCategory::BoxCarton, 'qty' => 5, 'vol' => 3.00, 'wt' => 35.00, 'pack' => PackingType::Pbo, 'fragile' => false, 'disasm' => false, 'cond' => OriginCondition::Good, 'notes' => null],

                // Master Bedroom
                ['room' => $masterBed, 'name' => 'King Bed & Mattress', 'category' => ItemCategory::Furniture, 'qty' => 1, 'vol' => 70.00, 'wt' => 180.00, 'pack' => PackingType::CompanyPacked, 'fragile' => false, 'disasm' => true, 'cond' => OriginCondition::Good, 'notes' => null],
                ['room' => $masterBed, 'name' => '6-Drawer Dresser', 'category' => ItemCategory::Furniture, 'qty' => 1, 'vol' => 35.00, 'wt' => 130.00, 'pack' => PackingType::UnpackedFurniture, 'fragile' => false, 'disasm' => false, 'cond' => OriginCondition::FairWorn, 'notes' => null],
                ['room' => $masterBed, 'name' => 'Nightstand', 'category' => ItemCategory::Furniture, 'qty' => 2, 'vol' => 6.00, 'wt' => 30.00, 'pack' => PackingType::UnpackedFurniture, 'fragile' => false, 'disasm' => false, 'cond' => OriginCondition::Good, 'notes' => null],
                ['room' => $masterBed, 'name' => 'Wardrobe Box with Bar', 'category' => ItemCategory::BoxCarton, 'qty' => 3, 'vol' => 15.00, 'wt' => 45.00, 'pack' => PackingType::CompanyPacked, 'fragile' => false, 'disasm' => false, 'cond' => OriginCondition::Good, 'notes' => null],

                // Kitchen & Dining
                ['room' => $kitchen, 'name' => 'Dining Table (6-Person)', 'category' => ItemCategory::Furniture, 'qty' => 1, 'vol' => 35.00, 'wt' => 110.00, 'pack' => PackingType::UnpackedFurniture, 'fragile' => false, 'disasm' => true, 'cond' => OriginCondition::Good, 'notes' => null],
                ['room' => $kitchen, 'name' => 'Dining Chair', 'category' => ItemCategory::Furniture, 'qty' => 6, 'vol' => 6.00, 'wt' => 18.00, 'pack' => PackingType::UnpackedFurniture, 'fragile' => false, 'disasm' => false, 'cond' => OriginCondition::Good, 'notes' => null],
                ['room' => $kitchen, 'name' => 'China Cabinet', 'category' => ItemCategory::Furniture, 'qty' => 1, 'vol' => 45.00, 'wt' => 160.00, 'pack' => PackingType::CompanyPacked, 'fragile' => true, 'disasm' => false, 'cond' => OriginCondition::Good, 'notes' => null],
                ['room' => $kitchen, 'name' => 'Dish Pack Box (Heavy Duty)', 'category' => ItemCategory::BoxCarton, 'qty' => 4, 'vol' => 5.00, 'wt' => 60.00, 'pack' => PackingType::CompanyPacked, 'fragile' => true, 'disasm' => false, 'cond' => OriginCondition::Good, 'notes' => null],

                // Garage
                ['room' => $garage, 'name' => 'Lawn Mower', 'category' => ItemCategory::Outdoor, 'qty' => 1, 'vol' => 15.00, 'wt' => 70.00, 'pack' => PackingType::UnpackedFurniture, 'fragile' => false, 'disasm' => false, 'cond' => OriginCondition::FairWorn, 'notes' => null],
                ['room' => $garage, 'name' => 'Mountain Bike', 'category' => ItemCategory::Outdoor, 'qty' => 2, 'vol' => 12.00, 'wt' => 32.00, 'pack' => PackingType::UnpackedFurniture, 'fragile' => false, 'disasm' => false, 'cond' => OriginCondition::Good, 'notes' => null],
            ];

            $tagNum = 1;
            foreach ($itemsData as $d) {
                if (! $d['room']) {
                    continue;
                }
                $catItem = CatalogItem::where('name', $d['name'])->first();

                InventoryItem::create([
                    'move_id' => $move1->id,
                    'move_room_id' => $d['room']->id,
                    'catalog_item_id' => $catItem?->id,
                    'tag_number' => sprintf('#%03d', $tagNum++),
                    'name' => $d['name'],
                    'item_category' => $d['category'],
                    'quantity' => $d['qty'],
                    'unit_volume_cuft' => $d['vol'],
                    'unit_weight_lbs' => $d['wt'],
                    'packing_type' => $d['pack'],
                    'is_fragile' => $d['fragile'],
                    'is_high_value' => false,
                    'requires_disassembly' => $d['disasm'],
                    'origin_condition' => $d['cond'],
                    'condition_notes' => $d['notes'],
                    'delivery_status' => DeliveryStatus::Pending,
                ]);
            }

            $calcService->recalculateMoveTotals($move1);
        }

        // -------------------------------------------------------------
        // MOVE 2: Pending Customer Sign-Off (Apartment 2BR) with Share Token
        // -------------------------------------------------------------
        $move2 = Move::firstOrCreate(
            ['reference_code' => 'MV-2026-00002'],
            [
                'customer_id' => $customer2->id,
                'assigned_surveyor_id' => $surveyor->id,
                'created_by_id' => $coordinator->id,
                'status' => MoveStatus::PendingSignoff,
                'property_type' => PropertyType::Apartment2Br,
                'origin_address' => '450 Sutter St, Apt 7C, San Francisco, CA 94108',
                'origin_floor_access' => '7th Floor, Freight elevator booked for moving day',
                'destination_address' => '1200 Grand Ave, Suite 300, Seattle, WA 98101',
                'destination_floor_access' => '3rd Floor, Passenger elevator only (max 10 ft items)',
                'scheduled_move_date' => now()->addDays(5)->toDateString(),
                'survey_started_at' => now()->subDay(),
                'notes' => 'Survey completed. Customer awaiting link to review manifest and sign.',
            ]
        );

        if ($move2->rooms()->count() === 0) {
            $lifecycleService->seedDefaultRooms($move2);
        }

        $aptLiving = $move2->rooms()->where('name', 'Living Room')->first();
        $aptBed = $move2->rooms()->where('name', 'Master Bedroom')->first();

        if ($move2->items()->count() === 0) {
            $tagNum = 1;
            $itemsData2 = [
                ['room' => $aptLiving, 'name' => '2-Seater Loveseat', 'category' => ItemCategory::Furniture, 'qty' => 1, 'vol' => 30.00, 'wt' => 100.00, 'pack' => PackingType::UnpackedFurniture, 'fragile' => false, 'disasm' => false, 'cond' => OriginCondition::Good, 'notes' => null],
                ['room' => $aptLiving, 'name' => '75-inch Flat Screen TV (Crated)', 'category' => ItemCategory::Electronics, 'qty' => 1, 'vol' => 20.00, 'wt' => 75.00, 'pack' => PackingType::CompanyPacked, 'fragile' => true, 'disasm' => false, 'cond' => OriginCondition::Good, 'notes' => null],
                ['room' => $aptLiving, 'name' => 'Large Box (4.5 cu ft)', 'category' => ItemCategory::BoxCarton, 'qty' => 6, 'vol' => 4.50, 'wt' => 50.00, 'pack' => PackingType::Pbo, 'fragile' => false, 'disasm' => false, 'cond' => OriginCondition::Good, 'notes' => null],
                ['room' => $aptBed, 'name' => 'Queen Bed & Mattress', 'category' => ItemCategory::Furniture, 'qty' => 1, 'vol' => 55.00, 'wt' => 140.00, 'pack' => PackingType::CompanyPacked, 'fragile' => false, 'disasm' => true, 'cond' => OriginCondition::Good, 'notes' => null],
                ['room' => $aptBed, 'name' => '6-Drawer Dresser', 'category' => ItemCategory::Furniture, 'qty' => 1, 'vol' => 35.00, 'wt' => 130.00, 'pack' => PackingType::UnpackedFurniture, 'fragile' => false, 'disasm' => false, 'cond' => OriginCondition::FairWorn, 'notes' => null],
            ];

            foreach ($itemsData2 as $d) {
                if (! $d['room']) {
                    continue;
                }
                $catItem = CatalogItem::where('name', $d['name'])->first();

                InventoryItem::create([
                    'move_id' => $move2->id,
                    'move_room_id' => $d['room']->id,
                    'catalog_item_id' => $catItem?->id,
                    'tag_number' => sprintf('#%03d', $tagNum++),
                    'name' => $d['name'],
                    'item_category' => $d['category'],
                    'quantity' => $d['qty'],
                    'unit_volume_cuft' => $d['vol'],
                    'unit_weight_lbs' => $d['wt'],
                    'packing_type' => $d['pack'],
                    'is_fragile' => $d['fragile'],
                    'is_high_value' => false,
                    'requires_disassembly' => $d['disasm'],
                    'origin_condition' => $d['cond'],
                    'condition_notes' => $d['notes'],
                    'delivery_status' => DeliveryStatus::Pending,
                ]);
            }

            $calcService->recalculateMoveTotals($move2);
        }

        // Active Share Link for Move 2 (Token: 64 chars, PIN: 4829)
        $shareToken = 'demo-customer-share-token-2026-moveledger-active-access-link-99'; // 63 chars
        MoveShare::firstOrCreate(
            ['token' => $shareToken],
            [
                'move_id' => $move2->id,
                'created_by_id' => $surveyor->id,
                'access_pin_hash' => Hash::make('4829'),
                'has_pin' => true,
                'allow_customer_signoff' => true,
                'expires_at' => now()->addDays(14),
            ]
        );

        // -------------------------------------------------------------
        // MOVE 3: In Transit (Office Move) with Signed Origin Manifest
        // -------------------------------------------------------------
        $move3 = Move::firstOrCreate(
            ['reference_code' => 'MV-2026-00003'],
            [
                'customer_id' => $customer3->id,
                'assigned_surveyor_id' => $surveyor->id,
                'created_by_id' => $admin->id,
                'status' => MoveStatus::InTransit,
                'property_type' => PropertyType::Office,
                'origin_address' => '100 Financial Plaza, Suite 800, New York, NY 10005',
                'origin_floor_access' => '8th floor commercial loading dock with designated service bay',
                'destination_address' => '300 Tech Boulevard, 2nd Floor, Austin, TX 78701',
                'destination_floor_access' => 'Ground floor roll-up bay with direct ramp access',
                'scheduled_move_date' => now()->toDateString(),
                'survey_started_at' => now()->subDays(2),
                'origin_signed_at' => now()->subHours(6),
                'notes' => 'Loaded and in transit. Ready for destination unloading verification.',
            ]
        );

        if ($move3->rooms()->count() === 0) {
            $lifecycleService->seedDefaultRooms($move3);
        }

        $reception = $move3->rooms()->where('name', 'Reception & Lobby')->first();
        $execOffice = $move3->rooms()->where('name', 'Executive Office')->first();
        $confRoom = $move3->rooms()->where('name', 'Conference Room')->first();

        if ($move3->items()->count() === 0) {
            $tagNum = 1;
            $itemsData3 = [
                ['room' => $reception, 'name' => 'Armchair / Recliner', 'category' => ItemCategory::Furniture, 'qty' => 2, 'vol' => 20.00, 'wt' => 65.00, 'pack' => PackingType::UnpackedFurniture, 'fragile' => false, 'disasm' => false, 'cond' => OriginCondition::Good, 'notes' => null],
                ['room' => $execOffice, 'name' => 'Executive Desk', 'category' => ItemCategory::Furniture, 'qty' => 1, 'vol' => 35.00, 'wt' => 120.00, 'pack' => PackingType::CompanyPacked, 'fragile' => false, 'disasm' => true, 'cond' => OriginCondition::Good, 'notes' => null],
                ['room' => $execOffice, 'name' => 'Ergonomic Office Chair', 'category' => ItemCategory::Furniture, 'qty' => 1, 'vol' => 12.00, 'wt' => 40.00, 'pack' => PackingType::UnpackedFurniture, 'fragile' => false, 'disasm' => false, 'cond' => OriginCondition::Good, 'notes' => null],
                ['room' => $execOffice, 'name' => 'Desktop PC & Dual Monitors', 'category' => ItemCategory::Electronics, 'qty' => 1, 'vol' => 10.00, 'wt' => 35.00, 'pack' => PackingType::CompanyPacked, 'fragile' => true, 'disasm' => false, 'cond' => OriginCondition::Good, 'notes' => null],
                ['room' => $confRoom, 'name' => 'Dining Table (6-Person)', 'category' => ItemCategory::Furniture, 'qty' => 1, 'vol' => 35.00, 'wt' => 110.00, 'pack' => PackingType::CompanyPacked, 'fragile' => false, 'disasm' => true, 'cond' => OriginCondition::Good, 'notes' => null],
                ['room' => $confRoom, 'name' => 'Dining Chair', 'category' => ItemCategory::Furniture, 'qty' => 8, 'vol' => 6.00, 'wt' => 18.00, 'pack' => PackingType::UnpackedFurniture, 'fragile' => false, 'disasm' => false, 'cond' => OriginCondition::Good, 'notes' => null],
                ['room' => $confRoom, 'name' => 'Large Box (4.5 cu ft)', 'category' => ItemCategory::BoxCarton, 'qty' => 10, 'vol' => 4.50, 'wt' => 50.00, 'pack' => PackingType::CompanyPacked, 'fragile' => false, 'disasm' => false, 'cond' => OriginCondition::Good, 'notes' => null],
            ];

            foreach ($itemsData3 as $d) {
                if (! $d['room']) {
                    continue;
                }
                $catItem = CatalogItem::where('name', $d['name'])->first();

                InventoryItem::create([
                    'move_id' => $move3->id,
                    'move_room_id' => $d['room']->id,
                    'catalog_item_id' => $catItem?->id,
                    'tag_number' => sprintf('#%03d', $tagNum++),
                    'name' => $d['name'],
                    'item_category' => $d['category'],
                    'quantity' => $d['qty'],
                    'unit_volume_cuft' => $d['vol'],
                    'unit_weight_lbs' => $d['wt'],
                    'packing_type' => $d['pack'],
                    'is_fragile' => $d['fragile'],
                    'is_high_value' => false,
                    'requires_disassembly' => $d['disasm'],
                    'origin_condition' => $d['cond'],
                    'condition_notes' => $d['notes'],
                    'delivery_status' => DeliveryStatus::Pending,
                ]);
            }

            $calcService->recalculateMoveTotals($move3);

            // Create sample PNG signature for origin
            $sampleSigPng = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAMgAAAAyCAYAAAAh+F7mAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAADwSURBVHhe7doxAYAwEMSwcxP4d2qC1mAC00D85959h33v/W3X67y/27m7O5y7u8O5uzucu7vDubvDubs7nLu7w7m7O5y7u8O5uzucu7vDubvDubs7nLu7w7m7O5y7u8O5uzucu7vDubvDubs7nLu7w7m7O5y7u8O5uzucu7vDubvDubs7nLu7w7m7O5y7u8O5uzucu7vDubvDubs7nLu7w7m7O5y7u8O5uzucu7vDubvDubs7nLu7w7m7O5y7u8O5uzucu7vDubvDubs7nLu7w7m7O5y7u8O5uzucu7vDubvDubs7nLu7w7m7OwD4wB5qBwH+kOq7cQAAAABJRU5ErkJggg==');
            $sigPath = 'moves/' . $move3->id . '/signatures/sig_origin_sample.png';
            Storage::disk('public')->put($sigPath, $sampleSigPng);

            $snapshot = $sigService->generateManifestSnapshot($move3);

            MoveSignature::create([
                'move_id' => $move3->id,
                'signature_stage' => 'origin',
                'signer_name' => 'Marcus Vance',
                'signer_role' => 'authorized_agent',
                'signature_image_path' => $sigPath,
                'manifest_snapshot_hash' => $snapshot['hash'],
                'snapshot_items_count' => $snapshot['items_count'],
                'snapshot_volume_cuft' => $snapshot['volume_cuft'],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X)',
                'is_valid' => true,
                'signed_at' => now()->subHours(6),
            ]);
        }
    }
}
