<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVerifiedStatusMagentoModuleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return ['name' => [
                'required',
                'max:150',
                'unique:magento_module_verified_status',
            ],];
    }
}
