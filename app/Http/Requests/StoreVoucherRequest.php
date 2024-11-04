<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVoucherRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'description' => [
                'required',
                'min:3',
            ],
'travel_type' => [
                'sometimes',
                'nullable',
                'string',
            ],
'amount'      => [
                'sometimes',
                'nullable',
                'numeric',
            ],
'paid'        => [
                'sometimes',
                'nullable',
                'numeric',
            ],
'date'        => [
                'required',
                'date',
            ],
];
    }
}
