<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddCategoryCategoryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'title'       => [
                'required',
            ],
'magento_id'  => [
                'required',
                'numeric',
            ],
'show_all_id' => [
                'numeric',
                'nullable',
            ],
];
    }
}
