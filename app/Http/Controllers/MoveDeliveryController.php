<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\DeliveryStatus;
use App\Models\InventoryItem;
use App\Models\Move;
use App\Models\MoveRoom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MoveDeliveryController extends Controller
{
    public function show(Request $request, Move $move): View
    {
        $this->authorize('manageDelivery', $move);

        $move->load(['customer', 'rooms.items.photos', 'validDestinationSignature']);

        $totalItems = $move->items->sum('quantity');
        $deliveredItems = $move->items->where('delivery_status', DeliveryStatus::Delivered)->sum('quantity');
        $exceptionItems = $move->items->whereIn('delivery_status', [DeliveryStatus::DamagedInTransit, DeliveryStatus::Missing])->sum('quantity');
        $pendingItems = $move->items->where('delivery_status', DeliveryStatus::Pending)->sum('quantity');

        $isAllAccountedFor = $pendingItems === 0 && $totalItems > 0;

        return view('moves.delivery', [
            'move' => $move,
            'stats' => [
                'total' => $totalItems,
                'delivered' => $deliveredItems,
                'exceptions' => $exceptionItems,
                'pending' => $pendingItems,
                'is_all_accounted_for' => $isAllAccountedFor,
            ],
        ]);
    }

    public function updateItem(Request $request, Move $move, InventoryItem $item): JsonResponse|RedirectResponse
    {
        $this->authorize('manageDelivery', $move);

        $validated = $request->validate([
            'delivery_status' => 'required|in:delivered,damaged_in_transit,missing,pending',
            'delivery_exception_notes' => 'required_if:delivery_status,damaged_in_transit,missing|nullable|string|max:1000',
        ]);

        $status = DeliveryStatus::from($validated['delivery_status']);

        $item->update([
            'delivery_status' => $status,
            'delivery_exception_notes' => $validated['delivery_exception_notes'] ?? null,
            'verified_by_id' => $request->user()->id,
            'verified_at' => now(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'item' => $item,
            ]);
        }

        return redirect()->route('moves.delivery', $move)
            ->with('success', "Item '{$item->name}' marked as {$status->label()}.");
    }

    public function markRoomDelivered(Request $request, Move $move): RedirectResponse
    {
        $this->authorize('manageDelivery', $move);

        $validated = $request->validate([
            'room_id' => 'required|exists:move_rooms,id',
        ]);

        $room = MoveRoom::where('move_id', $move->id)->findOrFail($validated['room_id']);

        $count = InventoryItem::where('move_room_id', $room->id)
            ->where('delivery_status', DeliveryStatus::Pending)
            ->update([
                'delivery_status' => DeliveryStatus::Delivered,
                'verified_by_id' => $request->user()->id,
                'verified_at' => now(),
            ]);

        return redirect()->route('moves.delivery', $move)
            ->with('success', "{$count} pending item(s) in '{$room->name}' marked as Delivered.");
    }
}
