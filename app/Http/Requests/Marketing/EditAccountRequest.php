<?php

namespace App\Http\Requests\Marketing;

use Illuminate\Foundation\Http\FormRequest;

class EditAccountRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'username'    => [
                'required',
            ],
'password'    => [
                'required',
            ],
'email'       => [
                'required:email',
            ],
'frequency'   => [
                'required',
            ],
'instance_id' => [
                'required',
            ],
'token'       => [
                'required',
            ],
'platform'    => [
                'required',
            ],
];
    }
}
