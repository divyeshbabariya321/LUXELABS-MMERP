<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CostStoreDevelopmentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'amount'    => [
                'required',
                'numeric',
            ],
'paid_date' => [
                'required',
            ],
];
    }
}
