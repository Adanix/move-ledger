<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MoveRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'move_id',
        'name',
        'floor_level',
        'sort_order',
        'room_items_count',
        'room_volume_cuft',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'room_items_count' => 'integer',
            'room_volume_cuft' => 'decimal:2',
        ];
    }

    public function move(): BelongsTo
    {
        return $this->belongsTo(Move::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InventoryItem::class)->orderBy('id');
    }
}
