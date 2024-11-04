<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttributeReplacementRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'field_identifier' => [
                'required',
            ],
'first_term'       => [
                'required',
            ],
];
    }
}
