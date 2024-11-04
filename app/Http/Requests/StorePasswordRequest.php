<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePasswordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'website' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],
            'url' => [
                'required',
            ],
            'username' => [
                'required',
                'min:3',
                'max:255',
            ],
            'password' => [
                'required',
                'min:6',
                'max:255',
            ],
        ];
    }
}
