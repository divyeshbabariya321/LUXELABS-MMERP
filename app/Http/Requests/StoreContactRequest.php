<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'name'     => [
                'required',
                'min:1',
                'string',
            ],
'phone'    => [
                'required',
                'numeric',
            ],
'category' => [
                'sometimes',
                'nullable',
                'string',
            ],
];
    }
}
