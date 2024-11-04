<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBloggerPaymentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'currency'       => [
                'required',
                'numeric',
            ],
'payment_date'   => [
                'required',
                'date',
            ],
'payable_amount' => [
                'required',
                'numeric',
            ],
'paid_date'      => [
                'sometimes',
                'nullable',
                'date',
            ],
'paid_amount'    => [
                'sometimes',
                'nullable',
                'numeric',
            ],
];
    }
}
