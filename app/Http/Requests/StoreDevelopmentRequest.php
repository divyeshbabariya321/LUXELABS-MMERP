<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDevelopmentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'subject'       => [
                'sometimes',
                'nullable',
                'string',
            ],
'task'          => [
                'required',
                'string',
                'min:3',
            ],
'status'        => [
                'required',
            ],
'repository_id' => [
                'required',
            ],
'module_id'     => [
                'required',
            ],
];
    }
}
