<?php

namespace App\Http\Requests\MagentoModule;

use Illuminate\Foundation\Http\FormRequest;

class MagentoModuleTypeRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'magento_module_type' => [
                'required',
                'max:150',
                'unique:magento_module_types',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'magento_module_type.required' => __('validation.required', ['attribute' => 'Module Type']),
        ];
    }
}
