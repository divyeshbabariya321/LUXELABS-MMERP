<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKeywordInstructionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'keywords'             => [
                'required',
                'array',
            ],
'instruction_category' => [
                'required',
            ],
];
    }
}
