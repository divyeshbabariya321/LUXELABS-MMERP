<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'customer_id'    => [
                'required',
            ],
'advance_detail' => [
                'numeric',
                'nullable',
            ],
'balance_amount' => [
                'numeric',
                'nullable',
            ],
];
    }
}
