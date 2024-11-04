<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ModuleStoreDevelopmentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return ['name' => [
                'required',
                'min:1',
                'unique:developer_modules,name,NULL,id,deleted_at,NULL',
            ],];
    }
}
