<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHubstaffPaymentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'billing_start' => [
                'required',
            ],
'billing_end'   => [
                'required',
            ],
'hrs'           => [
                'required',
            ],
'rate'          => [
                'required',
            ],
];
    }
}
