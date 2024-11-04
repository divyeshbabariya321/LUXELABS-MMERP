<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmailSendCustomerRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'subject' => [
                'required',
                'min:3',
                'max:255',
            ],
'message' => [
                'required',
            ],
];
    }
}
