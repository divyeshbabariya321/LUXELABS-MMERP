<?php

namespace App\Http\Requests\Github;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrganizationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'name'     => [
                'required',
            ],
'username' => [
                'required',
            ],
'token'    => [
                'required',
            ],
];
    }
}
