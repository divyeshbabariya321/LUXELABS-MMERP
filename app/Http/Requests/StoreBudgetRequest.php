<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBudgetRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'description'           => [
                'sometimes',
                'nullable',
                'string',
            ],
'date'                  => [
                'required',
            ],
'amount'                => [
                'required',
                'numeric',
            ],
'type'                  => [
                'required',
                'string',
            ],
'budget_category_id'    => [
                'required',
                'numeric',
            ],
'budget_subcategory_id' => [
                'required',
                'numeric',
            ],
];
    }
}
