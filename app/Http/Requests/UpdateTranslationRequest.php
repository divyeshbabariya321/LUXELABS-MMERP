<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTranslationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'from'          => [
                'required',
            ],
'to'            => [
                'required',
            ],
'text_original' => [
                'required',
            ],
'text'          => [
                'required',
            ],
];
    }
}
