<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScrapGoogleImagesScrapRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'query' => [
                'required',
            ],
'noi'   => [
                'required',
            ],
];
    }
}
