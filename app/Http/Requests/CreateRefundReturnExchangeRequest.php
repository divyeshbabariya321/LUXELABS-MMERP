<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateRefundReturnExchangeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'customer_id'        => [
                'required',
                'integer',
            ],
'refund_amount'      => [
                'required',
            ],
'refund_amount_mode' => [
                'required',
                'string',
            ],
];
    }
}
