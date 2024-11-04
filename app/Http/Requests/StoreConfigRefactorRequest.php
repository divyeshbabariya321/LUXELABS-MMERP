<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConfigRefactorRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'store_website_id' => [
                'required',
            ],
'name'             => [
                'required',
                'unique:config_refactor_sections,name',
            ],
'type'             => [
                'required',
            ],
];
    }
}
