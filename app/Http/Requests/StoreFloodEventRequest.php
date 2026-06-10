<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFloodEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // You can build strict hardware token or API header authentication checks here later
    }

    public function rules(): array
    {
        return [
            'flood' => ['required', 'boolean'],
            'location' => ['required', 'string', 'exists:barangays,name'],
        ];
    }
    
    protected function prepareForValidation()
    {
        // Automatically sanitizes raw hardware payload formats (like matching the legacy "1" or "0" string variants)
        $this->merge([
            'flood' => filter_var($this->flood, FILTER_VALIDATE_BOOL),
        ]);
    }
}