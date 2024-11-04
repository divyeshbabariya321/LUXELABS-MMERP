<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GoogleDialogFlowUpdateRequest extends FormRequest
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
            'account_id' => 'required|integer',
            'edit_site_id' => 'required|integer',
            'edit_project_id' => 'required|string',
            'edit_service_file' => 'sometimes|mimes:json',
            'edit_email' => 'required|email',
        ];
    }
}
