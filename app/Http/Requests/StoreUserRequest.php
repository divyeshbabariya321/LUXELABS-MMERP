<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'name'        => [
                'required',
            ],
'email'       => [
                'required',
                'email',
                'unique:users,email',
            ],
'gmail'       => [
                'sometimes',
                'nullable',
                'email',
            ],
'phone'       => [
                'sometimes',
                'nullable',
                'integer',
                'unique:users,phone',
            ],
'password'    => [
                'required',
                'same:confirm-password',
            ],
'hourly_rate' => [
                'numeric',
            ],
'currency'    => [
                'string',
            ],
'timezone'    => [
                'required',
            ],
];
    }
}
