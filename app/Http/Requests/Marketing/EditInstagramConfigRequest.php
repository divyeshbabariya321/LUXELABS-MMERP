<?php

namespace App\Http\Requests\Marketing;

use Illuminate\Foundation\Http\FormRequest;

class EditInstagramConfigRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'number'           => [
                'required',
                'max:13',
            ],
'provider'         => [
                'required',
            ],
'customer_support' => [
                'required',
            ],
'username'         => [
                'required',
                'min:3',
                'max:255',
            ],
'password'         => [
                'required',
                'min:6',
                'max:255',
            ],
'frequency'        => [
                'required',
            ],
'send_start'       => [
                'required',
            ],
'send_end'         => [
                'required',
            ],
];
    }
}
