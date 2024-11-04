<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTaskFromSortcutTaskModuleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'task_subject'      => [
                'required',
            ],
'task_detail'       => [
                'required',
            ],
'task_asssigned_to' => [
                'required_without:assign_to_contacts',
            ],
];
    }
}
