<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKeywordassignRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'keyword'          => [
                'required',
            ],
'task_category'    => [
                'required',
            ],
'task_description' => [
                'required',
            ],
'assign_to'        => [
                'required',
            ],
];
    }
}
