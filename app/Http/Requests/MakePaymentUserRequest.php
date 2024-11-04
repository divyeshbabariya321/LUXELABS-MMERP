<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MakePaymentUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'amount'         => [
                'required',
                'numeric',
                'min:1',
            ],
'payment_method' => [
                'required',
            ],
'currency'       => [
                'required',
            ],
];
    }
}
