<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CostUpdateCaseRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'paid_date'   => [
                'required',
            ],
'amount_paid' => [
                'required',
                'numeric',
            ],
];
    }
}
