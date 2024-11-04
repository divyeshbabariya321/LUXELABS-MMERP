<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskModuleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'assign_to.*'  => [
                'required_without:assign_to_contacts',
            ],
'sending_time' => [
                'sometimes',
                'nullable',
                'date',
            ],
];
    }
}
