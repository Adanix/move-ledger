<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\Move;
use App\Models\MoveRoom;
use App\Services\InventoryCalculationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MoveRoomController extends Controller
{
    public function __construct(
        protected InventoryCalculationService $calcService
    ) {}

    public function store(Request $request, Move $move): RedirectResponse
    {
        $this->authorize('manageInventory', $move);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'floor_level' => 'nullable|string|max:50',
        ]);

        $maxOrder = MoveRoom::where('move_id', $move->id)->max('sort_order') ?? 0;

        $room = MoveRoom::create([
            'move_id' => $move->id,
            'name' => $validated['name'],
            'floor_level' => $validated['floor_level'] ?? 'Main Floor',
            'sort_order' => $maxOrder + 1,
        ]);

        return redirect()->route('moves.survey', ['move' => $move, 'room_id' => $room->id])
            ->with('success', "Room '{$room->name}' added.");
    }

    public function update(Request $request, Move $move, MoveRoom $room): RedirectResponse
    {
        $this->authorize('manageInventory', $move);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'floor_level' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:1',
        ]);

        $room->update($validated);

        return redirect()->route('moves.survey', ['move' => $move, 'room_id' => $room->id])
            ->with('success', "Room '{$room->name}' updated.");
    }

    public function destroy(Request $request, Move $move, MoveRoom $room): RedirectResponse
    {
        $this->authorize('manageInventory', $move);

        $itemCount = InventoryItem::where('move_room_id', $room->id)->count();

        if ($itemCount > 0) {
            $targetRoomId = $request->input('target_room_id');
            $confirmDelete = $request->boolean('confirm_delete_items');

            if ($targetRoomId) {
                $targetRoom = MoveRoom::where('move_id', $move->id)->findOrFail($targetRoomId);

                DB::transaction(function () use ($room, $targetRoom, $move): void {
                    InventoryItem::where('move_room_id', $room->id)->update([
                        'move_room_id' => $targetRoom->id,
                    ]);
                    $room->delete();
                    $this->calcService->recalculateMoveTotals($move);
                });

                return redirect()->route('moves.survey', ['move' => $move, 'room_id' => $targetRoom->id])
                    ->with('success', "Room deleted. {$itemCount} item(s) moved to '{$targetRoom->name}'.");
            }

            if (! $confirmDelete) {
                return redirect()->back()
                    ->withErrors(['room' => "Room contains {$itemCount} items. Select a target room to reassign them or confirm deletion."]);
            }
        }

        DB::transaction(function () use ($room, $move): void {
            $room->delete();
            $this->calcService->recalculateMoveTotals($move);
        });

        $remainingRoom = MoveRoom::where('move_id', $move->id)->orderBy('sort_order')->first();

        return redirect()->route('moves.survey', ['move' => $move, 'room_id' => $remainingRoom?->id])
            ->with('success', "Room '{$room->name}' deleted.");
    }
}
