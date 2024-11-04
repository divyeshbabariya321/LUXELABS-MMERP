<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubCategoryStoreBudgetRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'parent_id'   => [
                'required',
                'integer',
            ],
'subcategory' => [
                'required',
                'string',
                'max:255',
            ],
];
    }
}
