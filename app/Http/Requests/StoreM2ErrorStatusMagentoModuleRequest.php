<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreM2ErrorStatusMagentoModuleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return ['m2_error_status_name' => [
                'required',
                'max:150',
                'unique:magento_module_m2_error_statuses',
            ],];
    }
}
