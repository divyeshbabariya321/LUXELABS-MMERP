<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDailyCashFlowRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'received_from' => [
                'sometimes',
                'nullable',
                'string',
            ],
'paid_to'       => [
                'sometimes',
                'nullable',
                'string',
            ],
'date'          => [
                'required',
            ],
'expected'      => [
                'required_without:received',
                'nullable',
                'numeric',
            ],
'received'      => [
                'required_without:expected',
                'nullable',
                'numeric',
            ],
];
    }
}
