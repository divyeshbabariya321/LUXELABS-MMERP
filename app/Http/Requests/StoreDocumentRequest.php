<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'user_id'     => [
                'required',
                'numeric',
            ],
'name'        => [
                'required',
                'string',
                'max:255',
            ],
'file'        => [
                'required',
            ],
'category_id' => [
                'required',
            ],
'version'     => [
                'required',
            ],
];
    }
}
