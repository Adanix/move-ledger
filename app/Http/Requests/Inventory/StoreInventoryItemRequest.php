<?php

declare(strict_types=1);

namespace App\Http\Requests\Inventory;

use App\Enums\ItemCategory;
use App\Enums\OriginCondition;
use App\Enums\PackingType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreInventoryItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'move_room_id' => 'required|exists:move_rooms,id',
            'catalog_item_id' => 'nullable|exists:catalog_items,id',
            'name' => 'required|string|max:150',
            'item_category' => ['required', new Enum(ItemCategory::class)],
            'quantity' => 'required|integer|min:1|max:500',
            'unit_volume_cuft' => 'required|numeric|min:0.00|max:999.99',
            'unit_weight_lbs' => 'required|numeric|min:0.00|max:9999.99',
            'packing_type' => ['required', new Enum(PackingType::class)],
            'is_fragile' => 'required|boolean',
            'is_high_value' => 'required|boolean',
            'requires_disassembly' => 'required|boolean',
            'declared_value' => 'nullable|numeric|min:0.00|max:9999999.99',
            'origin_condition' => ['required', new Enum(OriginCondition::class)],
            'condition_notes' => 'required_if:origin_condition,pre_existing_damage|nullable|string|max:1000',
            'photos.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'condition_notes.required_if' => 'Condition notes are mandatory when pre-existing damage is flagged.',
        ];
    }
}
