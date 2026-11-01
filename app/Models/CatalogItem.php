<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CatalogCategory;
use App\Enums\ItemCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CatalogItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'item_type',
        'default_volume_cuft',
        'default_weight_lbs',
        'is_fragile_default',
        'requires_disassembly_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'category' => CatalogCategory::class,
            'item_type' => ItemCategory::class,
            'default_volume_cuft' => 'decimal:2',
            'default_weight_lbs' => 'decimal:2',
            'is_fragile_default' => 'boolean',
            'requires_disassembly_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function inventoryItems(): HasMany
    {
        return $this->hasMany(InventoryItem::class);
    }
}
