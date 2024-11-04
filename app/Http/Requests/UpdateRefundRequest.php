<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRefundRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'customer_id' => [
                'required',
                'integer',
            ],
'order_id'    => [
                'required',
                'integer',
            ],
'type'        => [
                'required',
                'string',
            ],
];
    }
}
