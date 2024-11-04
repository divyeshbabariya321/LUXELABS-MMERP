<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttachOrDetachReviewsSitejabberQARequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'action'         => [
                'required',
            ],
'reviewTemplate' => [
                'required',
                'array',
            ],
];
    }
}
