<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\DeliveryStatus;
use App\Enums\OriginCondition;
use App\Enums\PackingType;
use App\Http\Requests\Inventory\StoreInventoryItemRequest;
use App\Http\Requests\Inventory\UpdateInventoryItemRequest;
use App\Models\CatalogItem;
use App\Models\InventoryItem;
use App\Models\InventoryItemPhoto;
use App\Models\Move;
use App\Models\MoveRoom;
use App\Services\InventoryCalculationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InventoryItemController extends Controller
{
    public function __construct(
        protected InventoryCalculationService $calcService
    ) {}

    public function store(StoreInventoryItemRequest $request, Move $move): JsonResponse|RedirectResponse
    {
        $this->authorize('manageInventory', $move);

        $data = $request->validated();
        $roomId = (int) $data['move_room_id'];

        // Verify room belongs to this move
        $room = MoveRoom::where('move_id', $move->id)->findOrFail($roomId);

        $item = DB::transaction(function () use ($move, $room, $data, $request): InventoryItem {
            // Check for existing identical catalog item in this room
            if (! empty($data['catalog_item_id'])) {
                $existingItem = InventoryItem::where('move_room_id', $room->id)
                    ->where('catalog_item_id', $data['catalog_item_id'])
                    ->where('origin_condition', $data['origin_condition'])
                    ->where('packing_type', $data['packing_type'])
                    ->whereNull('condition_notes')
                    ->first();

                if ($existingItem) {
                    $existingItem->increment('quantity', (int) $data['quantity']);
                    $this->calcService->recalculateMoveTotals($move);
                    return $existingItem;
                }
            }

            $tagNumber = $this->calcService->generateNextTagNumber($move);

            $item = InventoryItem::create([
                'move_id' => $move->id,
                'move_room_id' => $room->id,
                'catalog_item_id' => $data['catalog_item_id'] ?? null,
                'tag_number' => $tagNumber,
                'name' => $data['name'],
                'item_category' => $data['item_category'],
                'quantity' => $data['quantity'],
                'unit_volume_cuft' => $data['unit_volume_cuft'],
                'unit_weight_lbs' => $data['unit_weight_lbs'],
                'packing_type' => $data['packing_type'],
                'is_fragile' => $data['is_fragile'],
                'is_high_value' => $data['is_high_value'],
                'requires_disassembly' => $data['requires_disassembly'],
                'declared_value' => $data['declared_value'] ?? null,
                'origin_condition' => $data['origin_condition'],
                'condition_notes' => $data['condition_notes'] ?? null,
                'delivery_status' => DeliveryStatus::Pending,
            ]);

            // Handle optional photo attachments
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photoFile) {
                    $filename = Str::uuid() . '.' . $photoFile->getClientOriginalExtension();
                    $path = $photoFile->storeAs("moves/{$move->id}/items/{$item->id}", $filename, 'public');

                    InventoryItemPhoto::create([
                        'inventory_item_id' => $item->id,
                        'uploaded_by_id' => $request->user()->id,
                        'photo_stage' => 'origin',
                        'file_path' => $path,
                        'file_size_bytes' => $photoFile->getSize(),
                    ]);
                }
            }

            $this->calcService->recalculateMoveTotals($move);

            return $item;
        });

        if ($request->wantsJson()) {
            $move->refresh();
            $room->refresh();

            return response()->json([
                'success' => true,
                'message' => "Item '{$item->name}' added successfully.",
                'item' => $item->load('photos'),
                'room' => $room,
                'totals' => [
                    'total_items_count' => $move->total_items_count,
                    'total_volume_cuft' => (string) $move->total_volume_cuft,
                    'total_weight_lbs' => (string) $move->total_weight_lbs,
                    'total_boxes_count' => $move->total_boxes_count,
                ],
            ]);
        }

        return redirect()->route('moves.survey', ['move' => $move, 'room_id' => $room->id])
            ->with('success', "Item '{$item->name}' added.");
    }

    public function update(UpdateInventoryItemRequest $request, Move $move, InventoryItem $item): RedirectResponse
    {
        $this->authorize('manageInventory', $move);

        $data = $request->validated();
        $roomId = (int) $data['move_room_id'];

        $room = MoveRoom::where('move_id', $move->id)->findOrFail($roomId);

        DB::transaction(function () use ($move, $item, $room, $data): void {
            $item->update([
                'move_room_id' => $room->id,
                'name' => $data['name'],
                'item_category' => $data['item_category'],
                'quantity' => $data['quantity'],
                'unit_volume_cuft' => $data['unit_volume_cuft'],
                'unit_weight_lbs' => $data['unit_weight_lbs'],
                'packing_type' => $data['packing_type'],
                'is_fragile' => $data['is_fragile'],
                'is_high_value' => $data['is_high_value'],
                'requires_disassembly' => $data['requires_disassembly'],
                'declared_value' => $data['declared_value'] ?? null,
                'origin_condition' => $data['origin_condition'],
                'condition_notes' => $data['condition_notes'] ?? null,
            ]);

            $this->calcService->recalculateMoveTotals($move);
        });

        return redirect()->route('moves.survey', ['move' => $move, 'room_id' => $room->id])
            ->with('success', "Item '{$item->name}' updated.");
    }

    public function updateQuantity(Request $request, Move $move, InventoryItem $item): JsonResponse|RedirectResponse
    {
        $this->authorize('manageInventory', $move);

        $validated = $request->validate([
            'action' => 'required|in:increment,decrement',
            'confirm_delete' => 'nullable|boolean',
        ]);

        $action = $validated['action'];
        $confirmDelete = $request->boolean('confirm_delete');
        $deleted = false;

        $response = DB::transaction(function () use ($move, $item, $action, $confirmDelete, &$deleted) {
            if ($action === 'increment') {
                $item->increment('quantity');
            } elseif ($action === 'decrement') {
                if ($item->quantity > 1) {
                    $item->decrement('quantity');
                } else {
                    // Item quantity is 1
                    if ($confirmDelete) {
                        $item->delete();
                        $deleted = true;
                    } else {
                        return [
                            'success' => false,
                            'confirm_delete_required' => true,
                            'item_name' => $item->name,
                            'message' => "Remove {$item->name} from this room?",
                        ];
                    }
                }
            }

            $this->calcService->recalculateMoveTotals($move);
            $move->refresh();
            $room = MoveRoom::find($item->move_room_id);

            return [
                'success' => true,
                'deleted' => $deleted,
                'quantity' => $deleted ? 0 : $item->quantity,
                'room_id' => $item->move_room_id,
                'room_items_count' => $room?->room_items_count ?? 0,
                'room_volume_cuft' => (string) ($room?->room_volume_cuft ?? '0.00'),
                'total_items_count' => $move->total_items_count,
                'total_boxes_count' => $move->total_boxes_count,
                'total_volume_cuft' => (string) $move->total_volume_cuft,
                'total_weight_lbs' => (string) $move->total_weight_lbs,
            ];
        });

        if ($request->wantsJson()) {
            return response()->json($response);
        }

        return redirect()->route('moves.survey', ['move' => $move, 'room_id' => $item->move_room_id]);
    }

    public function destroy(Move $move, InventoryItem $item): RedirectResponse
    {
        $this->authorize('manageInventory', $move);

        $roomId = $item->move_room_id;
        $name = $item->name;

        DB::transaction(function () use ($move, $item): void {
            $item->delete();
            $this->calcService->recalculateMoveTotals($move);
        });

        return redirect()->route('moves.survey', ['move' => $move, 'room_id' => $roomId])
            ->with('success', "Item '{$name}' removed.");
    }
}
