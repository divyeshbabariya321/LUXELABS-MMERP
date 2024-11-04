<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubcategoryStoreReplyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'name'      => [
                'required',
                'string',
            ],
'parent_id' => [
                'required',
            ],
];
    }
}
