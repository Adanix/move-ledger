<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DeliveryStatus;
use App\Enums\ItemCategory;
use App\Enums\OriginCondition;
use App\Enums\PackingType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'move_id',
        'move_room_id',
        'catalog_item_id',
        'tag_number',
        'name',
        'item_category',
        'quantity',
        'unit_volume_cuft',
        'unit_weight_lbs',
        'packing_type',
        'is_fragile',
        'is_high_value',
        'requires_disassembly',
        'declared_value',
        'origin_condition',
        'condition_notes',
        'delivery_status',
        'delivery_exception_notes',
        'verified_by_id',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'item_category' => ItemCategory::class,
            'quantity' => 'integer',
            'unit_volume_cuft' => 'decimal:2',
            'unit_weight_lbs' => 'decimal:2',
            'packing_type' => PackingType::class,
            'is_fragile' => 'boolean',
            'is_high_value' => 'boolean',
            'requires_disassembly' => 'boolean',
            'declared_value' => 'decimal:2',
            'origin_condition' => OriginCondition::class,
            'delivery_status' => DeliveryStatus::class,
            'verified_at' => 'datetime',
        ];
    }

    public function move(): BelongsTo
    {
        return $this->belongsTo(Move::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(MoveRoom::class, 'move_room_id');
    }

    public function catalogItem(): BelongsTo
    {
        return $this->belongsTo(CatalogItem::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(InventoryItemPhoto::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_id');
    }

    public function totalVolumeCuft(): float
    {
        return (float) ($this->quantity * (float) $this->unit_volume_cuft);
    }

    public function totalWeightLbs(): float
    {
        return (float) ($this->quantity * (float) $this->unit_weight_lbs);
    }
}
