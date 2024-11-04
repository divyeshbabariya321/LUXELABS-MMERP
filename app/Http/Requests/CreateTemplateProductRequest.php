<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTemplateProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'template_no'      => [
                'required',
            ],
'product_media_id' => [
                'required',
            ],
'background'       => [
                'required',
            ],
'text'             => [
                'required',
            ],
];
    }
}
