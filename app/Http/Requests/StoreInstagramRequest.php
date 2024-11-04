<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInstagramRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'first_name' => [
                'required',
            ],
'last_name'  => [
                'required',
            ],
'password'   => [
                'required',
            ],
];
    }
}
