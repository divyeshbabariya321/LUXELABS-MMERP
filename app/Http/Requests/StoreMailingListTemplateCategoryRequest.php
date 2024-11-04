<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMailingListTemplateCategoryRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'unique:mailinglist_template_categories,title',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Category name required.',
            'name.unique'   => 'Category name already exists.',
        ];
    }
}
