<?php

namespace App\Http\Requests\MagentoModule;

use Illuminate\Foundation\Http\FormRequest;

class MagentoModuleLocationRequest extends FormRequest
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
            'magento_module_locations' => [
                'required',
                'max:150',
                'unique:magento_module_locations',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'magento_module_locations.required' => __('validation.required'),
        ];
    }
}
