<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventUserEventRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'date'    => [
                'required',
            ],
'time'    => [
                'required',
            ],
'subject' => [
                'required',
            ],
];
    }
}
