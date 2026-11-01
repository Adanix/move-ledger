<?php

declare(strict_types=1);

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:190',
            'phone' => 'required|string|max:30',
            'secondary_phone' => 'nullable|string|max:30',
            'notes' => 'nullable|string|max:2000',
        ];
    }
}
