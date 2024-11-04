<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLanguageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'locale' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],
'code'   => [
                'required',
            ],
];
    }
}
