<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCashFlowRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'cash_flow_category_id' => [
                'sometimes',
                'nullable',
                'integer',
            ],
'description'           => [
                'sometimes',
                'nullable',
                'string',
            ],
'date'                  => [
                'required',
            ],
'amount'                => [
                'required',
                'integer',
            ],
'type'                  => [
                'required',
                'string',
            ],
];
    }
}
