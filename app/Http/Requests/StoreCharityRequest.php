<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCharityRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'email'           => [
                'required',
                'email',
            ],
'contact_no'      => [
                'required',
                'integer',
            ],
'name'            => [
                'required',
                'string',
            ],
'whatsapp_number' => [
                'required',
                'integer',
            ],
];
    }
}
