<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetRecordNodeScrapperCategoryMapRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return ['category_stack' => [
                'required',
                'array',
            ],];
    }
}
