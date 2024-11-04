<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGoogleServerRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'name' => [
                'required',
                'string',
                'max:255',
            ],
'key'  => [
                'required',
                'string',
                'max:255',
            ],
];
    }
}
