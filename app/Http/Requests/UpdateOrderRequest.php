<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'advance_detail' => [
                'numeric',
                'nullable',
            ],
'balance_amount' => [
                'numeric',
                'nullable',
            ],
'contact_detail' => [
                'sometimes',
                'nullable',
                'numeric',
            ],
];
    }
}
