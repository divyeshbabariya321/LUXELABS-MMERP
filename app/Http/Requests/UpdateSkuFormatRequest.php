<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSkuFormatRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'category_id' => [
                'required',
            ],
'brand_id'    => [
                'required',
            ],
];
    }
}
