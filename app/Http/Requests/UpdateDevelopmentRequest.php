<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDevelopmentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'priority' => [
                'required',
                'integer',
            ],
'task'     => [
                'required',
                'string',
                'min:3',
            ],
'cost'     => [
                'sometimes',
                '',
                'nullable',
                'integer',
            ],
'status'   => [
                'required',
            ],
];
    }
}
