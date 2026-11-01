<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\CatalogCategory;
use App\Enums\ItemCategory;
use App\Enums\UserRole;
use App\Models\CatalogItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $coordinator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->coordinator = User::create([
            'name' => 'Catalog Coordinator',
            'email' => 'catalog_coord@test.com',
            'role' => UserRole::Coordinator,
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
    }

    public function test_user_can_browse_and_filter_catalog_items(): void
    {
        CatalogItem::create([
            'name' => 'Leather Recliner',
            'category' => CatalogCategory::LivingRoom,
            'item_type' => ItemCategory::Furniture,
            'default_volume_cuft' => '25.00',
            'default_weight_lbs' => '75.00',
            'is_active' => true,
        ]);

        CatalogItem::create([
            'name' => 'Office Desk',
            'category' => CatalogCategory::Office,
            'item_type' => ItemCategory::Furniture,
            'default_volume_cuft' => '30.00',
            'default_weight_lbs' => '90.00',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->coordinator)->get('/catalog?category=living_room');

        $response->assertStatus(200);
        $response->assertSee('Leather Recliner');
        $response->assertDontSee('Office Desk');
    }

    public function test_user_can_create_new_catalog_item(): void
    {
        $response = $this->actingAs($this->coordinator)->post('/catalog', [
            'name' => 'Standing Mirror Heavy',
            'category' => CatalogCategory::Bedroom->value,
            'item_type' => ItemCategory::FragileArt->value,
            'default_volume_cuft' => '12.00',
            'default_weight_lbs' => '35.00',
            'is_fragile_default' => '1',
            'requires_disassembly_default' => '0',
            'is_active' => '1',
        ]);

        $response->assertRedirect('/catalog?category=bedroom');
        $this->assertDatabaseHas('catalog_items', [
            'name' => 'Standing Mirror Heavy',
            'category' => CatalogCategory::Bedroom->value,
        ]);
    }

    public function test_user_can_update_and_delete_catalog_item(): void
    {
        $item = CatalogItem::create([
            'name' => 'Old Bookcase',
            'category' => CatalogCategory::Office,
            'item_type' => ItemCategory::Furniture,
            'default_volume_cuft' => '20.00',
            'default_weight_lbs' => '60.00',
            'is_active' => true,
        ]);

        $updateResponse = $this->actingAs($this->coordinator)->put("/catalog/{$item->id}", [
            'name' => 'Updated Solid Oak Bookcase',
            'category' => CatalogCategory::Office->value,
            'item_type' => ItemCategory::Furniture->value,
            'default_volume_cuft' => '22.00',
            'default_weight_lbs' => '80.00',
            'is_fragile_default' => '0',
            'requires_disassembly_default' => '0',
            'is_active' => '1',
        ]);

        $updateResponse->assertRedirect('/catalog?category=office');
        $item->refresh();
        $this->assertSame('Updated Solid Oak Bookcase', $item->name);

        $deleteResponse = $this->actingAs($this->coordinator)->delete("/catalog/{$item->id}");
        $deleteResponse->assertRedirect('/catalog?category=office');
        $this->assertDatabaseMissing('catalog_items', ['id' => $item->id]);
    }
}
