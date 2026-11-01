<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\InventoryItemPhoto;
use App\Models\Move;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InventoryItemPhotoController extends Controller
{
    public function store(Request $request, Move $move, InventoryItem $item): JsonResponse|RedirectResponse
    {
        $this->authorize('manageInventory', $move);

        $currentCount = $item->photos()->count();
        if ($currentCount >= 5) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Maximum 5 photos allowed per inventory item.'], 422);
            }
            return redirect()->back()->withErrors(['photos' => 'Maximum 5 photos allowed per item.']);
        }

        $request->validate([
            'photos' => 'required|array|min:1|max:' . (5 - $currentCount),
            'photos.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'caption' => 'nullable|string|max:190',
        ]);

        $createdPhotos = [];
        foreach ($request->file('photos') as $photoFile) {
            $filename = Str::uuid() . '.' . $photoFile->getClientOriginalExtension();
            $path = $photoFile->storeAs("moves/{$move->id}/items/{$item->id}", $filename, 'public');

            $photo = InventoryItemPhoto::create([
                'inventory_item_id' => $item->id,
                'uploaded_by_id' => $request->user()->id,
                'photo_stage' => 'origin',
                'file_path' => $path,
                'caption' => $request->input('caption'),
                'file_size_bytes' => $photoFile->getSize(),
            ]);

            $createdPhotos[] = $photo;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'photos' => $createdPhotos,
            ]);
        }

        return redirect()->route('moves.survey', ['move' => $move, 'room_id' => $item->move_room_id])
            ->with('success', count($createdPhotos) . ' photo(s) uploaded.');
    }

    public function destroy(Move $move, InventoryItem $item, InventoryItemPhoto $photo): JsonResponse|RedirectResponse
    {
        $this->authorize('manageInventory', $move);

        Storage::disk('public')->delete($photo->file_path);
        $photo->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('moves.survey', ['move' => $move, 'room_id' => $item->move_room_id])
            ->with('success', 'Photo removed.');
    }
}
