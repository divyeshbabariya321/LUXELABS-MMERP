<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'purchase_handler' => [
                'required',
            ],
'order_products'   => [
                'required',
            ],
];
    }
}
