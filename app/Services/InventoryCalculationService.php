<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ItemCategory;
use App\Models\InventoryItem;
use App\Models\Move;
use App\Models\MoveRoom;
use Illuminate\Support\Facades\DB;

class InventoryCalculationService
{
    /**
     * Atomically recalculate all totals for each room and the entire move.
     */
    public function recalculateMoveTotals(Move $move): void
    {
        DB::transaction(function () use ($move): void {
            // Recalculate room-level totals
            $rooms = MoveRoom::where('move_id', $move->id)->get();
            foreach ($rooms as $room) {
                $roomItems = InventoryItem::where('move_room_id', $room->id)->get();

                $itemCount = 0;
                $roomVolume = 0.00;

                foreach ($roomItems as $item) {
                    $itemCount += $item->quantity;
                    $roomVolume += ($item->quantity * (float) $item->unit_volume_cuft);
                }

                $room->update([
                    'room_items_count' => $itemCount,
                    'room_volume_cuft' => round($roomVolume, 2),
                ]);
            }

            // Recalculate move-level totals
            $allItems = InventoryItem::where('move_id', $move->id)->get();

            $totalItems = 0;
            $totalBoxes = 0;
            $totalVolume = 0.00;
            $totalWeight = 0.00;
            $totalValue = 0.00;

            foreach ($allItems as $item) {
                $totalItems += $item->quantity;

                if ($item->item_category === ItemCategory::BoxCarton) {
                    $totalBoxes += $item->quantity;
                }

                $totalVolume += ($item->quantity * (float) $item->unit_volume_cuft);
                $totalWeight += ($item->quantity * (float) $item->unit_weight_lbs);

                if ($item->declared_value !== null) {
                    $totalValue += (float) $item->declared_value;
                }
            }

            $move->update([
                'total_items_count' => $totalItems,
                'total_boxes_count' => $totalBoxes,
                'total_volume_cuft' => round($totalVolume, 2),
                'total_weight_lbs' => round($totalWeight, 2),
                'total_declared_value' => round($totalValue, 2),
            ]);
        });
    }

    /**
     * Generate next sequential tag number for a move (e.g. #001, #002) atomically.
     */
    public function generateNextTagNumber(Move $move): string
    {
        return DB::transaction(function () use ($move): string {
            // Lock move row for update to prevent concurrent duplicate tag numbers
            Move::where('id', $move->id)->lockForUpdate()->first();

            $latestItem = InventoryItem::where('move_id', $move->id)
                ->orderByDesc('id')
                ->first();

            $nextNumber = 1;
            if ($latestItem && preg_match('/#?(\d+)/', $latestItem->tag_number, $matches)) {
                $count = InventoryItem::where('move_id', $move->id)->count();
                $nextNumber = max((int) $matches[1] + 1, $count + 1);
            } else {
                $nextNumber = InventoryItem::where('move_id', $move->id)->count() + 1;
            }

            return sprintf('#%03d', $nextNumber);
        });
    }
}
