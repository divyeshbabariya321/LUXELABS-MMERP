<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAgentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'name'            => [
                'required',
                'string',
                '',
                'max:255',
            ],
'phone'           => [
                'sometimes',
                'nullable',
                'numeric',
            ],
'whatsapp_number' => [
                'sometimes',
                'nullable',
                'numeric',
            ],
'address'         => [
                'sometimes',
                'nullable',
                'string',
            ],
'email'           => [
                'sometimes',
                'nullable',
                'email',
            ],
];
    }
}
