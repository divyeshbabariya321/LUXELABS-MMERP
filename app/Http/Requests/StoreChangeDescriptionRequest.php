<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChangeDescriptionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'keyword'      => [
                'required',
            ],
'replace_with' => [
                'required',
            ],
];
    }
}
