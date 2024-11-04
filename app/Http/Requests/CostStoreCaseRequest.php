<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CostStoreCaseRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'billed_date' => [
                'required',
            ],
'amount'      => [
                'required',
                'numeric',
            ],
];
    }
}
