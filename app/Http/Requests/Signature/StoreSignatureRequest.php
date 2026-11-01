<?php

declare(strict_types=1);

namespace App\Http\Requests\Signature;

use Illuminate\Foundation\Http\FormRequest;

class StoreSignatureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'signer_name' => 'required|string|min:2|max:150',
            'signer_role' => 'required|in:customer,authorized_agent',
            'signature_data_uri' => 'required|string',
            'legal_acknowledgment' => 'accepted',
        ];
    }

    public function messages(): array
    {
        return [
            'signature_data_uri.required' => 'Please draw your signature in the box before submitting.',
            'legal_acknowledgment.accepted' => 'You must check the legal acknowledgment box to proceed.',
        ];
    }
}
