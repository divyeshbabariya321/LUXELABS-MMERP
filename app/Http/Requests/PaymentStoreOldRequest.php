<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentStoreOldRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'currency'     => [
                'required',
                'numeric',
            ],
'payment_date' => [
                'required',
                'date',
            ],
'paid_date'    => [
                'sometimes',
                'nullable',
                'date',
            ],
'paid_amount'  => [
                'sometimes',
                'nullable',
                'numeric',
            ],
];
    }
}
