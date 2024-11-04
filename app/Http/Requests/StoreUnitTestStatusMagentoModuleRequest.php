<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUnitTestStatusMagentoModuleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return ['unit_test_status_name' => [
                'required',
                'max:150',
                'unique:magento_modules_unit_test_statuses',
            ],];
    }
}
