<?php

declare(strict_types=1);

namespace App\Http\Requests\Move;

use App\Enums\PropertyType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreMoveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'required_without:customer_id|nullable|string|max:150',
            'customer_email' => 'required_without:customer_id|nullable|email|max:190',
            'customer_phone' => 'required_without:customer_id|nullable|string|max:30',
            'property_type' => ['required', new Enum(PropertyType::class)],
            'origin_address' => 'required|string|max:500',
            'origin_floor_access' => 'nullable|string|max:150',
            'destination_address' => 'required|string|max:500',
            'destination_floor_access' => 'nullable|string|max:150',
            'scheduled_move_date' => 'required|date|after_or_equal:today',
            'assigned_surveyor_id' => 'required|exists:users,id',
            'notes' => 'nullable|string|max:2000',
        ];
    }
}
