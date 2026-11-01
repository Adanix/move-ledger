<?php

declare(strict_types=1);

namespace App\Http\Requests\Catalog;

use App\Enums\CatalogCategory;
use App\Enums\ItemCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreCatalogItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:150|unique:catalog_items,name',
            'category' => ['required', new Enum(CatalogCategory::class)],
            'item_type' => ['required', new Enum(ItemCategory::class)],
            'default_volume_cuft' => 'required|numeric|min:0.10|max:999.99',
            'default_weight_lbs' => 'required|numeric|min:0.50|max:9999.99',
            'is_fragile_default' => 'boolean',
            'requires_disassembly_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
