<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCaseReceivableRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'currency'          => [
                'required',
                'numeric',
            ],
'receivable_date'   => [
                'required',
                'date',
            ],
'receivable_amount' => [
                'required',
                'numeric',
            ],
'received_date'     => [
                'sometimes',
                'nullable',
                'date',
            ],
'received_amount'   => [
                'sometimes',
                'nullable',
                'numeric',
            ],
];
    }
}
