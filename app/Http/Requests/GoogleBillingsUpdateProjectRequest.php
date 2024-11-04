<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GoogleBillingsUpdateProjectRequest extends FormRequest
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
            'edit_google_billing_master_id' => 'required',
            'edit_service_type' => 'required',
            'edit_project_id' => 'required',
            'edit_dataset_id' => 'required',
            'edit_table_id' => 'required',
        ];
    }
}
