<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskModuleCreateMultipleTaskFromSortcutBugtrackRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'task_subject' => 'required',
            'task_detail' => 'required',
            'task_asssigned_to' => 'required_without:assign_to_contacts',
        ];
    }
}
