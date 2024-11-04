<?php

namespace App\Http\Requests\MagentoModule;

use Illuminate\Foundation\Http\FormRequest;

class MagentoModuleApiHistoryRequest extends FormRequest
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
            'magento_module_id' => [
                'required',
            ],
            'resources'         => [
                'required',
            ],
            'frequency'         => [
                'required',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'remark.required'            => __('validation.required', ['attribute' => 'remark']),
            'magento_module_id.required' => __('validation.required', ['attribute' => 'module']),
        ];
    }
}
