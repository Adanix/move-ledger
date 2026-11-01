<?php

declare(strict_types=1);

namespace App\Http\Requests\Move;

use App\Enums\PropertyType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateMoveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'property_type' => ['required', new Enum(PropertyType::class)],
            'origin_address' => 'required|string|max:500',
            'origin_floor_access' => 'nullable|string|max:150',
            'destination_address' => 'required|string|max:500',
            'destination_floor_access' => 'nullable|string|max:150',
            'scheduled_move_date' => 'required|date',
            'assigned_surveyor_id' => 'required|exists:users,id',
            'notes' => 'nullable|string|max:2000',
        ];
    }
}
