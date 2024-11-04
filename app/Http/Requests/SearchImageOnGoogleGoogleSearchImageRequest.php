<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchImageOnGoogleGoogleSearchImageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'media_id'   => [
                'required',
            ],
'product_id' => [
                'required',
            ],
];
    }
}
