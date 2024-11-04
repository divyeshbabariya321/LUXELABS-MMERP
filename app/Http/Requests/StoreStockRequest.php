<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'courier'     => [
                'required',
                'string',
                'min:3',
                'max:255',
            ],
'from'        => [
                'sometimes',
                'nullable',
                'string',
                'min:3',
                'max:255',
            ],
'date'        => [
                'sometimes',
                'nullable',
            ],
'awb'         => [
                'required',
                'min:3',
                'max:255',
            ],
'l_dimension' => [
                'sometimes',
                'nullable',
                'numeric',
            ],
'w_dimension' => [
                'sometimes',
                'nullable',
                'numeric',
            ],
'h_dimension' => [
                'sometimes',
                'nullable',
                'numeric',
            ],
'weight'      => [
                'sometimes',
                'nullable',
                'numeric',
            ],
'pcs'         => [
                'sometimes',
                'nullable',
                'numeric',
            ],
];
    }
}
