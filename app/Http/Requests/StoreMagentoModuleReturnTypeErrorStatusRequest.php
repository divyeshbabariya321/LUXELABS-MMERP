<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMagentoModuleReturnTypeErrorStatusRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return ['return_type_name' => [
                'required',
                'max:150',
                'unique:magento_module_return_type_error_status',
            ],];
    }
}
