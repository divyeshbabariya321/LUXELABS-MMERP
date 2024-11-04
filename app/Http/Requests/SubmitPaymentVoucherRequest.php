<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitPaymentVoucherRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'date'              => [
                'required',
            ],
'amount'            => [
                'required',
            ],
'currency'          => [
                'required',
            ],
'payment_method_id' => [
                'required',
            ],
];
    }
}
