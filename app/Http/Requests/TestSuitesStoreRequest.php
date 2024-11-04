<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TestSuitesStoreRequest extends FormRequest
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
            'name' => 'required|string',
            'step_to_reproduce' => 'required|string',
            'url' => 'required|string',
            'bug_environment_id' => 'required|string',
            'assign_to' => 'required|string',
            'bug_status_id' => 'required|string',
            'module_id' => 'required|string',
            'remark' => 'required|string',
            'website' => 'required|string',
        ];
    }
}
