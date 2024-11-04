<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLearningModuleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'task_subject' => [
                'required',
            ],
'task_details' => [
                'required',
            ],
'assign_to'    => [
                'required_without:assign_to_contacts',
            ],
];
    }
}
