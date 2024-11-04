<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAccountAccountRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'username' => [
                'required',
            ],
'name'     => [
                'required',
            ],
'password' => [
                'required',
            ],
];
    }
}
