<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendToAllWhatsAppRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'sending_time' => [
                'required',
                'date',
            ],
'frequency'    => [
                'required',
                'numeric',
            ],
'rating'       => [
                'sometimes',
                'nullable',
                'numeric',
            ],
'gender'       => [
                'sometimes',
                'nullable',
                'string',
            ],
];
    }
}
