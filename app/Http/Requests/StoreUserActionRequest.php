<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserActionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'url'  => [
                'required',
            ],
'type' => [
                'required',
            ],
'data' => [
                'required',
            ],
];
    }
}
