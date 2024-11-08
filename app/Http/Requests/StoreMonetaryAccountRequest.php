<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMonetaryAccountRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'name'     => [
                'required',
            ],
'currency' => [
                'required',
            ],
'date'     => [
                'required',
                'date',
            ],
'amount'   => [
                'required',
                'numeric',
            ],
];
    }
}
