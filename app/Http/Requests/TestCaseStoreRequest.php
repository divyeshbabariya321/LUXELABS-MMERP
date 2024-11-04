<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TestCaseStoreRequest extends FormRequest
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
            'suite' => 'required|string',
            'module_id' => 'required|string',
            'precondition' => 'required|string',
            'step_to_reproduce' => 'required|string',
            'expected_result' => 'required|string',
            'test_status_id' => 'required|string',
            'website' => 'required|string',
        ];
    }
}
